<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\DealerInventoryFee;
use App\Models\Inventory\VehiclePrintable;
use App\Services\Inventory\InventoryListingService;
use App\Services\Inventory\PricingCalculatorService;
use App\Services\Inventory\VehicleDetailService;
use App\Services\Website\DealerResolverService;
use App\Http\Requests\Inventory\VinDecodeRequest;
use App\Services\Inventory\VinDecodeService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function __construct(
        private readonly DealerResolverService    $dealerResolver,
        private readonly InventoryListingService  $inventoryListing,
        private readonly VehicleDetailService     $vehicleDetail,
        private readonly PricingCalculatorService $pricingCalculator,
        private readonly VinDecodeService         $vinDecoder,
    ) {}

    // ── Home ──────────────────────────────────────────────────────────────────

    public function home(): View
    {
        $dealerId = $this->dealerResolver->resolve();

        $newArrivals = Vehicle::forDealer($dealerId)
            ->active()
            ->select([
                'id', 'dealer_id', 'year', 'make_id', 'make_model_id', 'trim', 'vin',
                'stock_number', 'mileage', 'list_price', 'original_price',
                'is_spotlight', 'featured', 'listed_at',
                'vehicle_condition', 'is_certified', 'body_style_id',
                'exterior_color_id', 'model_number',
            ])
            ->withCount('photos')
            ->with([
                'make:id,name',
                'makeModel:id,name',
                'primaryPhoto' => fn ($q) => $q
                    ->select(['id', 'vehicle_id', 'path', 'disk', 'url'])
                    ->live(),
                'photos' => fn ($q) => $q
                    ->select(['id', 'vehicle_id', 'path', 'disk', 'url', 'sort_order', 'is_primary'])
                    ->live()
                    ->orderBy('sort_order')
                    ->limit(3),
                'prices:vehicle_id,special_price,msrp',
            ])
            ->orderByDesc('listed_at')
            ->limit(12)
            ->get();

        $this->attachPricing($newArrivals, $dealerId);

        return view('frontend.pages.home', [
            'newArrivals' => $newArrivals,
            'seo'         => [
                'title'       => 'Angel Motors Inc | Used Cars for Sale in Smyrna, TN',
                'description' => 'Angel Motors Inc in Smyrna, TN offers quality pre-owned vehicles at competitive prices.',
                'keywords'    => 'angel motors inc, used cars smyrna tn',
            ],
        ]);
    }

    // ── All Inventory ─────────────────────────────────────────────────────────

    public function inventory(Request $request): View
    {
        $dealerId = $this->dealerResolver->resolve();
        $result   = ($this->inventoryListing)($request, $dealerId);

        $this->attachPricing($result['vehicles'], $dealerId);

        return view('frontend.pages.inventory-listing', $result + [
            'type'              => null,
            'label'             => 'All Inventory',
            'mobileSpacerClass' => 'h-104',
            'filters'           => $this->activeFilters($request),
            'seo'               => [
                'title'       => 'Used Cars for Sale in Smyrna, TN | Angel Motors Inc',
                'description' => 'Browse our full inventory at Angel Motors Inc in Smyrna, TN.',
                'keywords'    => 'used cars smyrna tn, angel motors inventory',
            ],
        ]);
    }

    // ── All Inventory — AJAX ──────────────────────────────────────────────────

    public function inventoryFilter(Request $request): JsonResponse
    {
        $dealerId = $this->dealerResolver->resolve();
        $result   = ($this->inventoryListing)($request, $dealerId);

        $this->attachPricing($result['vehicles'], $dealerId);

        $grid       = view('frontend.partials.inventory-grid', ['vehicles' => $result['vehicles']])->render();
        $pagination = $result['vehicles']->withQueryString()->links()->render();
        $total      = $result['total'];
        $heading    = $total . ' used vehicles for sale in Smyrna, TN';

        return response()->json(compact('grid', 'pagination', 'total', 'heading'));
    }

    // ── Inventory By Type ─────────────────────────────────────────────────────

    public function inventoryByType(Request $request, string $type): View
    {
        $dealerId = $this->dealerResolver->resolve();
        $config   = config("vehicle_types.{$type}");

        abort_if(! $config, 404);

        $result = ($this->inventoryListing)($request, $dealerId, $config['body_types']);

        $this->attachPricing($result['vehicles'], $dealerId);

        return view('frontend.pages.inventory-listing', $result + [
            'type'              => $type,
            'label'             => $config['label'],
            'mobileSpacerClass' => 'h-104',
            'filters'           => $this->activeFilters($request),
            'seo'               => [
                'title'       => $config['title'],
                'description' => $config['description'],
                'keywords'    => $config['keywords'],
            ],
        ]);
    }

    // ── Inventory By Type — AJAX ──────────────────────────────────────────────

    public function inventoryByTypeFilter(Request $request, string $type): JsonResponse
    {
        $dealerId = $this->dealerResolver->resolve();
        $config   = config("vehicle_types.{$type}");

        abort_if(! $config, 404);

        $result = ($this->inventoryListing)($request, $dealerId, $config['body_types']);

        $this->attachPricing($result['vehicles'], $dealerId);

        $grid       = view('frontend.partials.inventory-grid', ['vehicles' => $result['vehicles']])->render();
        $pagination = $result['vehicles']->withQueryString()->links()->render();
        $total      = $result['total'];
        $heading    = $total . ' used ' . $config['heading_label'] . ' for sale in Smyrna, TN';

        return response()->json(compact('grid', 'pagination', 'total', 'heading'));
    }

    // ── Inventory Detail ──────────────────────────────────────────────────────

    public function inventoryDetail(Request $request, string $slug): View
    {
        $dealerId = $this->dealerResolver->resolve();
        $vehicle  = $this->vehicleDetail->loadVehicle($slug, $dealerId);

        $applicableFees = DealerInventoryFee::where('dealer_id', $dealerId)
            ->where(function ($q) use ($vehicle) {
                $q->where('condition', 'any')
                  ->orWhere(function ($q2) use ($vehicle) {
                      $vc = $vehicle->vehicle_condition ?? '';
                      match (true) {
                          $vc === 'New'                  => $q2->where('condition', 'new'),
                          $vc === 'Certified Pre-Owned'  => $q2->whereIn('condition', ['used', 'cpo']),
                          default                        => $q2->where('condition', 'used'),
                      };
                  });
            })
            ->orderBy('sort_order')
            ->get();

        // ── Pricing ───────────────────────────────────────────────────────────
        $specials      = $this->pricingCalculator->getActiveSpecials($dealerId);
        $bodyStyleMap  = $this->pricingCalculator->getBodyStyleMap();
        $pricing       = $this->pricingCalculator->calculate($vehicle, $specials, $bodyStyleMap);
        $allSpecials   = $this->pricingCalculator->findMatching($vehicle, $specials, $bodyStyleMap);

        // ── VDP data ──────────────────────────────────────────────────────────
        $spec           = $vehicle->specs;
        $allPhotos      = $vehicle->photos;
        $mainPhoto      = $vehicle->primaryPhoto ?? $allPhotos->first();
        $thumbnails     = $allPhotos->where('id', '!=', $mainPhoto?->id)->take(4);
        $groupedOptions = $this->vehicleDetail->groupedFactoryOptions($vehicle);
        $faqs           = $this->vehicleDetail->buildFaqs($vehicle);
        $related        = $this->vehicleDetail->relatedVehicles($vehicle, $dealerId);

        $this->attachPricing($related, $dealerId);

        $vehicleTitle = strtoupper(trim(implode(' ', array_filter([
            $vehicle->year,
            $vehicle->make->name,
            $vehicle->makeModel->name,
            $vehicle->fuelType?->name,
            $vehicle->trim,
        ]))));

        $seo = [
            'title'       => "{$vehicleTitle} for Sale in Smyrna, TN | Angel Motors Inc",
            'description' => "Buy this {$vehicleTitle} at Angel Motors Inc in Smyrna, TN. " .
                             number_format((int) $vehicle->mileage) . " miles. Price: $" .
                             number_format((int) $vehicle->list_price) . ".",
            'keywords'    => strtolower("{$vehicleTitle}, used cars smyrna tn, angel motors inc"),
        ];

        $windowSticker = $vehicle->printables->firstWhere('name', 'Window Sticker');

        return view('frontend.pages.vehicle-detail', compact(
            'vehicle', 'spec', 'allPhotos', 'mainPhoto', 'thumbnails',
            'pricing', 'allSpecials', 'applicableFees', 'groupedOptions', 'faqs', 'related', 'vehicleTitle', 'seo', 'windowSticker',
        ));
    }



    public function aboutUs(): View
    {
        $dealerId = $this->dealerResolver->resolve();

        $newArrivals = Vehicle::forDealer($dealerId)
            ->active()
            ->select([
                'id', 'dealer_id', 'year', 'make_id', 'make_model_id', 'trim', 'vin',
                'stock_number', 'mileage', 'list_price', 'original_price',
                'is_spotlight', 'featured', 'listed_at',
                'vehicle_condition', 'is_certified', 'body_style_id',
                'exterior_color_id', 'model_number',
            ])
            ->withCount('photos')
            ->with([
                'make:id,name',
                'makeModel:id,name',
                'primaryPhoto' => fn ($q) => $q
                    ->select(['id', 'vehicle_id', 'path', 'disk', 'url'])
                    ->live(),
                'photos' => fn ($q) => $q
                    ->select(['id', 'vehicle_id', 'path', 'disk', 'url', 'sort_order', 'is_primary'])
                    ->live()
                    ->orderBy('sort_order')
                    ->limit(3),
                'prices:vehicle_id,special_price,msrp',
            ])
            ->orderByDesc('listed_at')
            ->limit(4)
            ->get();

        $this->attachPricing($newArrivals, $dealerId);

        return view('frontend.pages.about-us', compact('newArrivals'));
    }

    // ── Contact Us ────────────────────────────────────────────────────────────

    public function contactUs(): View
    {
        return view('frontend.pages.contact-us');
    }

    // ── Make Models (AJAX) ────────────────────────────────────────────────────

    public function makeModels(string $make): JsonResponse
    {
        $makeRecord = Make::where('name', $make)
            ->orWhere('id', $make)
            ->first(['id']);

        if (! $makeRecord) {
            return response()->json([]);
        }

        $models = MakeModel::where('make_id', $makeRecord->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($models);
    }

    public function vinDecode(VinDecodeRequest $request): JsonResponse
    {
        $result = ($this->vinDecoder)->decode($request->input('vin'));

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        $d = $result['data'];

        // Return only what the frontend form needs — no FK IDs
        return response()->json([
            'success' => true,
            'partial' => $result['partial'],
            'data'    => [
                'year'                 => $d['year'],
                'make'                 => $d['make'],
                'model'                => $d['model'],
                'trim'                 => $d['trim'],
                'engine_string'        => $d['engine_string'],
                'drive_type'           => $d['drive_type'],
                'drivetrain_standard'  => $d['drivetrain_standard'],
                'fuel_type_primary'    => $d['fuel_type_primary'],
                'body_class'           => $d['body_class'],
            ],
        ]);
    }

    public function showPage(string $slug): View
    {
        $dealerId = $this->dealerResolver->resolve();
        
        $page = \App\Models\Website\Page::where('slug', $slug)
            ->where('dealer_id', $dealerId)
            ->where('is_active', true)
            ->firstOrFail();

        return view('frontend.pages.dynamic-page', compact('page'));
    }

    // ─── Print Printables ───────────────────────────────────────────────────────────────

    public function printable(Request $request, Vehicle $vehicle, VehiclePrintable $printable): View|Response
    {
        $vehicle->loadMissing([
            'make', 'makeModel', 'dealer',
            'exteriorColor', 'interiorColor',
            'fuelType', 'transmissionType', 'drivetrainType',
            'specs', 'prices', 'primaryPhoto',
            'factoryOptions.category',
        ]);


        if ($printable->html_template) {
            return response($printable->html_template, 200)
                ->header('Content-Type', 'text/html');
        }

        $viewMap = [
            'Window Sticker' => 'dealer.printables.window-sticker',
            'Buyer\'s Guide' => 'dealer.printables.buyers-guide',
            'Generate Quote' => 'dealer.printables.generate-quote',
        ];

        $view = $viewMap[$printable->name] ?? 'dealer.printables.window-sticker';

        return view($view, compact('vehicle', 'printable'));
    }

    // ── Private: attach pricing to each vehicle ───────────────────────────────

    private function attachPricing(
        LengthAwarePaginator|EloquentCollection|Collection $vehicles,
        int $dealerId,
    ): void {
        $specials     = $this->pricingCalculator->getActiveSpecials($dealerId);
        $bodyStyleMap = $this->pricingCalculator->getBodyStyleMap();

        foreach ($vehicles as $vehicle) {
            $vehicle->pricing = $this->pricingCalculator->calculate(
                $vehicle,
                $specials,
                $bodyStyleMap,
            );
        }
    }

    // ── Private: active filter keys ──────────────────────────────────────────

    private function activeFilters(Request $request): array
    {
        return $request->only([
            'make', 'model', 'year', 'mileage', 'price',
            'body_type', 'body_style',
            'exterior_color', 'interior_color',
            'transmission', 'drivetrain', 'fuel_type',
            'seating', 'engine', 'feature', 'search', 'sort',
        ]);
    }
}