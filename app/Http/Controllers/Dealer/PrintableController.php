<?php

namespace App\Http\Controllers\Dealer;

use App\Actions\Inventory\DeletePrintableAction;
use App\Actions\Inventory\StorePrintableAction;
use App\Actions\Inventory\UpdatePrintableAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePrintableRequest;
use App\Http\Requests\Inventory\UpdatePrintableRequest;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehiclePrintable;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PrintableController extends Controller
{
    public function __construct(
        private readonly StorePrintableAction  $storePrintable,
        private readonly UpdatePrintableAction $updatePrintable,
        private readonly DeletePrintableAction $deletePrintable,
    ) {}

    // ─── List (AJAX) ──────────────────────────────────────────────────────────

    public function index(Request $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        $printables = VehiclePrintable::where('vehicle_id', $vehicle->id)
            ->orderBy('id')
            ->get(['id', 'name', 'cta', 'description', 'layout']);

        $usedTypes = $printables->pluck('name')->toArray();
        $available = array_values(array_diff(
            array_keys(VehiclePrintable::TYPES),
            $usedTypes
        ));

        return response()->json([
            'success'    => true,
            'printables' => $printables,
            'available'  => $available,
        ]);
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(StorePrintableRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        $printable = ($this->storePrintable)(
            $vehicle,
            $request->user()->current_dealer_id,
            $request->validated()
        );

        AuditLogger::info($request, 'Printable created', [
            'vehicle_id'   => $vehicle->id,
            'printable_id' => $printable->id,
            'name'         => $printable->name,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Printable created successfully.',
            'printable' => [
                'id'          => $printable->id,
                'name'        => $printable->name,
                'cta'         => $printable->cta,
                'description' => $printable->description,
                'layout'      => $printable->layout,
            ],
        ]);
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(UpdatePrintableRequest $request, Vehicle $vehicle, VehiclePrintable $printable): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        $this->authorizePrintable($vehicle, $printable);

        ($this->updatePrintable)($printable, $request->validated());

        AuditLogger::info($request, 'Printable updated', [
            'vehicle_id'   => $vehicle->id,
            'printable_id' => $printable->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Printable updated successfully.',
        ]);
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Request $request, Vehicle $vehicle, VehiclePrintable $printable): JsonResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        $this->authorizePrintable($vehicle, $printable);

        ($this->deletePrintable)($printable);

        AuditLogger::warning($request, 'Printable deleted', [
            'vehicle_id'   => $vehicle->id,
            'printable_id' => $printable->id,
            'name'         => $printable->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Printable deleted.',
        ]);
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(Request $request, Vehicle $vehicle, VehiclePrintable $printable): View|Response
    {
        $this->authorizeVehicle($request, $vehicle);
        $this->authorizePrintable($vehicle, $printable);

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

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function authorizeVehicle(Request $request, Vehicle $vehicle): void
    {
        abort_if($vehicle->dealer_id !== $request->user()->current_dealer_id, 403);
    }

    private function authorizePrintable(Vehicle $vehicle, VehiclePrintable $printable): void
    {
        abort_if((int) $printable->vehicle_id !== (int) $vehicle->id, 403);
    }
}
