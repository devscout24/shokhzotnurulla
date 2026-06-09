<?php

namespace App\Services\Exports;

use App\Enums\DealerStatus;
use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use Illuminate\Http\Request;

class CarGurusService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {}

    public function bulkExport(Request $request)
    {
        $fileName = 'inventory.csv';

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'vin', 'stock_number', 'year', 'make', 'model', 'trim', 'body_style', 'mileage',
            'condition', 'price', 'certified', 'exterior_color', 'interior_color',
            'transmission', 'drive_train', 'engine', 'fuel_type', 'description', 'options', 'image_urls', 'dealer_code',
        ];

        // CarGurus: condition must be NEW, USED, or CPO (uppercase)
        $conditionMap = [
            'New'                 => 'NEW',
            'Used'                => 'USED',
            'Certified Pre-Owned' => 'CPO',
        ];

        // CarGurus: full drivetrain names
        $drivetrainMap = [
            'FWD'  => 'Front-Wheel Drive',
            'RWD'  => 'Rear-Wheel Drive',
            'AWD'  => 'All-Wheel Drive',
            '4WD'  => 'Four-Wheel Drive',
            '4x4'  => 'Four-Wheel Drive',
        ];

        $callback = function () use ($columns, $conditionMap, $drivetrainMap) {
            $file = fopen('php://output', 'w');

            // Write header row with double-quoted fields
            $this->writeCsvRow($file, $columns);

            // Iterate over all active dealers
            Dealer::query()->where('is_active', true)
                ->where('status', DealerStatus::ACTIVE)
                ->whereNull('deleted_at')
                ->chunk(50, function ($dealers) use ($file, $conditionMap, $drivetrainMap) {
                    foreach ($dealers as $dealer) {
                        Vehicle::withoutGlobalScopes()
                            ->where('dealer_id', $dealer->id)
                            ->with([
                                'make', 'makeModel', 'bodyStyle', 'fuelType',
                                'exteriorColor', 'interiorColor', 'transmissionType',
                                'drivetrainType', 'photos', 'notes', 'specs',
                                'prices', 'factoryOptions',
                            ])
                            ->whereIn('status', ['active'])
                            ->chunk(100, function ($vehicles) use ($file, $dealer, $conditionMap, $drivetrainMap) {
                                foreach ($vehicles as $vehicle) {
                                    // Images: comma-separated URLs
                                    $images = collect($vehicle->photos)
                                        ->pluck('url')
                                        ->implode(',');

                                    // Options: comma-separated
                                    $options = $vehicle->factoryOptions?->pluck('label')->implode(',') ?? '';

                                    $description = $vehicle->notes?->dealer_notes
                                        ?? $vehicle->notes?->ai_description
                                        ?? '';

                                    // Normalize drivetrain to CarGurus full name
                                    $rawDrivetrain = $vehicle->drivetrainType?->name ?? '';
                                    $drivetrain    = $drivetrainMap[$rawDrivetrain] ?? $rawDrivetrain;

                                    $row = [
                                        $vehicle->vin ?? '',
                                        $vehicle->stock_number ?? '',
                                        (string) ($vehicle->year ?? ''),
                                        $vehicle->make?->name ?? '',
                                        $vehicle->makeModel?->name ?? '',
                                        $vehicle->trim ?? '',
                                        $vehicle->bodyStyle?->name ?? '',
                                        (string) ($vehicle->mileage ?? ''),
                                        $conditionMap[$vehicle->vehicle_condition] ?? '',
                                        (string) ($vehicle->prices?->internet_price ?? $vehicle->list_price ?? ''),
                                        $vehicle->is_certified ? '1' : '0',
                                        $vehicle->exteriorColor?->name ?? '',
                                        $vehicle->interiorColor?->name ?? '',
                                        $vehicle->transmissionType?->name ?? '',
                                        $drivetrain,
                                        $vehicle->engine ?? '',
                                        $vehicle->fuelType?->name ?? '',
                                        $description,
                                        $options,
                                        $images,
                                        $dealer->internal_id ?? (string) $dealer->id,
                                    ];

                                    $this->writeCsvRow($file, $row);
                                }
                            });
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Write a CSV row with all fields enclosed in double quotes.
     */
    private function writeCsvRow($file, array $fields): void
    {
        $escaped = array_map(
            fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"',
            $fields
        );

        fwrite($file, implode(',', $escaped) . "\n");
    }
}
