<?php

namespace App\Services\Inventory;

use App\Models\Catalog\Feature;
use App\Models\Inventory\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InventoryListingService
{
    private const PER_PAGE = 24;

    // ── SRP columns only — no heavy payload ───────────────────────────────────
    // Slug accessor needs: id, year, make_id, make_model_id, trim, vin
    // Card needs: stock_number, mileage, list_price, original_price,
    //             is_spotlight, featured, photos_count
    // Sort needs: featured, listed_at
    private const VEHICLE_COLUMNS = [
        'id',
        'dealer_id',
        'year',
        'make_id',
        'make_model_id',
        'trim',
        'vin',
        'stock_number',
        'mileage',
        'list_price',
        'original_price',
        'is_spotlight',
        'featured',
        'listed_at',
        // ── Pricing matching ──
        'vehicle_condition',
        'is_certified',
        'body_type_id',
        'body_style_id',
        'exterior_color_id',
        'model_number',
    ];

    // ── Invokable ─────────────────────────────────────────────────────────────
    public function __invoke(
        Request $request,
        int $dealerId,
        array $bodyTypeNames = []
    ): array {
        $query = $this->buildQuery($request, $dealerId, $bodyTypeNames);

        // paginate() runs COUNT internally — reuse its total, avoid double query
        $vehicles = $query->paginate(self::PER_PAGE)->withQueryString();
        $total    = $vehicles->total();

        $filterData = $this->buildFilterData($dealerId, $bodyTypeNames);

        return compact('vehicles', 'filterData', 'total');
    }

    // ── Build main vehicle query with all active filters ──────────────────────
    private function buildQuery(
        Request $request,
        int $dealerId,
        array $bodyTypeNames
    ): Builder {
        $query = Vehicle::forDealer($dealerId)
            ->active()
            ->select(self::VEHICLE_COLUMNS)
            ->withCount('photos')
            ->with([
                'make:id,name',
                'makeModel:id,name',
                'exteriorColor:id,name',
                'primaryPhoto' => fn ($q) => $q
                    ->select(['id', 'vehicle_id', 'path', 'disk', 'url'])
                    ->live(),
                'photos' => fn ($q) => $q
                    ->select(['id', 'vehicle_id', 'path', 'disk', 'url', 'sort_order', 'is_primary'])
                    ->live()
                    ->orderBy('sort_order')
                    ->limit(3),
                'prices:vehicle_id,special_price,msrp',
            ]);

        // ── Body type scope (for /cars, /trucks, etc.) ────────────────────────
        if (! empty($bodyTypeNames)) {
            $query->whereHas('bodyType', fn (Builder $q) => $q->whereIn('name', $bodyTypeNames));
        }

        // ── Search ────────────────────────────────────────────────────────────
        if ($search = trim((string) $request->input('search', ''))) {
            // Split into individual words — each word must match at least one field
            // This allows "2022 Chevrolet Colorado" to match across multiple columns
            $terms = array_values(array_filter(explode(' ', $search)));

            foreach ($terms as $term) {
                $query->where(function (Builder $q) use ($term) {
                    $q->whereHas('make',        fn (Builder $m) => $m->where('name',  'like', "%{$term}%"))
                      ->orWhereHas('makeModel', fn (Builder $m) => $m->where('name',  'like', "%{$term}%"))
                      ->orWhereHas('bodyType',  fn (Builder $m) => $m->where('name',  'like', "%{$term}%"))
                      ->orWhereHas('bodyStyle', fn (Builder $m) => $m->where('name',  'like', "%{$term}%"))
                      ->orWhereHas('factoryOptions', fn (Builder $m) => $m->where('label', 'like', "%{$term}%"))
                      ->orWhere('trim',          'like', "%{$term}%")
                      ->orWhere('stock_number',  'like', "%{$term}%")
                      ->orWhere('vin',           'like', "%{$term}%")
                      ->orWhereRaw('CAST(`year` AS CHAR) LIKE ?', ["%{$term}%"]);
                });
            }
        }

        // ── Make ─────────────────────────────────────────────────────────────
        if ($makes = $request->input('make')) {
            $query->whereHas('make', fn (Builder $q) => $q->whereIn('name', (array) $makes));
        }

        // ── Model ─────────────────────────────────────────────────────────────
        if ($models = $request->input('model')) {
            $query->whereHas('makeModel', fn (Builder $q) => $q->whereIn('name', (array) $models));
        }

        // ── Year ──────────────────────────────────────────────────────────────
        $query->when($request->input('year.gt'), fn (Builder $q, $v) => $q->where('year', '>=', $v))
              ->when($request->input('year.lt'), fn (Builder $q, $v) => $q->where('year', '<=', $v));

        // ── Mileage ───────────────────────────────────────────────────────────
        if ($mileageMax = $request->input('mileage.lt')) {
            $mileageMax === 'Over 100000'
                ? $query->where('mileage', '>', 100000)
                : $query->where('mileage', '<=', (int) $mileageMax);
        }

        // ── Price ─────────────────────────────────────────────────────────────
        $query->when($request->input('price.gt'), fn (Builder $q, $v) => $q->where('list_price', '>=', (int) $v))
              ->when($request->input('price.lt'), fn (Builder $q, $v) => $q->where('list_price', '<=', (int) $v));

        // ── Body Style ────────────────────────────────────────────────────────
        if ($bodyStyles = $request->input('body_style')) {
            $query->whereHas('bodyStyle', fn (Builder $q) => $q->whereIn('name', (array) $bodyStyles));
        }

        // ── Body Type (URL param: ?body_type[]=SUV) ───────────────────────────
        if ($bodyTypes = $request->input('body_type')) {
            $query->whereHas('bodyType', fn (Builder $q) => $q->whereIn('name', (array) $bodyTypes));
        }

        // ── Exterior Color ────────────────────────────────────────────────────
        if ($colors = $request->input('exterior_color')) {
            $query->whereHas('exteriorColor', fn (Builder $q) => $q->whereIn('name', (array) $colors));
        }

        // ── Interior Color ────────────────────────────────────────────────────
        if ($interiorColors = $request->input('interior_color')) {
            $query->whereHas('interiorColor', fn (Builder $q) => $q->whereIn('name', (array) $interiorColors));
        }

        // ── Transmission ──────────────────────────────────────────────────────
        if ($transmissions = $request->input('transmission')) {
            $query->whereHas('transmissionType', fn (Builder $q) => $q->whereIn('name', (array) $transmissions));
        }

        // ── Drivetrain ────────────────────────────────────────────────────────
        if ($drivetrains = $request->input('drivetrain')) {
            $query->whereHas('drivetrainType', fn (Builder $q) => $q->whereIn('name', (array) $drivetrains));
        }

        // ── Fuel Type ─────────────────────────────────────────────────────────
        if ($fuelTypes = $request->input('fuel_type')) {
            $query->whereHas('fuelType', fn (Builder $q) => $q->whereIn('name', (array) $fuelTypes));
        }

        // ── Engine ────────────────────────────────────────────────────────────
        if ($engines = $request->input('engine')) {
            $query->whereIn('engine', (array) $engines);
        }

        // ── Features (AND logic — numeric = ID, string = name) in future ────────────────
        // if ($featureValues = $request->input('feature')) {
        //     foreach ((array) $featureValues as $value) {
        //         $query->whereHas('features', function (Builder $q) use ($value) {
        //             is_numeric($value)
        //                 ? $q->where('features.id', (int) $value)
        //                 : $q->where('features.name', $value);
        //         });
        //     }
        // }

        // ── Features — MVP: factory_options label se match ────────────────────
        if ($featureValues = $request->input('feature')) {
            foreach ((array) $featureValues as $value) {
                $query->whereHas('factoryOptions', function (Builder $q) use ($value) {
                    $q->where('label', 'like', "%{$value}%");
                });
            }
        }

        // ── Seating ───────────────────────────────────────────────────────────
        if ($seating = $request->input('seating')) {
            $query->whereIn('seating_capacity', (array) $seating);
        }

        // ── Sort ──────────────────────────────────────────────────────────────
        match ($request->input('sort', 'best_match')) {
            'price_asc'    => $query->orderBy('list_price'),
            'price_desc'   => $query->orderByDesc('list_price'),
            'newest'       => $query->orderByDesc('listed_at'),
            'mileage_asc'  => $query->orderBy('mileage'),
            'mileage_desc' => $query->orderByDesc('mileage'),
            'year_desc'    => $query->orderByDesc('year'),
            'year_asc'     => $query->orderBy('year'),
            default        => $query->orderByDesc('featured')->orderByDesc('listed_at'),
        };

        return $query;
    }

    // ── Build filter sidebar data — cached per dealer + body type combo ────────
    // Version-based invalidation — when vehicle changes, version increments,
    // old cache keys become stale without needing wildcard deletes
    private function buildFilterData(int $dealerId, array $bodyTypeNames): array
    {
        $version  = Cache::get("inv_filters_v:{$dealerId}", 1);
        $bodyKey  = empty($bodyTypeNames) ? 'all' : md5(implode(',', $bodyTypeNames));
        $cacheKey = "inv_filters:{$dealerId}:{$version}:{$bodyKey}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($dealerId, $bodyTypeNames, $cacheKey) {

            $trackingKey = "inv_filters_keys:{$dealerId}";
            $keys = Cache::get($trackingKey, []);

            $keys[] = $cacheKey;

            Cache::put($trackingKey, array_slice(array_unique($keys), -100), now()->addHours(2));

            return $this->runFilterQueries($dealerId, $bodyTypeNames);
        });
    }

    // ── Cache version invalidation — call this from VehicleObserver ──────────
    // Stale old keys without wildcard delete — works on any cache driver
    public function invalidateFilterCache(int $dealerId): void
    {
        $trackingKey = "inv_filters_keys:{$dealerId}";
        $keys = Cache::get($trackingKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($trackingKey);

        Cache::increment("inv_filters_v:{$dealerId}");
    }

    // ── Run all filter group queries ──────────────────────────────────────────
    // Pure Eloquent — groupBy FK + eager load — no raw JOINs, no ambiguous IDs
    private function runFilterQueries(int $dealerId, array $bodyTypeNames): array
    {
        // Fresh Builder factory — closure returns new instance on each call
        $base = fn (): Builder => Vehicle::forDealer($dealerId)
            ->active()
            ->when(
                ! empty($bodyTypeNames),
                fn (Builder $q) => $q->whereHas(
                    'bodyType',
                    fn (Builder $bt) => $bt->whereIn('name', $bodyTypeNames)
                )
            );

        // ── Makes + Models — ONE query, derive makes from model rows ──────────
        // Eliminates the duplicate makes eager load that was happening before
        $makeModelRows = $base()
            ->select('make_id', 'make_model_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('make_id')
            ->whereNotNull('make_model_id')
            ->groupBy('make_id', 'make_model_id')
            ->with('make:id,name', 'makeModel:id,name')
            ->get();

        // Derive makes by summing model counts per make — no separate query
        $makes = $makeModelRows
            ->groupBy('make_id')
            ->map(fn ($group) => (object) [
                'make_name' => $group->first()->make?->name,
                'cnt'       => $group->sum('cnt'),
            ])
            ->filter(fn ($v) => $v->make_name)
            ->sortBy('make_name')
            ->values();

        // Models grouped by make name — same rows, no extra query
        $models = $makeModelRows
            ->map(fn ($v) => (object) [
                'make_name'  => $v->make?->name,
                'model_name' => $v->makeModel?->name,
                'cnt'        => $v->cnt,
            ])
            ->filter(fn ($v) => $v->make_name && $v->model_name)
            ->sortBy('model_name')
            ->groupBy('make_name');

        // ── Body Styles ───────────────────────────────────────────────────────
        $bodyStyles = $base()
            ->select('body_style_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('body_style_id')
            ->groupBy('body_style_id')
            ->with('bodyStyle:id,name')
            ->get()
            ->map(fn ($v) => (object) ['style_name' => $v->bodyStyle?->name, 'cnt' => $v->cnt])
            ->filter(fn ($v) => $v->style_name)
            ->sortBy('style_name')
            ->values();

        // ── Exterior Colors ───────────────────────────────────────────────────
        $colors = $base()
            ->select('exterior_color_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('exterior_color_id')
            ->groupBy('exterior_color_id')
            ->with('exteriorColor:id,name')
            ->get()
            ->map(fn ($v) => (object) ['color_name' => $v->exteriorColor?->name, 'cnt' => $v->cnt])
            ->filter(fn ($v) => $v->color_name)
            ->sortBy('color_name')
            ->values();

        // ── Interior Colors ───────────────────────────────────────────────────
        $interiorColors = $base()
            ->select('interior_color_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('interior_color_id')
            ->groupBy('interior_color_id')
            ->with('interiorColor:id,name')
            ->get()
            ->map(fn ($v) => (object) ['color_name' => $v->interiorColor?->name, 'cnt' => $v->cnt])
            ->filter(fn ($v) => $v->color_name)
            ->sortBy('color_name')
            ->values();

        // ── Transmissions ─────────────────────────────────────────────────────
        $transmissions = $base()
            ->select('transmission_type_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('transmission_type_id')
            ->groupBy('transmission_type_id')
            ->with('transmissionType:id,name')
            ->get()
            ->map(fn ($v) => (object) ['name' => $v->transmissionType?->name, 'cnt' => $v->cnt])
            ->filter(fn ($v) => $v->name)
            ->sortBy('name')
            ->values();

        // ── Drivetrains ───────────────────────────────────────────────────────
        $drivetrains = $base()
            ->select('drivetrain_type_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('drivetrain_type_id')
            ->groupBy('drivetrain_type_id')
            ->with('drivetrainType:id,name')
            ->get()
            ->map(fn ($v) => (object) ['name' => $v->drivetrainType?->name, 'cnt' => $v->cnt])
            ->filter(fn ($v) => $v->name)
            ->sortBy('name')
            ->values();

        // ── Fuel Types ────────────────────────────────────────────────────────
        $fuelTypes = $base()
            ->select('fuel_type_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('fuel_type_id')
            ->groupBy('fuel_type_id')
            ->with('fuelType:id,name')
            ->get()
            ->map(fn ($v) => (object) ['name' => $v->fuelType?->name, 'cnt' => $v->cnt])
            ->filter(fn ($v) => $v->name)
            ->sortBy('name')
            ->values();

        // ── Engines ───────────────────────────────────────────────────────────
        $engines = $base()
            ->select('engine', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('engine')
            ->where('engine', '!=', '')
            ->groupBy('engine')
            ->orderBy('engine')
            ->get();

        // ── Seating Capacity ──────────────────────────────────────────────────
        $seating = $base()
            ->select('seating_capacity', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('seating_capacity')
            ->groupBy('seating_capacity')
            ->orderBy('seating_capacity')
            ->get();

        // ── Price Range ───────────────────────────────────────────────────────
        $priceRange = $base()
            ->whereNotNull('list_price')
            ->selectRaw('MIN(list_price) as min_price, MAX(list_price) as max_price')
            ->first();

        // ── Year Range ────────────────────────────────────────────────────────
        $yearRange = $base()
            ->selectRaw('MIN(year) as min_year, MAX(year) as max_year')
            ->first();

        // ── Features (via Feature model — cleaner reverse relation) ───────────
        $features = Feature::whereHas('vehicles', function (Builder $q) use ($dealerId, $bodyTypeNames) {
                $q->forDealer($dealerId)->active();
                if (! empty($bodyTypeNames)) {
                    $q->whereHas('bodyType', fn (Builder $bt) => $bt->whereIn('name', $bodyTypeNames));
                }
            })
            ->withCount(['vehicles' => function (Builder $q) use ($dealerId, $bodyTypeNames) {
                $q->forDealer($dealerId)->active();
                if (! empty($bodyTypeNames)) {
                    $q->whereHas('bodyType', fn (Builder $bt) => $bt->whereIn('name', $bodyTypeNames));
                }
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'vehicles_count']);

        return compact(
            'makes', 'models', 'bodyStyles',
            'colors', 'interiorColors',
            'transmissions', 'drivetrains', 'fuelTypes',
            'engines', 'seating',
            'features', 'priceRange', 'yearRange'
        );
    }
}
