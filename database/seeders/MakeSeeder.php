<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MakeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $makes = [
            'Acura', 'Alfa Romeo', 'Arctic Cat', 'Aston Martin', 'Audi',
            'Bentley', 'BMW', 'Braun', 'Buick', 'Cadillac',
            'Can-Am', 'Chevrolet', 'Chrysler', 'Coachmen', 'Dodge',
            'Ducati', 'Ferrari', 'Fiat', 'Fisker', 'Ford',
            'Forest River', 'Freightliner', 'Genesis', 'GMC', 'Harley-Davidson',
            'Honda', 'Hummer', 'Husqvarna', 'Hyundai', 'INFINITI',
            'INTERNATIONAL', 'Isuzu', 'Jaguar', 'Jayco', 'Jeep',
            'John Deere', 'Kawasaki', 'Kenworth', 'Kia', 'KTM',
            'Lamborghini', 'Land Rover', 'Lexus', 'Lincoln', 'Lotus',
            'Lucid', 'Mack', 'Maserati', 'Mazda', 'McLaren',
            'Mercedes-Benz', 'Mercury', 'MINI', 'Mitsubishi', 'Nissan',
            'Oldsmobile', 'Peterbilt', 'Plymouth', 'POLARIS', 'Polestar',
            'Pontiac', 'Porsche', 'Ram', 'Rivian', 'Rolls-Royce',
            'Saab', 'Saturn', 'Scion', 'smart', 'Subaru',
            'Suzuki', 'Tesla', 'Toyota', 'Volkswagen', 'Volvo',
            'Wagoneer', 'YAMAHA',
        ];

        foreach ($makes as $make) {
            DB::table('makes')->insertOrIgnore([
                'name'       => $make,
                'slug'       => Str::slug($make),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
