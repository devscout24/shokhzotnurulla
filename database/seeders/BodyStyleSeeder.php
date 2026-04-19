<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BodyStyleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Extracted directly from OverFuel Edit Details — Body sub-type dropdown
        $data = [
            'All Purpose' => [
                'Conventional', 'Crew Cab', 'Crew Pickup', 'CrewMax',
                'Crossover', 'Double Cab', 'Extended Cab', 'Extended Wheelbase',
                'Flatbed', 'King Cab', 'Long Bed', 'Long Wheelbase',
                'Mega Cab', 'Off-Road', 'Quad Cab', 'Regular Cab',
                'Regular Wheelbase', 'Short Bed', 'Sport', 'Standard',
                'Standard Cab', 'SuperCab', 'SuperCrew',
            ],
            'Commercial Vehicles' => [
                'Access Cab', 'Crane Truck', 'Landscape',
                'Refrigerated', 'Service Body with Crane', 'Stake Sides',
                'Utility', 'Wrecker',
            ],
            'Marine' => [
                'Bay Boats', 'Boat', 'Bowrider', 'Center Consoles',
                'Cruisers', 'Cuddy Cabin', 'Deck Boats', 'Dual Console',
                'Express Cruiser', 'Fishing boat', 'Pontoon', 'Pontoon Boats',
                'Power', 'Rigid Sports Infaltables', 'Ski and Fish',
                'Ski and Wakeboard Boats', 'Ski boat',
            ],
            'Powersports' => [
                'Adventure Touring', 'Chopper', 'Cruiser', 'Dual-Sport',
                'Off-Road', 'Reverse-Trike', 'Scooter', 'Sport',
                'Sport Utility', 'Sport-Touring', 'Touring', 'Trike', 'Youth',
            ],
        ];

        foreach ($data as $groupName => $styles) {
            $group = DB::table('body_style_groups')->where('name', $groupName)->first();
            if (!$group) continue;

            foreach ($styles as $styleName) {
                // slug must be unique globally — prefix with group slug to avoid collisions
                // e.g. "All Purpose + Flatbed" and "Commercial + Flatbed" would clash
                $slug = Str::slug($groupName . '-' . $styleName);

                DB::table('body_styles')->insertOrIgnore([
                    'body_style_group_id' => $group->id,
                    'name'                => $styleName,
                    'slug'                => $slug,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }
}
