<?php

namespace App\Services\Exports;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use Illuminate\Http\Request;

class CarsForSalesExportService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function carsForSaleCsv(Dealer $dealer, Request $request){
        $fileName = 'dealer_'.$dealer->id.'_carsforsale_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'DealerID', 'NEWUSED', 'VIN', 'StockNumber', 'Make', 'Model', 'ModelYear', 'Trim', 'BodyStyle',
            'Mileage', 'EngineDescription', 'Cylinders', 'FuelType', 'Transmission', 'Price', 'ExteriorColor',
            'InteriorColor', 'Options', 'Description', 'Images',
        ];

        $callback = function () use ($columns, $dealer) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Vehicle::withoutGlobalScopes()->where('dealer_id', $dealer->id)
                ->with([
                    'make', 'makeModel', 'bodyStyle', 'fuelType', 'exteriorColor', 'interiorColor',
                    'transmissionType', 'photos', 'notes', 'specs', 'prices', 'factoryOptions',
                ])
                ->whereIn('status', ['active', 'sold'])
                ->chunk(100, function ($vehicles) use ($file, $dealer) {
                    foreach ($vehicles as $vehicle) {
                        $images = collect($vehicle->photos)->pluck('url')->implode(',');
                        $options = $vehicle->factoryOptions?->pluck('label')->implode(', ') ?? '';
                        $description = $vehicle->notes?->dealer_notes ?? $vehicle->notes?->ai_description ?? '';

                        $row = [
                            $dealer->internal_id ?? $dealer->id,
                            $vehicle->vehicle_condition ?? '',
                            $vehicle->vin ?? '',
                            $vehicle->stock_number ?? '',
                            $vehicle->make?->name ?? '',
                            $vehicle->makeModel?->name ?? '',
                            $vehicle->year ?? '',
                            $vehicle->trim ?? '',
                            $vehicle->bodyStyle?->name ?? '',
                            $vehicle->mileage ?? '',
                            $vehicle->engine ?? '',
                            $vehicle->specs?->cylinders ?? '',
                            $vehicle->fuelType?->name ?? '',
                            $vehicle->transmissionType?->name ?? '',
                            $vehicle->prices?->internet_price ?? $vehicle->list_price ?? '',
                            $vehicle->exteriorColor?->name ?? '',
                            $vehicle->interiorColor?->name ?? '',
                            $options,
                            $description,
                            $images,
                        ];

                        fputcsv($file, $row);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
