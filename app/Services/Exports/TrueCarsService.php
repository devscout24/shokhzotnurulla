<?php

namespace App\Services\Exports;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use Illuminate\Http\Request;

class TrueCarsService
{
    public function __construct()
    {}

    public function exportCsv(Dealer $dealer, Request $request)
    {
        $fileName = 'inventory'.$dealer->id.'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'vin', 'stock_number', 'year', 'make', 'model', 'trim', 'body_style', 'mileage',
            'condition', 'price', 'certified', 'exterior_color', 'interior_color',
            'transmission', 'engine', 'drive_train', 'fuel_type', 'description', 'image_urls', 'dealer_code',
        ];

        $conditionMap = [
            'New'                => 'New',
            'Used'               => 'Used',
            'Certified Pre-Owned' => 'CPO',
        ];

        $callback = function () use ($columns, $dealer, $conditionMap) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Vehicle::withoutGlobalScopes()->where('dealer_id', $dealer->id)
                ->with([
                    'make', 'makeModel', 'bodyStyle', 'fuelType', 'exteriorColor', 'interiorColor',
                    'transmissionType', 'drivetrainType', 'photos', 'notes', 'specs', 'prices',
                ])
                ->whereIn('status', ['active'])
                ->chunk(100, function ($vehicles) use ($file, $dealer, $conditionMap) {
                    foreach ($vehicles as $vehicle) {
                        $images      = collect($vehicle->photos)->pluck('url')->implode('|');
                        $description = $vehicle->notes?->dealer_notes ?? $vehicle->notes?->ai_description ?? '';

                        $row = [
                            $vehicle->vin ?? '',
                            $vehicle->stock_number ?? '',
                            $vehicle->year ?? '',
                            $vehicle->make?->name ?? '',
                            $vehicle->makeModel?->name ?? '',
                            $vehicle->trim ?? '',
                            $vehicle->bodyStyle?->name ?? '',
                            $vehicle->mileage ?? '',
                            $conditionMap[$vehicle->vehicle_condition] ?? '',
                            $vehicle->prices?->internet_price ?? $vehicle->list_price ?? '',
                            $vehicle->is_certified ? 1 : 0,
                            $vehicle->exteriorColor?->name ?? '',
                            $vehicle->interiorColor?->name ?? '',
                            $vehicle->transmissionType?->name ?? '',
                            $vehicle->engine ?? '',
                            $vehicle->drivetrainType?->name ?? '',
                            $vehicle->fuelType?->name ?? '',
                            $description,
                            $images,
                            $dealer->internal_id ?? $dealer->id,
                        ];

                        fputcsv($file, $row);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }
}
