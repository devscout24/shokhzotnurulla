<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use Illuminate\Http\Request;

class DealerExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $fileName = 'dealers_export_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Company Name', 'Domain', 'Email', 'Phone', 'Status', 'Created At'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Dealer::chunk(100, function ($dealers) use ($file) {
                foreach ($dealers as $dealer) {
                    $row = [
                        $dealer->id,
                        $dealer->company_name,
                        $dealer->domain,
                        $dealer->email,
                        $dealer->phone,
                        $dealer->status?->value,
                        $dealer->created_at ? $dealer->created_at->format('Y-m-d H:i:s') : '',
                    ];

                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportDealerVehiclesCsv(Dealer $dealer, Request $request)
    {
        $fileName = 'dealer_'.$dealer->id.'_vehicles_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'title', 'description',
            'dealer_id', 'dealer_name', 'dealer_phone',
            'state_of_vehicle', 'status', 'availability', 'vin', 'stock_number',
            'year', 'make', 'model', 'trim', 'body_style', 'body_type',
            'list_price', 'price', 'special_price', 'cost_price',
            'addon_price', 'mileage.value', 'mileage.unit', 'fuel_type', 'exterior_color', 'interior_color',
            'drivetrain', 'transmission', 'image[0].url', 'image[1].url', 'image[2].url', 'image[3].url',
            'image[4].url', 'image[5].url', 'image[6].url', 'image[7].url', 'image[8].url', 'image[9].url',
            'image[10].url', 'image[11].url', 'image[12].url', 'image[13].url', 'image[14].url', 'image[15].url',
            'image[16].url', 'image[17].url', 'image[18].url', 'image[19].url', 'date_first_on_lot',
            'days_on_lot', 'link', 'url', 'video_link', 'custom_label_0', 'custom_label_1', 'custom_label_2',
            'address', 'address.city', 'address.region', 'address.country', 'address.postal_code',
            'latitude', 'longitude',
            'vehicle_id',
        ];

        $dealer->load('locations');
        $location = $dealer->locations->first();
        $dealerAddress = [
            'address' => $location?->street1 ?? '',
            'address.city' => $location?->city ?? '',
            'address.region' => $location?->state ?? '',
            'address.country' => $location?->country ?? '',
            'address.postal_code' => $location?->postalcode ?? '',
        ];

        $callback = function () use ($columns, $dealer, $dealerAddress) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Vehicle::withoutGlobalScopes()->where('dealer_id', $dealer->id)
                ->with([
                    'make', 'makeModel', 'bodyType', 'bodyStyle', 'fuelType', 'exteriorColor', 'interiorColor',
                    'drivetrainType', 'transmissionType', 'photos', 'notes', 'specs', 'prices', 'video',
                ])
                ->chunk(100, function ($vehicles) use ($file, $dealer, $dealerAddress) {
                    foreach ($vehicles as $vehicle) {
                        $images = collect($vehicle->photos)->pluck('url')->take(20)->pad(20, '')->toArray();
                        $url = $dealer->domain ? "https://{$dealer->domain}/vehicles/{$vehicle->slug}" : '';

                        $row = [
                            $vehicle->display_title ?? '',
                            $vehicle->notes?->dealer_notes ?? $vehicle->notes?->ai_description ?? '',
                            $dealer->internal_id ?? $dealer->id,
                            $dealer->company_name,
                            $dealer->phone ?? '',
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
                        ];

                        $row = array_merge($row, $images);

                        $remainingRow = [
                            $vehicle->listed_at?->toDateString() ?? '',
                            $vehicle->days_on_lot ?? '',
                            $url, // link
                            $url, // url
                            $vehicle->video?->url ?? '', // video_link
                            '', // custom_label_0
                            '', // custom_label_1
                            '', // custom_label_2
                            $dealerAddress['address'],
                            $dealerAddress['address.city'],
                            $dealerAddress['address.region'],
                            $dealerAddress['address.country'],
                            $dealerAddress['address.postal_code'],
                            '', // latitude
                            '', // longitude
                            $vehicle->ulid ?? $vehicle->id, // vehicle_id
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
