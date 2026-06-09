<?php

namespace App\Services\Exports;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use Illuminate\Http\Request;

class CarfaxExportService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {}

    public function carfaxCsv(Dealer $dealer, Request $request){
        $fileName = 'iventory-feed-carfax_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['VIN', 'Make', 'Model', 'Dealer ID', 'Dealer Name', 'Dealer Contact Info',
        'Location info', 'Image Urls', 'Trim', 'Transmission', 'Body', 'Mileage', 'Features',
         'Exterior Color', 'Interior Color', 'CPO', 'Stock Number', "Seller Comments"];

        $callback = function () use ($columns, $dealer, $request) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Vehicle::withoutGlobalScopes()->where('dealer_id', $dealer->id)
                ->with(['make', 'makeModel', 'bodyStyle', 'notes'])
                ->whereIn('status', ['active'])
                ->chunk(100, function ($vehicles) use ($file) {
                    foreach ($vehicles as $vehicle) {
                        $accidentHistory = $vehicle->notes?->accident_history ?? '';
                        $serviceHistory = $vehicle->notes?->service_history ?? '';

                        $row = [
                            $vehicle->vin ?? '',
                            $vehicle->make?->name ?? '',
                            $vehicle->makeModel?->name ?? '',
                            $vehicle->year ?? '',
                            $vehicle->trim ?? '',
                            $vehicle->bodyStyle?->name ?? '',
                            $vehicle->mileage ?? '',
                            $accidentHistory,
                            $serviceHistory,
                        ];

                        fputcsv($file, $row);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
