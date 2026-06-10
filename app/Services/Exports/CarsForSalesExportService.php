<?php

namespace App\Services\Exports;

use App\Helpers\TimeHelper;
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
        // $fileName = 'inventory'.$dealer->id.'_carsforsale_'.date('Y-m-d_H-i-s').'.csv';
        $fileName = 'inventory'.$dealer->id.'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'DealerID', 'NEWUSED', 'VIN', 'StockNumber', 'Make', 'Model', 'ModelYear', 'Trim', 'BodyStyle',
            'Mileage', 'EngineDescription', 'Cylinders', 'FuelType', 'Transmission', 'Price',
            // 'Status',
            'ExteriorColor', 'InteriorColor', 'Options', 'Description', 'Images',
        ];

        $condition =[
            'New' => 'N',
            'Used' => 'U',
            'Certified Pre-Owned' => 'CPO',
        ];

        $callback = function () use ($columns, $dealer, $condition) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Vehicle::withoutGlobalScopes()->where('dealer_id', $dealer->id)
                ->with([
                    'make', 'makeModel', 'bodyStyle', 'fuelType', 'exteriorColor', 'interiorColor',
                    'transmissionType', 'photos', 'notes', 'specs', 'prices', 'factoryOptions',
                ])
                ->whereIn('status', ['active'])
                ->chunk(100, function ($vehicles) use ($file, $dealer, $condition) {
                    foreach ($vehicles as $vehicle) {
                        $images = collect($vehicle->photos)->pluck('url')->implode(',');
                        $options = $vehicle->factoryOptions?->pluck('label')->implode(', ') ?? '';
                        $description = $vehicle->notes?->dealer_notes ?? $vehicle->notes?->ai_description ?? '';

                        $row = [
                            $dealer->internal_id ?? $dealer->id,
                            $condition[$vehicle->vehicle_condition] ?? '',
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
                            // $vehicle->status ?? '',
                            $vehicle->exteriorColor?->name ?? '',
                            $vehicle->interiorColor?->name ?? '',
                            $options,
                            TimeHelper::strip_tags($description),
                            $images,
                        ];

                        fputcsv($file, $row);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function carsForSaleTxt(Dealer $dealer, Request $request){
        // $fileName = 'inventory'.$dealer->id.'.txt';
        $fileName = 'inventory'.'.txt';

        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'DealerID', 'NEWUSED', 'VIN', 'StockNumber', 'Make', 'Model', 'ModelYear', 'Trim', 'BodyStyle',
            'Mileage', 'EngineDescription', 'Cylinders', 'FuelType', 'Transmission', 'Price',
            // 'Status',
            'ExteriorColor', 'InteriorColor', 'Options', 'Description', 'Images',
        ];

        $condition = [
            'New' => 'N',
            'Used' => 'U',
            'Certified Pre-Owned' => 'CPO',
        ];

        $formatRow = function (array $fields): string {
            return implode(", ", array_map(fn ($v) => '"'.str_replace('"', '""', (string) $v).'"', $fields))."\n";
        };

        $callback = function () use ($columns, $dealer, $condition, $formatRow) {
            $file = fopen('php://output', 'w');
            fwrite($file, $formatRow($columns));

            Vehicle::withoutGlobalScopes()->where('dealer_id', $dealer->id)
                ->with([
                    'make', 'makeModel', 'bodyStyle', 'fuelType', 'exteriorColor', 'interiorColor',
                    'transmissionType', 'photos', 'notes', 'specs', 'prices', 'factoryOptions',
                ])
                ->whereIn('status', ['active'])
                ->chunk(100, function ($vehicles) use ($file, $dealer, $condition, $formatRow) {
                    foreach ($vehicles as $vehicle) {
                        $images = collect($vehicle->photos)->pluck('url')->implode(',');
                        $options = $vehicle->factoryOptions?->pluck('label')->implode(', ') ?? '';
                        $description = $vehicle->notes?->dealer_notes ?? $vehicle->notes?->ai_description ?? '';

                        $row = [
                            $dealer->internal_id ?? $dealer->id,
                            $condition[$vehicle->vehicle_condition] ?? '',
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
                            // $vehicle->status ?? '',
                            $vehicle->exteriorColor?->name ?? '',
                            $vehicle->interiorColor?->name ?? '',
                            $options,
                            TimeHelper::strip_tags($description),
                            $images,
                        ];

                        fwrite($file, $formatRow($row));
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
