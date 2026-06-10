<?php

namespace App\Services\Exports;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use Illuminate\Http\Request;


class ExportMakerService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {}

    public function makeExport(Dealer $dealer, Request $request, ?string $exportType)
    {
        switch ($exportType) {
            case 'cars-for-sales':
                return (new CarsForSalesExportService())->carsForSaleTxt($dealer, $request);
                // return (new CarsForSalesExportService())->carsForSaleCsv($dealer, $request);
            case 'carfax':
                return (new CarfaxExportService())->carfaxXml($dealer, $request);
            case 'truecars':
                return (new TrueCarsService())->exportCsv($dealer, $request);
            default:
                throw new \InvalidArgumentException("Unsupported export type: $exportType");
        }
    }


    public function defaultExport(Dealer $dealer, Request $request)
    {
        $fileName = 'dealer_' . $dealer->id . '_vehicles_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'title', 'description',
            'dealer_id', 'dealer_name', 'dealer_phone',
            'vehicle_id',
            'state_of_vehicle', 'status', 'availability', 'vin', 'stock_number',
            'year', 'make', 'model', 'trim', 'body_style', 'body_type',
            'list_price', 'price', 'special_price', 'cost_price',
            'addon_price', 'mileage.value', 'mileage.unit', 'fuel_type', 'exterior_color', 'interior_color',
            'drivetrain', 'transmission', 'images', 'date_first_on_lot',
            'days_on_lot', 'link', 'url', 'video_link', 'custom_label_0', 'custom_label_1', 'custom_label_2',
            'address', 'address.city', 'address.region', 'address.country', 'address.postal_code',
            'latitude', 'longitude',

        ];

        $columns = array_merge($columns, [
            'Interior Material', 'Wheelbase', 'Door Count', 'Engine Displacement', 'Cylinders',
            'Engine', 'Transmission Speed', 'Option Description', 'Option Code', 'Photo Timestamp',
            'Dealer Comments on Vehicle', 'Last Modified Date', 'ExtraPrice1', 'ExtraPrice2', 'ExtraPrice3',
            'Factory Certified', 'Dealer Certified', 'Model Code', 'Chrome Style ID',
            'Exterior Color Code', 'Interior Color Code', 'City MPG', 'Hwy MPG',
        ]);

        $dealer->load('locations');
        $location      = $dealer->locations->first();
        $dealerAddress = [
            'address'             => $location?->street1 ?? '',
            'address.city'        => $location?->city ?? '',
            'address.region'      => $location?->state ?? '',
            'address.country'     => $location?->country ?? '',
            'address.postal_code' => $location?->postalcode ?? '',
        ];

        $callback = function () use ($columns, $dealer, $dealerAddress) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Vehicle::withoutGlobalScopes()->where('dealer_id', $dealer->id)
                ->with([
                    'make', 'makeModel', 'bodyType', 'bodyStyle', 'fuelType', 'exteriorColor', 'interiorColor',
                    'drivetrainType', 'transmissionType', 'photos', 'notes', 'specs', 'prices', 'video',
                    'factoryOptions', 'primaryPhoto',
                ])
                ->whereIn('status', ['active', 'sold'])
                ->chunk(100, function ($vehicles) use ($file, $dealer, $dealerAddress) {
                    foreach ($vehicles as $vehicle) {
                        $images = collect($vehicle->photos)->pluck('url')->implode(',');
                        $url = $dealer->domain ? "https://{$dealer->domain}/vehicles/{$vehicle->slug}" : '';

                        $row = [
                            $vehicle->display_title ?? '',
                            $vehicle->notes?->dealer_notes ?? $vehicle->notes?->ai_description ?? '',
                            $dealer->internal_id ?? $dealer->id,
                            $dealer->company_name,
                            $dealer->phone ?? '',
                            $vehicle->ulid ?? $vehicle->id, // vehicle_id

                            $vehicle->vehicle_condition ?? '',
                            $vehicle->status ?? '',
                            $vehicle->status === 'active' ? 'in stock' : 'out of stock',
                            $vehicle->vin,
                            $vehicle->stock_number,
                            $vehicle->year,
                            $vehicle->make?->name ?? '',
                            $vehicle->makeModel?->name ?? '',
                            $vehicle->trim,
                            $vehicle->bodyStyle?->name ?? '',
                            $vehicle->bodyType?->name ?? '',
                            $vehicle->list_price ?? '',
                            $vehicle->prices?->internet_price ?? $vehicle->list_price ?? '',
                            $vehicle->prices?->special_price ?? '',
                            $vehicle->original_price ?? $vehicle->prices?->dealer_cost ?? '',
                            $vehicle->prices?->addon_price ?? '',
                            $vehicle->mileage ?? '',
                            'mi', // mileage.unit
                            $vehicle->fuelType?->name ?? '',
                            $vehicle->exteriorColor?->name ?? '',
                            $vehicle->interiorColor?->name ?? '',
                            $vehicle->drivetrainType?->name ?? '',
                            $vehicle->transmissionType?->name ?? '',
                            $images,
                        ];

                        $remainingRow = [
                            $vehicle->listed_at?->toDateString() ?? '',
                            $vehicle->days_on_lot ?? '',
                            $url,                             // link
                            $url,                             // url
                            $vehicle->video?->url ?? '',      // video_link
                            $vehicle->bodyStyle?->name ?? '', // custom_label_0
                            '',                               // custom_label_1
                            '',                               // custom_label_2
                            $dealerAddress['address'],
                            $dealerAddress['address.city'],
                            $dealerAddress['address.region'],
                            $dealerAddress['address.country'],
                            $dealerAddress['address.postal_code'],
                            '',                             // latitude
                            '',                             // longitude
                            $vehicle->ulid ?? $vehicle->id, // vehicle_id

                                                                                                  // Appended Columns
                            $vehicle->specs?->interior_material ?? '',                            // Interior Material
                            $vehicle->specs?->wheelbase ?? '',                                    // Wheelbase
                            $vehicle->doors ?? '',                                                // Door Count
                            $vehicle->specs?->displacement ?? '',                                 // Engine Displacement
                            $vehicle->specs?->cylinders ?? '',                                    // Cylinders
                            $vehicle->engine ?? '',                                               // Engine
                            $vehicle->specs?->transmission_standard ?? '',                        // Transmission Speed
                            $vehicle->factoryOptions?->pluck('label')->implode(' | ') ?? '',      // Option Description
                            $vehicle->factoryOptions?->pluck('option_key')->implode(' | ') ?? '', // Option Code
                            $vehicle->primaryPhoto?->updated_at?->toIso8601String() ?? '',        // Photo Timestamp
                            $vehicle->notes?->dealer_notes ?? '',                                 // Dealer Comments on Vehicle
                            $vehicle->updated_at?->toIso8601String() ?? '',                       // Last Modified Date
                            $vehicle->original_price ?? '',                                       // ExtraPrice1 (originalprice)
                            $vehicle->prices?->addon_price ?? '',                                 // ExtraPrice2 (addonprice)
                            $vehicle->prices?->internet_price ?? '',                              // ExtraPrice3 (Internet Price)
                            $vehicle->specs?->factory_certified ?? '',                            // Factory Certified
                            $vehicle->specs?->dealer_certified ?? '',                             // Dealer Certified
                            $vehicle->model_number ?? '',                                         // Model Code
                            $vehicle->specs?->chrome_style_id ?? '',                              // Chrome Style ID
                            $vehicle->specs?->exterior_color_code ?? '',                          // Exterior Color Code
                            $vehicle->specs?->interior_color_code ?? '',                          // Interior Color Code
                            $vehicle->specs?->mpg_city ?? '',                                     // City MPG
                            $vehicle->specs?->mpg_highway ?? '',                                  // Hwy MPG
                        ];

                        $row = array_merge($row, $remainingRow);

                        fputcsv($file, $row);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
