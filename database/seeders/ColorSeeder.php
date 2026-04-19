<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $colors = [
            ['name' => 'Black',         'standard_name' => 'Black',  'slug' => 'black',         'hex' => '#1a1a1a'],
            ['name' => 'White',         'standard_name' => 'White',  'slug' => 'white',         'hex' => '#ffffff'],
            ['name' => 'Pearl White',   'standard_name' => 'White',  'slug' => 'pearl-white',   'hex' => '#f5f5f0'],
            ['name' => 'Silver',        'standard_name' => 'Silver', 'slug' => 'silver',        'hex' => '#c0c0c0'],
            ['name' => 'Gray',          'standard_name' => 'Gray',   'slug' => 'gray',          'hex' => '#808080'],
            ['name' => 'Charcoal',      'standard_name' => 'Gray',   'slug' => 'charcoal',      'hex' => '#3c3c3c'],
            ['name' => 'Dark Gray',     'standard_name' => 'Gray',   'slug' => 'dark-gray',     'hex' => '#555555'],
            ['name' => 'Red',           'standard_name' => 'Red',    'slug' => 'red',           'hex' => '#cc0000'],
            ['name' => 'Maroon',        'standard_name' => 'Red',    'slug' => 'maroon',        'hex' => '#6b0000'],
            ['name' => 'Burgundy',      'standard_name' => 'Red',    'slug' => 'burgundy',      'hex' => '#800020'],
            ['name' => 'Blue',          'standard_name' => 'Blue',   'slug' => 'blue',          'hex' => '#1a4f8a'],
            ['name' => 'Navy Blue',     'standard_name' => 'Blue',   'slug' => 'navy-blue',     'hex' => '#001f5b'],
            ['name' => 'Dark Blue',     'standard_name' => 'Blue',   'slug' => 'dark-blue',     'hex' => '#00308f'],
            ['name' => 'Midnight Blue', 'standard_name' => 'Blue',   'slug' => 'midnight-blue', 'hex' => '#191970'],
            ['name' => 'Light Blue',    'standard_name' => 'Blue',   'slug' => 'light-blue',    'hex' => '#5b9bd5'],
            ['name' => 'Green',         'standard_name' => 'Green',  'slug' => 'green',         'hex' => '#2d6a2d'],
            ['name' => 'Dark Green',    'standard_name' => 'Green',  'slug' => 'dark-green',    'hex' => '#1a3d1a'],
            ['name' => 'Teal',          'standard_name' => 'Green',  'slug' => 'teal',          'hex' => '#008080'],
            ['name' => 'Brown',         'standard_name' => 'Brown',  'slug' => 'brown',         'hex' => '#6b3a2a'],
            ['name' => 'Bronze',        'standard_name' => 'Brown',  'slug' => 'bronze',        'hex' => '#8c5a2a'],
            ['name' => 'Beige',         'standard_name' => 'Beige',  'slug' => 'beige',         'hex' => '#d4c5a9'],
            ['name' => 'Tan',           'standard_name' => 'Beige',  'slug' => 'tan',           'hex' => '#c4a882'],
            ['name' => 'Champagne',     'standard_name' => 'Beige',  'slug' => 'champagne',     'hex' => '#e8d5a3'],
            ['name' => 'Cream',         'standard_name' => 'Beige',  'slug' => 'cream',         'hex' => '#fffdd0'],
            ['name' => 'Gold',          'standard_name' => 'Gold',   'slug' => 'gold',          'hex' => '#c5a028'],
            ['name' => 'Rose Gold',     'standard_name' => 'Gold',   'slug' => 'rose-gold',     'hex' => '#c8a090'],
            ['name' => 'Orange',        'standard_name' => 'Orange', 'slug' => 'orange',        'hex' => '#e05c00'],
            ['name' => 'Yellow',        'standard_name' => 'Yellow', 'slug' => 'yellow',        'hex' => '#f0c800'],
            ['name' => 'Purple',        'standard_name' => 'Purple', 'slug' => 'purple',        'hex' => '#5c2d8f'],
            ['name' => 'Other',         'standard_name' => 'Other',  'slug' => 'other',         'hex' => null],
        ];

        foreach ($colors as $color) {
            DB::table('colors')->insertOrIgnore([
                'name'          => $color['name'],
                'standard_name' => $color['standard_name'],
                'slug'          => $color['slug'],
                'hex'           => $color['hex'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
