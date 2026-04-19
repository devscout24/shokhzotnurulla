<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransmissionTypeSeeder extends Seeder
{
    public function run(): void
    {
        // name = full display name, standard = short standard code
        $transmissions = [
            ['name' => 'Automatic',                          'standard' => 'Automatic'],
            ['name' => '6-Speed Automatic',                  'standard' => 'Automatic'],
            ['name' => '7-Speed Automatic',                  'standard' => 'Automatic'],
            ['name' => '8-Speed Automatic',                  'standard' => 'Automatic'],
            ['name' => '9-Speed Automatic',                  'standard' => 'Automatic'],
            ['name' => '10-Speed Automatic',                 'standard' => 'Automatic'],
            ['name' => 'Manual',                             'standard' => 'Manual'],
            ['name' => '5-Speed Manual',                     'standard' => 'Manual'],
            ['name' => '6-Speed Manual',                     'standard' => 'Manual'],
            ['name' => 'CVT',                                'standard' => 'CVT'],
            ['name' => 'Continuously Variable Transmission', 'standard' => 'CVT'],
            ['name' => 'Dual-Clutch',                        'standard' => 'DCT'],
            ['name' => '7-Speed Dual-Clutch',                'standard' => 'DCT'],
            ['name' => '8-Speed Dual-Clutch',                'standard' => 'DCT'],
            ['name' => 'Semi-Automatic',                     'standard' => 'Semi-Auto'],
            ['name' => 'Direct Drive',                       'standard' => 'Direct Drive'],
        ];

        foreach ($transmissions as $trans) {
            DB::table('transmission_types')->insertOrIgnore([
                'name'       => $trans['name'],
                'standard'   => $trans['standard'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
