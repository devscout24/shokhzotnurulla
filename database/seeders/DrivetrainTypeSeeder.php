<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrivetrainTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $drivetrains = [
            'FWD',   // Front-Wheel Drive
            'RWD',   // Rear-Wheel Drive
            'AWD',   // All-Wheel Drive
            '4WD',   // Four-Wheel Drive
            '4x4',   // Four-by-Four
            '4x2',   // Two-Wheel Drive
        ];

        foreach ($drivetrains as $name) {
            DB::table('drivetrain_types')->insertOrIgnore([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
