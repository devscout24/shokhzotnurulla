<?php

namespace App\Services\Exports;

use App\Helpers\TimeHelper;
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
            'condition', 'price', 'certified',
            'exterior_color', 'interior_color',
            'transmission_type', 'engine', 'drive_train', 'fuel_type',

            'new_used', 'vehicle_status', 'date-in-stock', 'door_count',

            'exteriorcolor_code', 'interiorcolor_code',
            'picture_updated', 'modified_date',

            'option_descriptions', 'option_codes',
            'description', 'image_urls', 'dealer_code',
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
                            $vehicle->vin ?? '', // vin
                            $vehicle->stock_number ?? '', // stock_number
                            $vehicle->year ?? '', // year
                            $vehicle->make?->name ?? '', // make
                            $vehicle->makeModel?->name ?? '', // model
                            $vehicle->trim ?? '', // trim
                            $vehicle->bodyStyle?->name ?? '', // body_style
                            $vehicle->mileage ?? '', // mileage
                            $conditionMap[$vehicle->vehicle_condition] ?? '', // condition
                            $vehicle->prices?->internet_price ?? $vehicle->list_price ?? '', // price
                            $vehicle->is_certified ? 1 : 0, // certified
                            $vehicle->exteriorColor?->name ?? '', // exterior_color
                            $vehicle->interiorColor?->name ?? '', // interior_color
                            $vehicle->transmissionType?->name ?? '', // transmission_type
                            $vehicle->engine ?? '', // engine
                            $vehicle->drivetrainType?->name ?? '', // drive_train
                            $vehicle->fuelType?->name ?? '', // fuel_type

                            // ---------------- updates for TrueCar specific fields below ----------------

                            $vehicle->status ?? '', // new_used
                            $vehicle->location_status ?? '', // vehicle_status
                            $vehicle->listed_at?->toDateString() ?? '', // date-in-stock
                            $vehicle->doors ?? '', // door_count

                            $vehicle->specs?->exterior_color_code ?? '', // exteriorcolor_code
                            $vehicle->specs?->interior_color_code ?? '', // interiorcolor_code

                            $vehicle->primaryPhoto?->updated_at?->toIso8601String() ?? '', // picture_updated
                            $vehicle->updated_at?->toIso8601String() ?? '', // modified_date

                            $vehicle->factoryOptions?->pluck('label')->implode('|') ?? '', // option_descriptions
                            $vehicle->factoryOptions?->pluck('option_key')->implode('|') ?? '', // option_codes

                            $description, // description
                            $images, // image_urls
                            $dealer->internal_id ?? $dealer->id, // dealer_code,

                        ];

                        fputcsv($file, $row);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function exportBulkCsv(Request $request)
    {
        $fileName = 'webgurus365.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'dealer_id',
            'vin', 'stock_number', 'year', 'make', 'model', 'trim', 'body_style', 'mileage',
            'condition', 'price', 'certified',

            'exterior_color', 'interior_color',

            'transmission_type', 'engine', 'drive_train', 'fuel_type',

            'new_used', 'vehicle_status', 'date-in-stock', 'door_count',

            'exteriorcolor_code', 'interiorcolor_code',
            'picture_updated', 'modified_date',

            'option_descriptions', 'option_codes',
            'description', 'url', 'image_urls',
        ];

        $conditionMap = [
            'New'                 => 'New',
            'Used'                => 'Used',
            'Certified Pre-Owned' => 'CPO',
        ];

        $callback = function () use ($columns, $conditionMap) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Dealer::query()->where('status', 'active')
                ->where('is_active', true)
                ->chunk(20, function ($dealers) use ($file, $conditionMap) {
                    foreach ($dealers as $dealer) {
                        Vehicle::withoutGlobalScopes()
                            ->where('dealer_id', $dealer->id)
                            ->with([
                                'make', 'makeModel', 'bodyStyle', 'fuelType', 'exteriorColor', 'interiorColor',
                                'transmissionType', 'drivetrainType', 'photos', 'primaryPhoto', 'notes',
                                'specs', 'prices', 'factoryOptions',
                            ])
                            ->whereIn('status', ['active'])
                            ->chunk(100, function ($vehicles) use ($file, $dealer, $conditionMap) {
                                foreach ($vehicles as $vehicle) {
                                    $images      = collect($vehicle->photos)->pluck('url')->implode('|');
                                    $description = TimeHelper::strip_tags($vehicle?->notes?->dealer_notes) ?? '';
                                    $options     = $vehicle->factoryOptions?->pluck('label')->implode('|') ?? '';
                                    $optionCodes = $vehicle->factoryOptions?->pluck('option_key')->implode('|') ?? '';
                                    $picUpdated  = $vehicle->primaryPhoto?->updated_at?->toIso8601String() ?? '';

                                    $row = [
                                        $dealer->internal_id ?? $dealer->id,          // dealer_id
                                        $vehicle->vin ?? '',                           // vin
                                        $vehicle->stock_number ?? '',                  // stock_number
                                        $vehicle->year ?? '',                          // year
                                        $vehicle->make?->name ?? '',                   // make
                                        $vehicle->makeModel?->name ?? '',              // model
                                        $vehicle->trim ?? '',                          // trim
                                        $vehicle->bodyStyle?->name ?? '',              // body_style
                                        $vehicle->mileage ?? '',                       // mileage

                                        $conditionMap[$vehicle->vehicle_condition] ?? '', // condition
                                        $vehicle->prices?->internet_price ?? $vehicle->list_price ?? '', // price
                                        $vehicle->is_certified ? 1 : 0,            // certified (to keep or not, doubt)


                                        $vehicle->exteriorColor?->name ?? '',          // exterior_color
                                        $vehicle->interiorColor?->name ?? '',          // interior_color

                                        $vehicle->transmissionType?->name ?? '',       // transmission
                                        $vehicle->engine ?? '',                        // engine
                                        $vehicle->drivetrainType?->name ?? '',         // drive_train
                                        $vehicle->fuelType?->name ?? '',               // fuel_type


                                        $vehicle->status ?? '',                        // new_used  (vehicle->status)
                                        $vehicle->location_status ?? '',               // vehicle_status (location_status)
                                        $vehicle->listed_at?->toDateString() ?? '',    // date-in-stock
                                        $vehicle->doors ?? '',                         // door_count

                                        $vehicle->specs?->exterior_color_code ?? '',   // exteriorcolor_code
                                        $vehicle->specs?->interior_color_code ?? '',   // interiorcolor_code

                                        $picUpdated,                                   // picture_updated
                                        $vehicle->updated_at?->toIso8601String() ?? '', // modified_date

                                        $options,                                      // option_descriptions
                                        $optionCodes,                                  // option_codes
                                        $description,                                  // description (dealer comments)
                                        $dealer->domain ? "https://{$dealer->domain}/vehicles/{$vehicle->slug}" : '', // url
                                        $images,                                       // image_urls (pipe-separated)
                                    ];

                                    fputcsv($file, $row);
                                }
                            });
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
