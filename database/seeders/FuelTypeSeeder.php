<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Extracted directly from OverFuel Fuel dropdown
        $fuelTypes = [
            'Gasoline',
            'Flex Fuel',
            'Diesel',
            'Electric',
            'Hybrid',
        ];

        foreach ($fuelTypes as $name) {
            DB::table('fuel_types')->insertOrIgnore([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
