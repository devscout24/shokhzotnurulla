<?php

namespace App\Services\Inventory;

use App\Models\Catalog\BodyStyle;
use App\Models\Inventory\PricingSpecial;
use App\Models\Inventory\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class PricingCalculatorService
{
    private const ANNUAL_RATE = 6.79;
    private const TERM_MONTHS = 60;

    // ─── Load all active specials for dealer ──────────────────────────────────

    public function getActiveSpecials(int $dealerId): Collection
    {
        return PricingSpecial::forDealer($dealerId)
            ->enabled()
            ->active()
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->get();
    }

    // ─── Body style name → id map (cached 6h) ────────────────────────────────

    public function getBodyStyleMap(): Collection
    {
        return Cache::remember('body_style_name_map', now()->addHours(6), function () {
            return BodyStyle::pluck('id', 'name');
        });
    }

    // ─── Main: compute pricing result for one vehicle ─────────────────────────

    public function calculate(
        Vehicle $vehicle,
        Collection $specials,
        Collection $bodyStyleMap,
    ): array {
        $listPrice    = (float) ($vehicle->list_price ?? 0);
        $msrp         = $vehicle->relationLoaded('prices')
            ? (float) ($vehicle->prices?->msrp ?? 0)
            : 0.0;
        $specialPrice = $vehicle->relationLoaded('prices')
            ? (float) ($vehicle->prices?->special_price ?? 0)
            : 0.0;

        // ── Priority 1 — manual special_price per vehicle ─────────────────────
        if ($specialPrice > 0) {
            $wasPrice = $msrp > $specialPrice
                ? $msrp
                : ($listPrice > $specialPrice ? $listPrice : 0.0);

            return $this->buildResult(
                finalPrice:      $specialPrice,
                displayOriginal: $wasPrice,
                savings:         max(0.0, $wasPrice - $specialPrice),
                msrp:            $msrp,
                appliedSpecial:  null,
                isFormfill:      false,
            );
        }

        // ── Priority 2 — best matching pricing special ────────────────────────
        $matching = $this->findMatching($vehicle, $specials, $bodyStyleMap);

        if ($matching->isNotEmpty()) {
            $best       = $matching->first();
            $finalPrice = $this->applyDiscount($listPrice, $msrp, $best);

            $isOffset   = in_array($best->discount_type, ['offsetdollar', 'offsetincrease'], true);
            $isIncrease = in_array($best->discount_type, ['increase', 'offsetincrease'], true);

            // Offset types = MSRP base, baaki sab = list_price base
            $wasPrice = $isOffset
                ? ($msrp > 0 ? $msrp : $listPrice)
                : $listPrice;

            $savings = $isIncrease ? 0.0 : max(0.0, $wasPrice - $finalPrice);

            return $this->buildResult(
                finalPrice:      $finalPrice,
                displayOriginal: (! $isIncrease && $wasPrice > $finalPrice) ? $wasPrice : 0.0,
                savings:         $savings,
                msrp:            $msrp,
                appliedSpecial:  $best,
                isFormfill:      $best->type === 'formfill',
            );
        }

        // ── Priority 3 — list price fallback ─────────────────────────────────
        // original_price column is never populated from dealer panel — ignore it
        // Was price = MSRP only if higher than list_price
        $wasPrice = $msrp > $listPrice ? $msrp : 0.0;

        return $this->buildResult(
            finalPrice:      $listPrice,
            displayOriginal: $wasPrice,
            savings:         $wasPrice > 0 ? max(0.0, $wasPrice - $listPrice) : 0.0,
            msrp:            $msrp,
            appliedSpecial:  null,
            isFormfill:      false,
        );
    }

    // ─── Find all specials that match vehicle criteria ────────────────────────

    public function findMatching(
        Vehicle $vehicle,
        Collection $specials,
        Collection $bodyStyleMap,
    ): Collection {
        return $specials->filter(
            fn (PricingSpecial $s) => $this->vehicleMatchesSpecial($vehicle, $s, $bodyStyleMap)
        );
    }

    // ─── Estimated monthly payment ────────────────────────────────────────────

    public function estimatedMonthly(float $price, ?PricingSpecial $special = null): float
    {
        if ($price <= 0) {
            return 0.0;
        }

        $rate = ($special?->discount_type === 'special' && $special?->finance_rate)
                ? (float) $special->finance_rate
                : self::ANNUAL_RATE;

        $term = ($special?->discount_type === 'special' && $special?->finance_term)
                ? (int) $special->finance_term
                : self::TERM_MONTHS;

        $r = ($rate / 100) / 12;

        return ($price * $r) / (1 - pow(1 + $r, -$term));
    }

    // ─── Check if vehicle matches a pricing special ───────────────────────────

    private function vehicleMatchesSpecial(
        Vehicle $vehicle,
        PricingSpecial $special,
        Collection $bodyStyleMap,
    ): bool {
        // Condition
        if ($special->condition) {
            $vc = $vehicle->vehicle_condition ?? '';
            if ($special->condition === 'Pre-owned') {
                if (! in_array($vc, ['Used', 'Certified Pre-Owned'], true)) {
                    return false;
                }
            } elseif ($special->condition === 'New' && $vc !== 'New') {
                return false;
            }
        }

        // Certified
        if ($special->is_certified !== null) {
            if ((bool) $special->is_certified !== (bool) ($vehicle->is_certified ?? false)) {
                return false;
            }
        }

        // Year
        if ($special->year && (int) $special->year !== (int) $vehicle->year) {
            return false;
        }

        // Make
        if ($special->make_id && (int) $special->make_id !== (int) $vehicle->make_id) {
            return false;
        }

        // Model
        if ($special->make_model_id
            && (int) $special->make_model_id !== (int) ($vehicle->make_model_id ?? 0)) {
            return false;
        }

        // Trim (partial match)
        if ($special->trim && stripos($vehicle->trim ?? '', $special->trim) === false) {
            return false;
        }

        // Body Style — name → id via map
        if ($special->body_style) {
            $expectedId = $bodyStyleMap->get($special->body_style);
            if (! $expectedId
                || (int) $expectedId !== (int) ($vehicle->body_style_id ?? 0)) {
                return false;
            }
        }

        // Exterior Color
        if ($special->exterior_color_id
            && (int) $special->exterior_color_id !== (int) ($vehicle->exterior_color_id ?? 0)) {
            return false;
        }

        // Stock Number
        if ($special->stock_number && $special->stock_number !== $vehicle->stock_number) {
            return false;
        }

        // Model Number
        if ($special->model_number
            && $special->model_number !== ($vehicle->model_number ?? '')) {
            return false;
        }

        // Min / Max days on lot
        if ($special->min_days !== null || $special->max_days !== null) {
            $dol = $vehicle->days_on_lot;
            if ($special->min_days !== null && $dol < (int) $special->min_days) {
                return false;
            }
            if ($special->max_days !== null && $dol > (int) $special->max_days) {
                return false;
            }
        }

        return true;
    }

    // ─── Apply discount type to list price ───────────────────────────────────

    private function applyDiscount(
        float $listPrice,
        float $msrp,
        PricingSpecial $special,
    ): float {
        $amount = (float) ($special->amount ?? 0);
        $base   = $msrp > 0 ? $msrp : $listPrice; // offset types use MSRP as base

        $final = match ($special->discount_type) {
            'fixed'          => $amount,
            'percentage'     => $listPrice * (1 - $amount / 100),
            'dollars'        => $listPrice - $amount,
            'offsetdollar'   => $base - $amount,
            'offsetincrease' => $base + $amount,
            'increase'       => $listPrice + $amount,
            'special'        => $listPrice,
            default          => $listPrice,
        };

        return max(0.0, round($final, 2));
    }

    // ─── Build pricing result array ───────────────────────────────────────────

    private function buildResult(
        float $finalPrice,
        float $displayOriginal,
        float $savings,
        float $msrp,
        ?PricingSpecial $appliedSpecial,
        bool $isFormfill,
    ): array {
        return [
            'final_price'        => $finalPrice,
            'display_original'   => $displayOriginal > $finalPrice ? $displayOriginal : 0.0,
            'savings'            => $savings,
            'msrp'               => $msrp,
            'has_discount'       => $savings > 0,
            'applied_special'    => $appliedSpecial,
            'is_formfill'        => $isFormfill,
            'is_special_finance' => $appliedSpecial?->discount_type === 'special',
            'monthly'            => $this->estimatedMonthly($finalPrice, $appliedSpecial),
            'button_text'        => $appliedSpecial?->button_text ?: null,
            'price_label'        => $this->resolvePriceLabel($appliedSpecial, $isFormfill),
        ];
    }

    private function resolvePriceLabel(?PricingSpecial $special, bool $isFormfill): string
    {
        if ($isFormfill)                                      return 'e-Price';
        if ($special?->discount_type === 'special')           return 'Special Financing';
        return 'Cash price';
    }
}