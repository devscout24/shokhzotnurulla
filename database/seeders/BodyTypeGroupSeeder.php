<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BodyTypeGroupSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $groups = [
            'Passenger Vehicles',
            'Commercial Vehicles',
            'Commercial Trucks',
            'Powersports',
            'Recreational Vehicles',
            'Trailers',
            'Other',
        ];

        foreach ($groups as $group) {
            DB::table('body_type_groups')->insertOrIgnore([
                'name'       => $group,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
