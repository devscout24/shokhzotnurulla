<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FactoryOptionCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Extracted from OverFuel — Installed Factory Options accordion
        $data = [
            'Entertainment and Technology' => [
                'Audio System',
                'In Car Entertainment',
                'Telematics',
            ],
            'Exterior' => [
                'Exterior Features',
                'Lights',
                'Mirrors',
                'Wheels and Tires',
                'Windows',
            ],
            'Interior' => [
                'Air Conditioning',
                'Comfort Features',
                'Convenience Features',
                'Instrumentation',
                'Seats',
            ],
            'Performance' => [
                'Powertrain',
                'Suspension',
                'Towing and Hauling',
            ],
            'Safety and Security' => [
                'Airbags',
                'Brakes',
                'Safety',
                'Seatbelts',
                'Security',
                'Stability and Traction',
            ],
        ];

        foreach ($data as $categoryName => $groups) {
            $categoryId = DB::table('factory_option_categories')->insertGetId([
                'name'       => $categoryName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($groups as $groupName) {
                DB::table('factory_option_groups')->insertOrIgnore([
                    'category_id' => $categoryId,
                    'name'        => $groupName,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
