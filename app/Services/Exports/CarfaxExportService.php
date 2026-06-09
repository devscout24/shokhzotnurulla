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
                ->with(['make', 'makeModel', 'bodyStyle', 'photos', 'notes', 'transmissionType'])
                ->whereIn('status', ['active'])
                ->chunk(100, function ($vehicles) use ($file) {
                    foreach ($vehicles as $vehicle) {
                        $images = collect($vehicle->photos)->pluck('url')->implode(',');

                        $row = [
                            $vehicle->vin ?? '',
                            $vehicle->make?->name ?? '',
                            $vehicle->makeModel?->name ?? '',
                            $vehicle->dealer?->internal_id ?? '',
                            $vehicle->dealer?->company_name ?? '',
                            $vehicle->dealer?->phone ?? '',
                            '', // Location info (i have multiple locations, not sure which one to use)
                            $images,
                            $vehicle->trim ?? '',
                            $vehicle->transmissionType?->name ?? '',
                            $vehicle->bodyStyle?->name ?? '',
                            $vehicle->mileage ?? '',
                        ];

                        fputcsv($file, $row);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
