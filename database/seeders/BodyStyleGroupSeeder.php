<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BodyStyleGroupSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Extracted from OverFuel Edit Details — Body sub-type optgroups
        $groups = [
            'All Purpose',
            'Commercial Vehicles',
            'Marine',
            'Powersports',
        ];

        foreach ($groups as $group) {
            DB::table('body_style_groups')->insertOrIgnore([
                'name'       => $group,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
