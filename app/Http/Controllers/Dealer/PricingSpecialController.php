<?php

namespace App\Http\Controllers\Dealer;

use App\Actions\Inventory\DeletePricingSpecialAction;
use App\Actions\Inventory\StorePricingSpecialAction;
use App\Actions\Inventory\UpdatePricingSpecialAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePricingSpecialRequest;
use App\Http\Requests\Inventory\UpdatePricingSpecialRequest;
use App\Models\Catalog\Color;
use App\Models\Catalog\Make;
use App\Models\Inventory\PricingSpecial;
use App\Models\Inventory\Vehicle;
use App\Models\Catalog\BodyStyle;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingSpecialController extends Controller
{
    public function __construct(
        private readonly StorePricingSpecialAction  $storePricingSpecial,
        private readonly UpdatePricingSpecialAction $updatePricingSpecial,
        private readonly DeletePricingSpecialAction $deletePricingSpecial,
    ) {}

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;

        $query = PricingSpecial::forDealer($dealerId)
            ->with([
                'make:id,name',
                'makeModel:id,name',
                'exteriorColor:id,name',
            ])
            ->orderByDesc('priority')
            ->orderByDesc('id');

        if ($request->filled('filter_type')) {
            $query->where('type', $request->filter_type);
        }

        if ($request->filled('filter_condition')) {
            $query->where('condition', $request->filter_condition);
        }

        if ($request->filled('filter_certified')) {
            $query->where('is_certified', (bool) $request->filter_certified);
        }

        if ($request->filled('filter_year_from')) {
            $query->where('year', '>=', $request->filter_year_from);
        }

        if ($request->filled('filter_year_to')) {
            $query->where('year', '<=', $request->filter_year_to);
        }

        $pricingSpecials = $query->get();
        $dealer          = $request->user()->currentDealer;

        // Modal dropdown data
        $makes        = Make::orderBy('name')->get(['id', 'name']);
        $colors       = Color::orderBy('name')->get(['id', 'name']);
        $bodyStyles   = BodyStyle::orderBy('name')->get(['id', 'name']);
        $stockNumbers = Vehicle::forDealer($dealerId)
                            ->active()
                            ->orderBy('stock_number')
                            ->pluck('stock_number');

        return view('dealer.pages.inventory.pricing-specials', compact(
            'pricingSpecials', 'dealer', 'makes', 'colors', 'stockNumbers', 'bodyStyles'
        ));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(StorePricingSpecialRequest $request): JsonResponse
    {
        $dealer         = $request->user()->currentDealer;
        $data           = $this->prepareBooleans($request->validated());
        $pricingSpecial = ($this->storePricingSpecial)($dealer, $data);

        AuditLogger::info($request, 'Pricing special created', [
            'pricing_special_id' => $pricingSpecial->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pricing special created successfully.',
        ]);
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(UpdatePricingSpecialRequest $request, PricingSpecial $pricingSpecial): JsonResponse
    {
        $this->authorizePricingSpecial($request, $pricingSpecial);

        ($this->updatePricingSpecial)(
            $pricingSpecial,
            $this->prepareBooleans($request->validated())
        );

        AuditLogger::info($request, 'Pricing special updated', [
            'pricing_special_id' => $pricingSpecial->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pricing special updated successfully.',
        ]);
    }

    public function toggle(PricingSpecial $pricingSpecial, Request $request): JsonResponse
    {
        $this->authorizePricingSpecial($request, $pricingSpecial);

        $pricingSpecial->update(['is_enabled' => ! $pricingSpecial->is_enabled]);

        AuditLogger::info($request, 'Pricing special toggled', [
            'pricing_special_id' => $pricingSpecial->id,
            'is_enabled'         => $pricingSpecial->is_enabled,
        ]);

        return response()->json([
            'is_enabled' => $pricingSpecial->is_enabled,
        ]);
    }

    public function duplicate(PricingSpecial $pricingSpecial, Request $request): JsonResponse
    {
        $this->authorizePricingSpecial($request, $pricingSpecial);

        $new = $pricingSpecial->replicate();
        $new->title      = $pricingSpecial->title . ' (Copy)';
        $new->is_enabled = false;
        $new->save();

        AuditLogger::info($request, 'Pricing special duplicated', [
            'original_id' => $pricingSpecial->id,
            'new_id'      => $new->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pricing special duplicated.',
        ]);
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Request $request, PricingSpecial $pricingSpecial): JsonResponse
    {
        $this->authorizePricingSpecial($request, $pricingSpecial);

        ($this->deletePricingSpecial)($pricingSpecial);

        AuditLogger::warning($request, 'Pricing special deleted', [
            'pricing_special_id' => $pricingSpecial->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pricing special deleted successfully.',
        ]);
    }

    // ─── Match Count (AJAX) ───────────────────────────────────────────────────

    public function matchCount(Request $request): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        $query = Vehicle::forDealer($dealerId);

        if ($request->filled('condition')) {
            if ($request->condition === 'Pre-owned') {
                $query->whereIn('vehicle_condition', ['Used', 'Certified Pre-Owned']);
            } else {
                $query->where('vehicle_condition', $request->condition);
            }
        }

        if ($request->filled('is_certified') && $request->is_certified !== '') {
            $query->where('is_certified', (bool) $request->is_certified);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('make_id')) {
            $query->where('make_id', $request->make_id);
        }

        if ($request->filled('make_model_id')) {
            $query->where('make_model_id', $request->make_model_id);
        }

        if ($request->filled('model_number')) {
            $query->where('model_number', $request->model_number);
        }

        if ($request->filled('body_style')) {
            $query->whereHas('bodyStyle', fn ($q) => $q->where('name', $request->body_style));
        }

        if ($request->filled('trim')) {
            $query->where('trim', 'like', '%' . $request->trim . '%');
        }

        if ($request->filled('exterior_color_id')) {
            $query->where('exterior_color_id', $request->exterior_color_id);
        }

        if ($request->filled('stock_number')) {
            $query->where('stock_number', $request->stock_number);
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('tag', $request->tag));
        }

        if ($request->filled('min_days')) {
            $query->where('listed_at', '<=', now()->subDays((int) $request->min_days));
        }

        if ($request->filled('max_days')) {
            $query->where('listed_at', '>=', now()->subDays((int) $request->max_days));
        }

        return response()->json(['count' => $query->count()]);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function authorizePricingSpecial(Request $request, PricingSpecial $pricingSpecial): void
    {
        abort_if($pricingSpecial->dealer_id !== $request->user()->current_dealer_id, 403);
    }

    private function prepareBooleans(array $data): array
    {
        $data['stackable']  = (bool) ($data['stackable']  ?? false);
        $data['send_email'] = (bool) ($data['send_email'] ?? false);
        $data['hide_price'] = (bool) ($data['hide_price'] ?? false);

        if (isset($data['is_certified']) && $data['is_certified'] !== null && $data['is_certified'] !== '') {
            $data['is_certified'] = (bool) $data['is_certified'];
        } else {
            $data['is_certified'] = null;
        }

        return $data;
    }
}