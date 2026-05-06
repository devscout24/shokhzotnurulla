<?php
namespace App\Http\Controllers\Dealer;

use App\Actions\Inventory\BulkDeletePhotosAction;
use App\Actions\Inventory\CreateVehicleAction;
use App\Actions\Inventory\DeletePhotoAction;
use App\Actions\Inventory\DeleteVehicleAction;
use App\Actions\Inventory\ReorderPhotosAction;
use App\Actions\Inventory\SetPrimaryPhotoAction;
use App\Actions\Inventory\StorePremiumOptionAction;
use App\Actions\Inventory\UpdateDetailsAction;
use App\Actions\Inventory\UpdateFactoryOptionsAction;
use App\Actions\Inventory\UpdateNotesAction;
use App\Actions\Inventory\UpdatePhotoStatusAction;
use App\Actions\Inventory\UpdatePricingAction;
use App\Actions\Inventory\UpdateTagsAction;
use App\Actions\Inventory\UpdateVehicleStatusAction;
use App\Actions\Inventory\UpdateVideoAction;
use App\Actions\Inventory\UploadPhotosAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ReorderPhotosRequest;
use App\Http\Requests\Inventory\StorePremiumOptionRequest;
use App\Http\Requests\Inventory\StoreVehicleRequest;
use App\Http\Requests\Inventory\UpdateDetailsRequest;
use App\Http\Requests\Inventory\UpdateFactoryOptionsRequest;
use App\Http\Requests\Inventory\UpdateNotesRequest;
use App\Http\Requests\Inventory\UpdatePhotoStatusRequest;
use App\Http\Requests\Inventory\UpdatePricingRequest;
use App\Http\Requests\Inventory\UpdateTagsRequest;
use App\Http\Requests\Inventory\UpdateVehicleStatusRequest;
use App\Http\Requests\Inventory\UpdateVideoRequest;
use App\Http\Requests\Inventory\UploadPhotosRequest;
use App\Http\Requests\Inventory\VinDecodeRequest;
use App\Models\Catalog\Color;
use App\Models\Catalog\DrivetrainType;
use App\Models\Catalog\FuelType;
use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use App\Models\Catalog\TransmissionType;
use App\Models\Inventory\Incentive;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehicleHiddenIncentive;
use App\Models\Inventory\VehiclePhoto;
use App\Models\Inventory\VehiclePremiumOption;
use App\Services\Inventory\VdpFormDataService;
use App\Services\Inventory\VinDecodeService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class InventoryController extends Controller
{
    public function __construct(
        private readonly VinDecodeService $vinDecoder,
        private readonly VdpFormDataService $vdpFormData,
        private readonly CreateVehicleAction $createVehicle,
        private readonly UpdatePricingAction $updatePricing,
        private readonly UpdateDetailsAction $updateDetails,
        private readonly UpdateTagsAction $updateTags,
        private readonly UpdateNotesAction $updateNotes,
        private readonly UpdateVehicleStatusAction $updateVehicleStatus,
        private readonly DeleteVehicleAction $deleteVehicle,
        private readonly UpdateFactoryOptionsAction $updateFactoryOptions,
        private readonly UploadPhotosAction $uploadPhotos,
        private readonly DeletePhotoAction $deletePhoto,
        private readonly UpdatePhotoStatusAction $updatePhotoStatus,
        private readonly ReorderPhotosAction $reorderPhotos,
        private readonly BulkDeletePhotosAction $bulkDeletePhotos,
        private readonly SetPrimaryPhotoAction $setPhotoAsPrimary,
        private readonly UpdateVideoAction $updateVideo,
        private readonly StorePremiumOptionAction $storePremiumOption,
    ) {}

    // ─── Inventory Listing ────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;
        $dealer   = $request->user()->currentDealer;

        $query = Vehicle::with([
            'make',
            'makeModel',
            'primaryPhoto',
            'prices:vehicle_id,msrp,internet_price,dealer_cost',
        ])
            ->forDealer($dealerId);

        // ── Sorting ───────────────────────────────────────────────────────────
        $sortBy    = $request->input('sortby', 'listed_at');
        $sortOrder = $request->input('sortorder', 'desc');

        if ($sortBy === 'price') {
            $query->join('vehicle_prices', 'vehicles.id', '=', 'vehicle_prices.vehicle_id')
                ->select('vehicles.*')
                ->orderBy('vehicle_prices.internet_price', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // ── Filters ───────────────────────────────────────────────────────────

        if ($request->filled('status') && in_array($request->status, ['draft', 'active', 'sold'])) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('on_hold')) {
            $query->onHold();
        }

        if ($request->boolean('no_photos')) {
            $query->doesntHave('photos');
        }

        if ($request->filled('make_id')) {
            $query->where('make_id', $request->make_id);
        }

        if ($request->filled('year_min')) {
            $query->where('year', '>=', $request->year_min);
        }

        if ($request->filled('year_max')) {
            $query->where('year', '<=', $request->year_max);
        }

        if ($request->filled('condition')) {
            $query->where('vehicle_condition', $request->condition);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('make', fn($m) => $m->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('makeModel', fn($m) => $m->where('name', 'like', "%{$search}%"))
                    ->orWhere('stock_number', 'like', "%{$search}%")
                    ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        $perPage  = in_array($request->per_page, [12, 25, 48, 100]) ? (int) $request->per_page : 25;
        $vehicles = $query->paginate($perPage)->withQueryString();

        // ── Sidebar filter data ───────────────────────────────────────────────

        $baseQuery  = Vehicle::forDealer($dealerId);
        $totalCount = $baseQuery->count();

        $makeCounts = Make::withCount(['vehicles' => fn($q) => $q->where('dealer_id', $dealerId)])
            ->whereHas('vehicles', fn($q) => $q->where('dealer_id', $dealerId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $years = (clone $baseQuery)
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $conditionCounts = (clone $baseQuery)
            ->select('vehicle_condition', DB::raw('COUNT(*) as total'))
            ->groupBy('vehicle_condition')
            ->orderBy('vehicle_condition')
            ->pluck('total', 'vehicle_condition');

        $bodyTypeCounts = (clone $baseQuery)
            ->join('body_types', 'vehicles.body_type_id', '=', 'body_types.id')
            ->selectRaw('body_types.name, count(*) as total')
            ->groupBy('body_types.name')
            ->orderBy('body_types.name')
            ->pluck('total', 'body_types.name');

        $exteriorColorCounts = Color::withCount(['vehiclesExterior' => fn($q) => $q->where('dealer_id', $dealerId)])
            ->whereHas('vehiclesExterior', fn($q) => $q->where('dealer_id', $dealerId))
            ->orderBy('name')
            ->get(['id', 'name', 'hex']);

        $interiorColorCounts = Color::withCount(['vehiclesInterior' => fn($q) => $q->where('dealer_id', $dealerId)])
            ->whereHas('vehiclesInterior', fn($q) => $q->where('dealer_id', $dealerId))
            ->orderBy('name')
            ->get(['id', 'name', 'hex']);

        $fuelTypeCounts = FuelType::withCount(['vehicles' => fn($q) => $q->where('dealer_id', $dealerId)])
            ->whereHas('vehicles', fn($q) => $q->where('dealer_id', $dealerId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $transmissionCounts = TransmissionType::withCount(['vehicles' => fn($q) => $q->where('dealer_id', $dealerId)])
            ->whereHas('vehicles', fn($q) => $q->where('dealer_id', $dealerId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $drivetrainCounts = DrivetrainType::withCount(['vehicles' => fn($q) => $q->where('dealer_id', $dealerId)])
            ->whereHas('vehicles', fn($q) => $q->where('dealer_id', $dealerId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $seatingCounts = (clone $baseQuery)
            ->whereNotNull('seating_capacity')
            ->selectRaw('seating_capacity, count(*) as total')
            ->groupBy('seating_capacity')
            ->orderBy('seating_capacity')
            ->pluck('total', 'seating_capacity');

        return view('dealer.pages.inventory.index',
            array_merge(
                $this->vdpFormData->getDropdowns(),
                compact(
                    'vehicles', 'makeCounts', 'years', 'totalCount', 'dealer',
                    'conditionCounts', 'bodyTypeCounts', 'exteriorColorCounts',
                    'interiorColorCounts', 'fuelTypeCounts', 'transmissionCounts',
                    'drivetrainCounts', 'seatingCounts',
                )
            )
        );
    }

    // ─── VIN Decode (AJAX) ────────────────────────────────────────────────────

    public function vinDecode(VinDecodeRequest $request): JsonResponse
    {
        $vin       = $request->input('vin');
        $modelYear = $request->integer('model_year') ?: null;

        $result = ($this->vinDecoder)->decode($vin, $modelYear);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success'  => true,
            'partial'  => $result['partial'],
            'message'  => $result['message'],
            'warnings' => $result['warnings'],
            'data'     => $result['data'],
        ]);
    }

    // ─── AJAX: Get Models by Make ─────────────────────────────────────────────

    public function getModels(Request $request): JsonResponse
    {
        $request->validate([
            'make_id' => ['required', 'integer', 'exists:makes,id'],
        ]);

        $models = MakeModel::where('make_id', $request->make_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($models);
    }

    // ─── Store (Create from VIN modal) ───────────────────────────────────────

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $vehicle = ($this->createVehicle)($request->user()->currentDealer, $request->validated());

        AuditLogger::info($request, 'Vehicle created', [
            'vehicle_id'   => $vehicle->id,
            'stock_number' => $vehicle->stock_number,
            'vin'          => $vehicle->vin,
        ]);

        session()->flash('success', 'Vehicle added successfully.');

        return redirect()->route('dealer.inventory.vdp.show', $vehicle);
    }

    // ─── VDP (Vehicle Detail Page) ────────────────────────────────────────────

    public function show(Request $request, Vehicle $vehicle): View
    {
        $this->authorizeVehicle($request, $vehicle);

        $vehicle->load([
            'make', 'makeModel', 'bodyType', 'bodyStyle',
            'fuelType', 'transmissionType', 'drivetrainType',
            'exteriorColor', 'interiorColor',
            'prices', 'specs', 'notes', 'video',
            'photos', 'tags', 'factoryOptions', 'dailyStats', 'premiumOptions',
        ]);

        // Incentives — category match karo vehicle condition se
        $conditionMap = [
            'Used'                => 'used',
            'New'                 => 'new',
            'Certified Pre-Owned' => 'cpo',
        ];
        $vehicleCategory = $conditionMap[$vehicle->vehicle_condition] ?? 'used';

        $applicableIncentives = Incentive::forDealer($vehicle->dealer_id)
            ->enabled()
            ->whereIn('category', [$vehicleCategory, 'all'])
            ->orderBy('title')
            ->get();

        $hiddenIncentiveIds = VehicleHiddenIncentive::where('vehicle_id', $vehicle->id)
            ->pluck('incentive_id')
            ->toArray();

        return view('dealer.pages.inventory.details',
            array_merge(
                ['vehicle' => $vehicle],
                ['applicableIncentives' => $applicableIncentives],
                ['hiddenIncentiveIds' => $hiddenIncentiveIds],
                $this->vdpFormData->getDropdowns(),
                $this->vdpFormData->getFactoryOptionState($vehicle),
            )
        );
    }

    // ─── Update Pricing Tab ───────────────────────────────────────────────────

    public function updatePricing(UpdatePricingRequest $request, Vehicle $vehicle): JsonResponse | RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        ($this->updatePricing)($vehicle, $request->validated());

        AuditLogger::info($request, 'Vehicle pricing updated', ['vehicle_id' => $vehicle->id]);

        return $this->jsonOrBack($request, 'Pricing updated successfully.');
    }

    // ─── Update Details Tab ───────────────────────────────────────────────────

    public function updateDetails(UpdateDetailsRequest $request, Vehicle $vehicle): JsonResponse | RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        ($this->updateDetails)($vehicle, $request->validated());

        AuditLogger::info($request, 'Vehicle details updated', ['vehicle_id' => $vehicle->id]);

        return $this->jsonOrBack($request, 'Details updated successfully.');
    }

    // ─── Update Tags Tab ──────────────────────────────────────────────────────

    public function updateTags(UpdateTagsRequest $request, Vehicle $vehicle): JsonResponse | RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        ($this->updateTags)($vehicle, $request->validated());

        AuditLogger::info($request, 'Vehicle tags updated', ['vehicle_id' => $vehicle->id]);

        return $this->jsonOrBack($request, 'Tags updated successfully.');
    }

    // ─── Update Notes Tab ─────────────────────────────────────────────────────

    public function updateNotes(UpdateNotesRequest $request, Vehicle $vehicle): JsonResponse | RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        ($this->updateNotes)($vehicle, $request->validated());

        AuditLogger::info($request, 'Vehicle notes updated', ['vehicle_id' => $vehicle->id]);

        return $this->jsonOrBack($request, 'Notes updated successfully.');
    }

    // ─── Update Factory Options ───────────────────────────────────────────────

    public function updateFactoryOptions(UpdateFactoryOptionsRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        ($this->updateFactoryOptions)($vehicle, $request->validated());

        AuditLogger::info($request, 'Vehicle factory options updated', ['vehicle_id' => $vehicle->id]);

        return response()->json(['success' => true, 'message' => 'Factory options updated successfully.']);
    }

    // ─── Premium Build Options ────────────────────────────────────────────────

    public function storePremiumOption(StorePremiumOptionRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        $option = ($this->storePremiumOption)($vehicle, $request->validated());

        AuditLogger::info($request, 'Premium option added', [
            'vehicle_id' => $vehicle->id,
            'option_id'  => $option->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Option added successfully.',
            'option'  => [
                'id'           => $option->id,
                'factory_code' => $option->factory_code,
                'category'     => $option->category,
                'name'         => $option->name,
                'description'  => $option->description,
                'msrp'         => $option->msrp,
            ],
        ]);
    }

    public function destroyPremiumOption(Request $request, Vehicle $vehicle, VehiclePremiumOption $option): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        abort_if((int) $option->vehicle_id !== (int) $vehicle->id, 403);

        $option->delete();

        AuditLogger::warning($request, 'Premium option deleted', [
            'vehicle_id' => $vehicle->id,
            'option_id'  => $option->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Option deleted.']);
    }

    // ─── Update Status (Right Sidebar) ───────────────────────────────────────

    public function updateStatus(UpdateVehicleStatusRequest $request, Vehicle $vehicle): JsonResponse | RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        ($this->updateVehicleStatus)($vehicle, $request->validated());

        AuditLogger::info($request, 'Vehicle status updated', [
            'vehicle_id' => $vehicle->id,
            'status'     => $request->status ?? 'unchanged',
        ]);

        return $this->jsonOrBack($request, 'Vehicle status updated successfully.');
    }

    // ─── Gallery ──────────────────────────────────────────────────────────────

    public function gallery(Request $request, Vehicle $vehicle): View
    {
        $this->authorizeVehicle($request, $vehicle);

        $vehicle->load(['make', 'makeModel']);

        $photos     = VehiclePhoto::where('vehicle_id', $vehicle->id)->orderBy('sort_order')->get();
        $liveCount  = $photos->where('status', 'live')->count();
        $draftCount = $photos->where('status', 'draft')->count();

        return view('dealer.pages.inventory.gallery', compact('vehicle', 'photos', 'liveCount', 'draftCount'));
    }

    public function uploadPhotos(UploadPhotosRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        $photos = ($this->uploadPhotos)($vehicle, $request->file('photos'));

        AuditLogger::info($request, 'Vehicle photos uploaded', [
            'vehicle_id' => $vehicle->id,
            'count'      => count($photos),
        ]);

        $result = collect($photos)->map(fn($p) => [
            'id'         => $p->id,
            'url'        => $p->url,
            'status'     => $p->status,
            'sort_order' => $p->sort_order,
            'is_primary' => $p->is_primary,
        ]);

        return response()->json([
            'success' => true,
            'message' => count($photos) . ' photo(s) uploaded successfully.',
            'photos'  => $result,
        ]);
    }

    public function updatePhotoStatus(UpdatePhotoStatusRequest $request, Vehicle $vehicle, VehiclePhoto $photo): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        $this->authorizePhoto($vehicle, $photo);

        $status = $request->validated()['status'];
        ($this->updatePhotoStatus)($photo, $status);

        AuditLogger::info($request, 'Vehicle photo status updated', [
            'vehicle_id' => $vehicle->id,
            'photo_id'   => $photo->id,
            'status'     => $status,
        ]);

        return response()->json(['success' => true, 'message' => 'Photo status updated.']);
    }

    public function reorderPhotos(ReorderPhotosRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        ($this->reorderPhotos)($vehicle, $request->validated()['ids']);

        AuditLogger::info($request, 'Vehicle photos reordered', ['vehicle_id' => $vehicle->id]);

        return response()->json(['success' => true, 'message' => 'Photos reordered.']);
    }

    public function setPhotoAsPrimary(Request $request, Vehicle $vehicle, VehiclePhoto $photo): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        $this->authorizePhoto($vehicle, $photo);

        ($this->setPhotoAsPrimary)($vehicle, $photo);

        AuditLogger::info($request, 'Vehicle primary photo set', [
            'vehicle_id' => $vehicle->id,
            'photo_id'   => $photo->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Primary photo updated.']);
    }

    public function deletePhoto(Request $request, Vehicle $vehicle, VehiclePhoto $photo): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        $this->authorizePhoto($vehicle, $photo);

        ($this->deletePhoto)($photo);

        AuditLogger::warning($request, 'Vehicle photo deleted', [
            'vehicle_id' => $vehicle->id,
            'photo_id'   => $photo->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Photo deleted.']);
    }

    public function bulkDeletePhotos(Request $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        ($this->bulkDeletePhotos)($vehicle);

        AuditLogger::warning($request, 'All vehicle photos deleted', ['vehicle_id' => $vehicle->id]);

        return response()->json(['success' => true, 'message' => 'All photos deleted.']);
    }

    public function downloadPhotos(Request $request, Vehicle $vehicle): BinaryFileResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        $photos  = VehiclePhoto::where('vehicle_id', $vehicle->id)->get();
        $zipName = 'photos-' . $vehicle->stock_number . '-' . now()->format('Ymd') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipName);

        if (! is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($photos as $photo) {
            $fullPath = Storage::disk($photo->disk)->path($photo->path);
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, basename($photo->path));
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    // ─── Update Video ─────────────────────────────────────────────────────────

    public function updateVideo(UpdateVideoRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        ($this->updateVideo)($vehicle, $request->validated());

        AuditLogger::info($request, 'Vehicle video updated', [
            'vehicle_id' => $vehicle->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Video saved successfully.']);
    }

    // ─── VDP Incentive Hide ───────────────────────────────────────────

    public function hideIncentive(Request $request, Vehicle $vehicle, Incentive $incentive): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        VehicleHiddenIncentive::firstOrCreate([
            'vehicle_id'   => $vehicle->id,
            'incentive_id' => $incentive->id,
            'dealer_id'    => $request->user()->current_dealer_id,
        ]);

        AuditLogger::info($request, 'Incentive hidden on vehicle', [
            'vehicle_id'   => $vehicle->id,
            'incentive_id' => $incentive->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Incentive hidden.']);
    }

    public function unhideIncentive(Request $request, Vehicle $vehicle, Incentive $incentive): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        VehicleHiddenIncentive::where('vehicle_id', $vehicle->id)
            ->where('incentive_id', $incentive->id)
            ->delete();

        AuditLogger::info($request, 'Incentive unhidden on vehicle', [
            'vehicle_id'   => $vehicle->id,
            'incentive_id' => $incentive->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Incentive visible.']);
    }

    public function getSoldModelsByMake(Request $request): JsonResponse
    {
        $user            = $request->user();
        $dealerIds       = $user->dealers()->pluck('dealers.id')->toArray();
        $currentDealerId = $request->integer('dealer_id');
        $makeId          = $request->integer('make_id');

        $targetDealerIds = ($currentDealerId && in_array($currentDealerId, $dealerIds))
            ? [$currentDealerId]
            : $dealerIds;

        $dateRange = $request->input('date_range');
        $startDate = null;
        $endDate   = null;

        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                try {
                    $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $endDate   = \Carbon\Carbon::parse($dates[1])->endOfDay();
                } catch (\Exception $e) {}
            }
        }

        $query = Vehicle::whereIn('dealer_id', $targetDealerIds)
            ->where('make_id', $makeId)
            ->sold();

        if ($startDate && $endDate) {
            $query->whereBetween('sold_at', [$startDate, $endDate]);
        }

        $models = $query->with(['makeModel', 'prices'])
            ->get()
            ->groupBy('make_model_id')
            ->map(function ($vehicles) {
                $model     = $vehicles->first()->makeModel;
                $soldCount = $vehicles->count();
                $estSales  = $vehicles->sum(fn($v) => $v->prices->sold_price ?? 0);
                $avgPrice  = $soldCount > 0 ? $estSales / $soldCount : 0;
                $avgDays   = $vehicles->avg(fn($v) => $v->days_on_lot);
                $minDays   = $vehicles->min(fn($v) => $v->days_on_lot);
                $maxDays   = $vehicles->max(fn($v) => $v->days_on_lot);

                return [
                    'model_name'    => $model->name ?? 'Unknown',
                    'sold'          => $soldCount,
                    'est_sales'     => number_format($estSales),
                    'avg_price'     => number_format($avgPrice),
                    'avg_days'      => round($avgDays),
                    'min_days'      => $minDays,
                    'max_days'      => $maxDays,
                    'changes_count' => '--',
                    'avg_change'    => '--',
                ];
            })->sortByDesc('sold')->values();

        return response()->json($models);
    }

    public function exportSoldInventory(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user            = $request->user();
        $dealerIds       = $user->dealers()->pluck('dealers.id')->toArray();
        $currentDealerId = $request->integer('dealer_id');
        $targetDealerIds = ($currentDealerId && in_array($currentDealerId, $dealerIds))
            ? [$currentDealerId]
            : $dealerIds;

        $dateRange = $request->input('date_range');
        $startDate = $endDate = null;
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                try {
                    $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $endDate   = \Carbon\Carbon::parse($dates[1])->endOfDay();
                } catch (\Exception $e) {}
            }
        }

        $query = Vehicle::whereIn('dealer_id', $targetDealerIds)
            ->sold()
            ->with(['make', 'makeModel', 'prices']);

        if ($startDate && $endDate) {
            $query->whereBetween('sold_at', [$startDate, $endDate]);
        }

        // Group by make → model
        $rows = $query->get()
            ->groupBy('make_id')
            ->flatMap(function ($makeVehicles) {
                $make = $makeVehicles->first()->make;
                return $makeVehicles->groupBy('make_model_id')
                    ->map(function ($vehicles) use ($make) {
                        $model      = $vehicles->first()->makeModel;
                        $prices     = $vehicles->map(fn($v) => $v->prices->sold_price ?? $v->prices->internet_price ?? $v->prices->msrp ?? 0);
                        $days       = $vehicles->map(fn($v) => $v->days_on_lot ?? 0);
                        $soldCount  = $vehicles->count();
                        $totalPrice = $prices->sum();

                        return [
                            'make'               => $make->name ?? '',
                            'model'              => $model->name ?? '',
                            'avg_days'           => $soldCount > 0 ? round($days->avg(), 2) : 0,
                            'avg_price'          => $soldCount > 0 ? round($totalPrice / $soldCount, 2) : 0,
                            'count_pricechanges' => '--',
                            'max_days'           => $days->max() ?? 0,
                            'max_price'          => $prices->max() ?? 0,
                            'min_days'           => $days->min() ?? 0,
                            'min_price'          => $prices->min() ?? 0,
                            'pct_pricechanges'   => '--',
                            'soldcount'          => $soldCount,
                            'total_price'        => $totalPrice,
                            'total_pricechanges' => '--',
                        ];
                    });
            });

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-sold-export-' . now()->format('Ymd-His') . '.csv"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ];

        $columns = [
            'make', 'model', 'avg_days', 'avg_price', 'count_pricechanges',
            'max_days', 'max_price', 'min_days', 'min_price',
            'pct_pricechanges', 'soldcount', 'total_price', 'total_pricechanges',
        ];

        return response()->stream(function () use ($rows, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($rows as $row) {
                fputcsv($handle, array_map(fn($col) => $row[$col] ?? '', $columns));
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function exportSoldModelsByMake(Request $request, int $make_id): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user            = $request->user();
        $dealerIds       = $user->dealers()->pluck('dealers.id')->toArray();
        $currentDealerId = $request->integer('dealer_id');
        $targetDealerIds = ($currentDealerId && in_array($currentDealerId, $dealerIds))
            ? [$currentDealerId]
            : $dealerIds;

        $dateRange = $request->input('date_range');
        $startDate = $endDate = null;
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                try {
                    $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $endDate   = \Carbon\Carbon::parse($dates[1])->endOfDay();
                } catch (\Exception $e) {}
            }
        }

        $makeName = \App\Models\Catalog\Make::find($make_id)?->name ?? 'make';

        $query = Vehicle::whereIn('dealer_id', $targetDealerIds)
            ->where('make_id', $make_id)
            ->sold()
            ->with(['makeModel', 'make', 'prices']);

        if ($startDate && $endDate) {
            $query->whereBetween('sold_at', [$startDate, $endDate]);
        }

        $rows = $query->get()
            ->groupBy('make_model_id')
            ->map(function ($vehicles) use ($makeName) {
                $model      = $vehicles->first()->makeModel;
                $prices     = $vehicles->map(fn($v) => $v->prices->sold_price ?? $v->prices->internet_price ?? $v->prices->msrp ?? 0);
                $days       = $vehicles->map(fn($v) => $v->days_on_lot ?? 0);
                $soldCount  = $vehicles->count();
                $totalPrice = $prices->sum();

                return [
                    'make'               => $makeName,
                    'model'              => $model->name ?? '',
                    'avg_days'           => $soldCount > 0 ? round($days->avg(), 2) : 0,
                    'avg_price'          => $soldCount > 0 ? round($totalPrice / $soldCount, 2) : 0,
                    'count_pricechanges' => '--',
                    'max_days'           => $days->max() ?? 0,
                    'max_price'          => $prices->max() ?? 0,
                    'min_days'           => $days->min() ?? 0,
                    'min_price'          => $prices->min() ?? 0,
                    'pct_pricechanges'   => '--',
                    'soldcount'          => $soldCount,
                    'total_price'        => $totalPrice,
                    'total_pricechanges' => '--',
                ];
            })->values();

        $safeMake = preg_replace('/[^a-z0-9]+/i', '-', strtolower($makeName));
        $filename = "inventory-{$safeMake}-models-" . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        $columns = [
            'make', 'model', 'avg_days', 'avg_price', 'count_pricechanges',
            'max_days', 'max_price', 'min_days', 'min_price',
            'pct_pricechanges', 'soldcount', 'total_price', 'total_pricechanges',
        ];

        return response()->stream(function () use ($rows, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($rows as $row) {
                fputcsv($handle, array_map(fn($col) => $row[$col] ?? '', $columns));
            }
            fclose($handle);
        }, 200, $headers);
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        ($this->deleteVehicle)($vehicle);

        AuditLogger::warning($request, 'Vehicle deleted', [
            'vehicle_id'   => $vehicle->id,
            'stock_number' => $vehicle->stock_number,
            'vin'          => $vehicle->vin,
        ]);

        session()->flash('success', 'Vehicle removed successfully.');

        return redirect()->route('dealer.inventory.index');
    }

    // ─── Placeholder methods (future modules) ────────────────────────────────

    public function dashboard(Request $request): View
    {
        $user            = $request->user();
        $dealerIds       = $user->dealers()->pluck('dealers.id')->toArray();
        $currentDealerId = $request->integer('dealer_id');

        // If no dealer_id is provided or it's 0 (All Locations), use all dealer IDs the user belongs to
        $targetDealerIds = ($currentDealerId && in_array($currentDealerId, $dealerIds))
            ? [$currentDealerId]
            : $dealerIds;

        // Date Range Filter
        $dateRange = $request->input('date_range');
        $startDate = null;
        $endDate   = null;

        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                try {
                    $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $endDate   = \Carbon\Carbon::parse($dates[1])->endOfDay();
                } catch (\Exception $e) {
                    // Fallback or ignore
                }
            }
        }

        // Summary Cards Data
        $baseQuery = Vehicle::whereIn('dealer_id', $targetDealerIds);

        // In Stock: Active vehicles
        $inStockQuery = (clone $baseQuery)->active();
        $inStockCount = $inStockQuery->count();
        $inStockCost  = (clone $inStockQuery)->join('vehicle_prices', 'vehicles.id', '=', 'vehicle_prices.vehicle_id')
            ->sum('vehicle_prices.dealer_cost');
        $inStockValue = (clone $inStockQuery)->join('vehicle_prices', 'vehicles.id', '=', 'vehicle_prices.vehicle_id')
            ->sum('vehicle_prices.internet_price');
        // Actually usually sold price for in stock means list price or asking price.
        // But let's follow: "count their cost/dealer_cost and sold price"
        // Wait, "in stock card will count unsold/active cars and count their cost/dealer_cost and sold price".
        // For in-stock, sold_price might be NULL. Maybe they mean list_price or asking_price.
        // Let's use list_price as a fallback for "sold price" on in-stock vehicles if sold_price is not set.
        // Actually, in the screenshot, the "in stock" card shows a total dollar amount.
        // I'll use internet_price as the primary value for in-stock total.

        // Sold: Sold vehicles within date range
        $soldQuery = (clone $baseQuery)->sold();
        if ($startDate && $endDate) {
            $soldQuery->whereBetween('sold_at', [$startDate, $endDate]);
        }
        $soldCount = $soldQuery->count();
        $soldValue = (clone $soldQuery)->join('vehicle_prices', 'vehicles.id', '=', 'vehicle_prices.vehicle_id')
            ->sum('vehicle_prices.sold_price');

        // No Photos: Active vehicles with no photos
        $noPhotosCount = (clone $baseQuery)->active()->doesntHave('photos')->count();

        // No Price: Active vehicles with no list price (asking_price or internet_price is 0 or null)
        $noPriceCount = (clone $baseQuery)->active()
            ->whereHas('prices', function ($q) {
                $q->whereNull('internet_price')->orWhere('internet_price', 0);
            })->count();

        // Inventory: Units Sold Table (Make-wise)
        $soldMakes = (clone $baseQuery)->sold()
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('sold_at', [$startDate, $endDate]))
            ->with(['make', 'prices'])
            ->get()
            ->groupBy('make_id')
            ->map(function ($vehicles) {
                $make      = $vehicles->first()->make;
                $unitsSold = $vehicles->count();
                $avgDays   = $vehicles->avg(fn($v) => $v->days_on_lot);
                $estSales  = $vehicles->sum(fn($v) => $v->prices->sold_price ?? 0);
                $avgPrice  = $unitsSold > 0 ? $estSales / $unitsSold : 0;

                // For # Changes and Avg. Change - these would usually come from an audit log or price history table.
                // Since I don't see a price history table yet, I'll return placeholder or 0 for now if not available.
                // Wait, I should check if there's a price history table.

                return [
                    'make_id'       => $make->id ?? 0,
                    'make_name'     => $make->name ?? 'Unknown',
                    'units_sold'    => $unitsSold,
                    'avg_days'      => round($avgDays),
                    'est_sales'     => $estSales,
                    'avg_price'     => $avgPrice,
                    'changes_count' => '--',
                    'avg_change'    => '--',
                ];
            })->sortByDesc('units_sold')->values();

        $dealers = $user->dealers;

        // ─── Inventory Activity Chart Data ─────────────────────
        $chartLabels = [];
        $chartViews  = [];
        $chartStock  = [];

        $chartStartDate = $startDate ? $startDate->copy() : now()->subDays(29)->startOfDay();
        $chartEndDate   = $endDate ? $endDate->copy() : now()->endOfDay();

        $stats = \App\Models\Inventory\VehicleDailyStat::whereIn('dealer_id', $targetDealerIds)
            ->where('date', '>=', $chartStartDate->format('Y-m-d'))
            ->where('date', '<=', $chartEndDate->format('Y-m-d'))
            ->select('date', DB::raw('SUM(views) as total_views'))
            ->groupBy('date')
            ->pluck('total_views', 'date');

        $vehicles = Vehicle::whereIn('dealer_id', $targetDealerIds)
            ->whereIn('status', ['active', 'sold'])
            ->whereNotNull('listed_at')
            ->with('prices')
            ->get();

        $daysDiff = $chartStartDate->diffInDays($chartEndDate);

        for ($i = $daysDiff; $i >= 0; $i--) {
            $date          = $chartEndDate->copy()->subDays($i);
            $dateStr       = $date->format('Y-m-d');
            $chartLabels[] = $date->format('n/j');

            // Views
            $chartViews[] = (int) ($stats[$dateStr] ?? 0);

            // Stock
            $endOfDay   = $date->copy()->endOfDay();
            $stockCount = $vehicles->filter(function ($v) use ($endOfDay) {
                return $v->listed_at <= $endOfDay && ($v->sold_at === null || $v->sold_at > $endOfDay);
            })->count();
            $chartStock[] = $stockCount;
        }

        // ─── Days in Inventory Chart Data (Active Vehicles Only) ──────────────
        $daysStats = [
            '0-30'   => ['units' => 0, 'total' => 0, 'avg' => 0],
            '31-60'  => ['units' => 0, 'total' => 0, 'avg' => 0],
            '61-90'  => ['units' => 0, 'total' => 0, 'avg' => 0],
            '91-120' => ['units' => 0, 'total' => 0, 'avg' => 0],
            '120+'   => ['units' => 0, 'total' => 0, 'avg' => 0],
        ];

        foreach ($vehicles as $v) {
            if ($v->sold_at === null) {
                $days   = (int) $v->listed_at->diffInDays(now());
                $bucket = '120+';
                if ($days <= 30) {
                    $bucket = '0-30';
                } elseif ($days <= 60) {
                    $bucket = '31-60';
                } elseif ($days <= 90) {
                    $bucket = '61-90';
                } elseif ($days <= 120) {
                    $bucket = '91-120';
                }

                $price  = $v->prices->internet_price ?? $v->prices->msrp ?? 0;
                $daysStats[$bucket]['units']++;
                $daysStats[$bucket]['total'] += $price;
            }
        }

        foreach ($daysStats as $key => $stat) {
            if ($stat['units'] > 0) {
                $daysStats[$key]['avg'] = $stat['total'] / $stat['units'];
            }
        }

        $chartDays = array_column($daysStats, 'units');

        // ─── Inventory by Location (Per Dealer) ──────────────────────────────
        $locationStats = [];
        $allVehicles   = Vehicle::whereIn('dealer_id', $dealerIds)
            ->active()
            ->with('prices')
            ->get();

        foreach ($dealers as $dealer) {
            $dealerVehicles = $allVehicles->where('dealer_id', $dealer->id);
            $units          = $dealerVehicles->count();
            $total          = $dealerVehicles->sum(fn($v) => $v->prices->internet_price ?? 0);
            $avg            = $units > 0 ? $total / $units : 0;

            $locationStats[] = [
                'name'  => $dealer->name,
                'units' => $units,
                'total' => $total,
                'avg'   => $avg,
            ];
        }

        return view('dealer.pages.inventory.dashboard', compact(
            'inStockCount', 'inStockCost', 'inStockValue',
            'soldCount', 'soldValue',
            'noPhotosCount', 'noPriceCount',
            'soldMakes', 'dealers', 'currentDealerId', 'dateRange',
            'chartLabels', 'chartViews', 'chartStock', 'chartDays', 'daysStats',
            'locationStats'
        ));
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function authorizeVehicle(Request $request, Vehicle $vehicle): void
    {
        abort_if($vehicle->dealer_id !== $request->user()->current_dealer_id, 403);
    }

    private function authorizePhoto(Vehicle $vehicle, VehiclePhoto $photo): void
    {
        abort_if((int) $photo->vehicle_id !== (int) $vehicle->id, 403);
    }

    private function jsonOrBack(Request $request, string $message): JsonResponse | RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        session()->flash('success', $message);

        return back();
    }
}
