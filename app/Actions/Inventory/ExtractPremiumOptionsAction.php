<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use App\Models\Vin\VehicleVinData;
use Illuminate\Support\Str;

class ExtractPremiumOptionsAction
{
    private const SECTION_LABELS = [
        'mechanical_and_powertrain' => 'Mechanical & Powertrain',
        'safety' => 'Safety',
        'interior' => 'Interior',
        'exterior' => 'Exterior',
    ];

    public function __invoke(Vehicle $vehicle): void
    {
        if ($vehicle->premiumOptions()->exists()) {
            return;
        }

        $vinData = VehicleVinData::where('vin', $vehicle->vin)->first();

        if (! $vinData?->vehicle_databases) {
            return;
        }

        $raw = $vinData->vehicle_databases;
        $data = $raw['data'] ?? [];
        $variant = reset($data);

        if (! $variant || ! isset($variant['feature'])) {
            return;
        }

        $features = $variant['feature'];

        foreach (self::SECTION_LABELS as $section => $label) {
            $items = $features[$section] ?? [];

            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                $item = trim($item);

                if ($item === '') {
                    continue;
                }

                $vehicle->premiumOptions()->create([
                    'factory_code' => $this->generateFactoryCode($item),
                    'category' => $label,
                    'name' => Str::limit($item, 150),
                    'description' => $item,
                    'msrp' => null,
                ]);
            }
        }
    }

    private function generateFactoryCode(string $text): string
    {
        $slug = Str::slug(substr($text, 0, 40));

        if ($slug === '') {
            return 'option-'.md5($text);
        }

        return $slug;
    }
}
