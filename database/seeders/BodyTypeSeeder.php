<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BodyTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Extracted directly from OverFuel Edit Details — Body type dropdown
        $data = [
            'Passenger Vehicles' => [
                'Classic', 'Convertible', 'Coupe', 'Hatchback', 'Minivan',
                'Passenger van', 'Pickup truck', 'Sedan', 'SUV', 'Van', 'Wagon',
            ],
            'Commercial Vehicles' => [
                'Activity Bus', 'Ada Bus', 'Boom truck', 'Box truck', 'Bucket truck',
                'Bus', 'Cab and chassis', 'Cabover truck', 'Cargo van', 'Commercial',
                'Commercial Bus', 'Commercial van', 'Contractor truck', 'Crane truck',
                'Crew van', 'Curtain Side truck', 'Cutaway van', 'Day cab',
                'Dump truck', 'Dump truck body', 'Enclosed service truck',
                'Executive Coach', 'Flatbed truck', 'Flatbed truck body',
                'Grapple truck', 'Hooklift truck', 'Landscape truck', 'Lawn mower',
                'Liftgate', 'Mechanics truck', 'Mini School Bus', 'Moving truck',
                'Pickup utility', 'Refrigerated Cargo van', 'Refrigerated truck',
                'Refrigerated truck body', 'Roll off truck', 'Rollback truck',
                'Rotator', 'School Bus', 'Scissor lift truck', 'Service body truck',
                'Service truck', 'Shuttle bus', 'Sleeper cab', 'Sprinter Van',
                'Step van', 'Tow truck', 'Tractor truck', 'Truck body',
                'Utility vehicle', 'Utility/service truck', 'Vending trailer',
                'Wheelchair Accessible Vehicle', 'Winch', 'Wrecker',
            ],
            'Commercial Trucks' => [
                'Cab chassis', 'Compact Track Loaders', 'Concrete Equipment',
                'Excavators', 'HVAC', 'KUV', 'Light Vehicles',
                'Mini Dumpers & Loaders', 'Roll Off', 'Scissor lift', 'Scissor Lifts',
                'Skid-Steer Loaders', 'Sleeper', 'Specialty vehicle',
                'Towable Boom Lifts', 'Tractor', 'Tractors', 'Wheel Loaders',
            ],
            'Powersports' => [
                'ATV', 'Motorcycle', 'MPV', 'Powersports', 'Scooter',
                'Snowmobile', 'Watercraft',
            ],
            'Recreational Vehicles' => [
                'Class A', 'Class B', 'Class C', 'Destination Trailer', 'Expandables',
                'Fifth Wheel', 'Motor Home Class A', 'Motor Home Class A - Diesel',
                'Motor Home Class B', 'Motor Home Class B - Diesel',
                'Motor Home Class C', 'Pop-Up', 'Super C', 'Teardrop Trailers',
                'Toy Hauler', 'Travel Trailer',
            ],
            'Trailers' => [
                'BBQ trailer', 'Camper', 'Cargo trailer', 'Concession trailer',
                'Dump trailer', 'Dump Trailers', 'Enclosed trailer',
                'Equipment trailer', 'Folding Pop-Up Camper', 'Pop Up',
                'Race trailer', 'Refrigerated trailer', 'Roll off trailer',
                'Tag-Along Trailers', 'Teardrop Trailer', 'Towable RV',
                'Toy Hauler Expandable', 'Toy Hauler Fifth Wheel', 'Trailer',
                'Vending trailer',
            ],
            'Other' => [
                'Aircraft', 'Attachment/Implement', 'Auger Attachments', 'Generator',
                'Generators', 'Golf cart', 'Lawn & Landscape', 'Light Compaction',
                'Loader Accessories', 'Loader Attachments', 'Parts for sale',
                'Pumps & Hoses', 'Tractor Attachments', 'Unknown',
            ],
        ];

        foreach ($data as $groupName => $types) {
            $group = DB::table('body_type_groups')->where('name', $groupName)->first();
            if (!$group) continue;

            foreach ($types as $typeName) {
                DB::table('body_types')->insertOrIgnore([
                    'body_type_group_id' => $group->id,
                    'name'               => $typeName,
                    'slug'               => Str::slug($typeName),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
    }
}
