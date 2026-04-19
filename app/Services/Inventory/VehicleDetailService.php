<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Vehicle;
use Illuminate\Support\Collection;

final class VehicleDetailService
{
    private const PHOTO_COLUMNS = ['id', 'vehicle_id', 'path', 'disk', 'url', 'sort_order', 'is_primary'];

    private const RELATED_COLUMNS = [
        'id', 'dealer_id', 'year', 'make_id', 'make_model_id', 'trim', 'vin',
        'stock_number', 'mileage', 'list_price', 'original_price',
        'is_spotlight', 'featured', 'listed_at',
        'vehicle_condition', 'is_certified', 'body_style_id',
        'exterior_color_id', 'model_number',
    ];

    private const VEHICLE_COLUMNS = [
        'id', 'dealer_id', 'year', 'make_id', 'make_model_id', 'trim', 'vin',
        'stock_number', 'mileage', 'list_price', 'original_price',
        'is_spotlight', 'featured', 'listed_at', 'status',
        'body_type_id', 'body_style_id', 'fuel_type_id', 'transmission_type_id',
        'drivetrain_type_id', 'exterior_color_id',
        'engine', 'seating_capacity',
        // ── Pricing matching ──
        'vehicle_condition', 'is_certified', 'model_number',
    ];

    // ─── Public API ───────────────────────────────────────────────────────────

    public function loadVehicle(string $slug, int $dealerId): Vehicle
    {
        preg_match('/([a-z0-9]{17})-in-/i', $slug, $m);

        abort_if(empty($m[1]), 404);

        return Vehicle::forDealer($dealerId)
            ->active()
            ->whereRaw('UPPER(vin) = ?', [strtoupper($m[1])])
            ->select(self::VEHICLE_COLUMNS)
            ->with([
                'make:id,name',
                'makeModel:id,name',
                'dealer:id,name,phone',
                'bodyType:id,name',
                'fuelType:id,name',
                'transmissionType:id,name',
                'drivetrainType:id,name',
                'exteriorColor:id,name',
                'specs',
                'primaryPhoto' => fn ($q) => $q
                    ->select(['id', 'vehicle_id', 'path', 'disk', 'url'])
                    ->live(),
                'photos' => fn ($q) => $q
                    ->select(self::PHOTO_COLUMNS)
                    ->live()
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order'),
                'features:id,name',
                'factoryOptions' => fn ($q) => $q
                    ->select([
                        'factory_options.id',
                        'factory_options.label',
                        'factory_options.category_id',
                    ])
                    ->with('category:id,name'),
                'prices:vehicle_id,special_price,msrp',
                'printables',
            ])
            ->withCount(['photos' => fn ($q) => $q->live()])
            ->firstOrFail();
    }

    public function relatedVehicles(Vehicle $vehicle, int $dealerId): Collection
    {
        $related = Vehicle::forDealer($dealerId)
            ->active()
            ->where('id', '!=', $vehicle->id)
            ->where('make_id', $vehicle->make_id)
            ->select(self::RELATED_COLUMNS)
            ->with($this->relatedWith())
            ->withCount(['photos' => fn ($q) => $q->live()])
            ->orderByDesc('listed_at')
            ->limit(8)
            ->get();

        if ($related->count() < 4) {
            $existingIds = $related->pluck('id')->push($vehicle->id);

            $fallback = Vehicle::forDealer($dealerId)
                ->active()
                ->whereNotIn('id', $existingIds)
                ->select(self::RELATED_COLUMNS)
                ->with($this->relatedWith())
                ->withCount(['photos' => fn ($q) => $q->live()])
                ->orderByDesc('listed_at')
                ->limit(8 - $related->count())
                ->get();

            $related = $related->concat($fallback);
        }

        return $related;
    }

    public function estimatedMonthly(
        float $price,
        float $annualRatePercent = 6.79,
        int   $months = 60,
    ): float {
        if ($price <= 0) {
            return 0.0;
        }

        $monthlyRate = ($annualRatePercent / 100) / 12;

        if ($monthlyRate === 0.0) {
            return $price / $months;
        }

        return ($price * $monthlyRate) / (1 - pow(1 + $monthlyRate, -$months));
    }

    public function groupedFactoryOptions(Vehicle $vehicle): Collection
    {
        return $vehicle->factoryOptions
            ->filter(fn ($fo) => $fo->category !== null)
            ->groupBy(fn ($fo) => $fo->category->name);
    }

    public function buildFaqs(Vehicle $vehicle): array
    {
        $spec  = $vehicle->specs;
        $title = trim(strtoupper(
            $vehicle->year . ' ' .
            ($vehicle->make?->name ?? '') . ' ' .
            ($vehicle->makeModel?->name ?? '') .
            ($vehicle->trim ? ' ' . $vehicle->trim : '')
        ));

        $faqs = [];

        $fuelType = $vehicle->fuelType?->name;
        if ($fuelType) {
            $faqs[] = [
                'question' => "What type of fuel does a {$title} use?",
                'answer'   => "A {$title} uses {$fuelType}.",
            ];
        }

        if ($spec?->fuel_capacity) {
            $faqs[] = [
                'question' => "What is the {$title} fuel capacity?",
                'answer'   => "A {$title} has the fuel capacity of {$spec->fuel_capacity} gallons.",
            ];
        }

        if ($spec?->horsepower) {
            $faqs[] = [
                'question' => "How much horsepower does a {$title} have?",
                'answer'   => "A {$title} has {$spec->horsepower} horsepower.",
            ];
        }

        if ($spec?->torque) {
            $faqs[] = [
                'question' => "How much torque does a {$title} have?",
                'answer'   => "A {$title} has {$spec->torque} lb-ft torque.",
            ];
        }

        if ($spec?->mpg_city) {
            $faqs[] = [
                'question' => "What is the {$title} city fuel economy / miles per gallon?",
                'answer'   => "A {$title} has a city fuel economy of {$spec->mpg_city} mpg.",
            ];
        }

        return $faqs;
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function relatedWith(): array
    {
        return [
            'make:id,name',
            'makeModel:id,name',
            'primaryPhoto' => fn ($q) => $q
                ->select(['id', 'vehicle_id', 'path', 'disk', 'url'])
                ->live(),
            'photos' => fn ($q) => $q
                ->select(self::PHOTO_COLUMNS)
                ->live()
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->limit(3),
            'prices:vehicle_id,special_price,msrp',
        ];
    }
}