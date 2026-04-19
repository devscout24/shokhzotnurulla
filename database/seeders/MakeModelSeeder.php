<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Catalog\Make;

class MakeModelSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $data = [
            'Toyota'        => ['Camry', 'Corolla', 'RAV4', 'Highlander', 'Tacoma', 'Tundra', 'Sienna', '4Runner', 'Prius', 'Avalon', 'C-HR', 'Venza', 'Sequoia', 'Land Cruiser', 'GR86', 'Supra', 'bZ4X'],
            'Honda'         => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Odyssey', 'HR-V', 'Ridgeline', 'Passport', 'Insight', 'Prologue'],
            'Ford'          => ['F-150', 'Mustang', 'Explorer', 'Escape', 'Edge', 'Expedition', 'Bronco', 'Maverick', 'Ranger', 'Transit', 'F-250', 'F-350', 'F-450', 'EcoSport', 'Lightning'],
            'Chevrolet'     => ['Silverado', 'Equinox', 'Traverse', 'Tahoe', 'Suburban', 'Malibu', 'Camaro', 'Colorado', 'Blazer', 'Trailblazer', 'Corvette', 'Trax', 'Express'],
            'GMC'           => ['Sierra', 'Terrain', 'Acadia', 'Yukon', 'Canyon', 'Envoy', 'Hummer EV'],
            'Dodge'         => ['Charger', 'Challenger', 'Durango', 'Grand Caravan', 'Journey'],
            'Ram'           => ['1500', '2500', '3500', '4500', '5500', 'ProMaster', 'ProMaster City'],
            'Jeep'          => ['Wrangler', 'Grand Cherokee', 'Cherokee', 'Compass', 'Renegade', 'Gladiator', 'Wagoneer', 'Grand Wagoneer'],
            'Chrysler'      => ['300', 'Pacifica', 'Voyager'],
            'Nissan'        => ['Altima', 'Sentra', 'Maxima', 'Rogue', 'Murano', 'Pathfinder', 'Frontier', 'Titan', 'Armada', 'Kicks', 'Versa', 'Leaf', 'Ariya'],
            'Hyundai'       => ['Sonata', 'Elantra', 'Tucson', 'Santa Fe', 'Palisade', 'Kona', 'Venue', 'Ioniq 5', 'Ioniq 6', 'Santa Cruz'],
            'Kia'           => ['K5', 'Forte', 'Sportage', 'Sorento', 'Telluride', 'Soul', 'Stinger', 'Carnival', 'EV6', 'Niro', 'Seltos'],
            'Subaru'        => ['Outback', 'Forester', 'Impreza', 'Legacy', 'Crosstrek', 'Ascent', 'WRX', 'BRZ', 'Solterra'],
            'Mazda'         => ['Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'CX-30', 'MX-5 Miata', 'CX-50', 'CX-90'],
            'Volkswagen'    => ['Jetta', 'Passat', 'Golf', 'Tiguan', 'Atlas', 'Taos', 'ID.4', 'Arteon'],
            'BMW'           => ['3 Series', '5 Series', '7 Series', 'X1', 'X3', 'X5', 'X7', '4 Series', '2 Series', 'M3', 'M5', 'i4', 'iX', 'i7', 'M8'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE', 'GLS', 'A-Class', 'CLA', 'AMG GT', 'EQS', 'EQE', 'G-Class', 'GLA', 'GLB'],
            'Audi'          => ['A3', 'A4', 'A6', 'A8', 'Q3', 'Q5', 'Q7', 'Q8', 'e-tron', 'RS6', 'TT', 'Q4 e-tron'],
            'Lexus'         => ['ES', 'IS', 'GS', 'LS', 'NX', 'RX', 'GX', 'LX', 'UX', 'LC', 'RC', 'RZ'],
            'Acura'         => ['TLX', 'RDX', 'MDX', 'ILX', 'NSX', 'Integra'],
            'INFINITI'      => ['Q50', 'Q60', 'QX50', 'QX60', 'QX80', 'QX55'],
            'Cadillac'      => ['CT4', 'CT5', 'XT4', 'XT5', 'XT6', 'Escalade', 'Lyriq'],
            'Buick'         => ['Encore', 'Encore GX', 'Enclave', 'Envision', 'Envista'],
            'Lincoln'       => ['Corsair', 'Nautilus', 'Aviator', 'Navigator'],
            'Volvo'         => ['S60', 'S90', 'V60', 'XC40', 'XC60', 'XC90', 'C40'],
            'Land Rover'    => ['Defender', 'Discovery', 'Discovery Sport', 'Range Rover', 'Range Rover Sport', 'Range Rover Evoque', 'Range Rover Velar'],
            'Jaguar'        => ['XE', 'XF', 'XJ', 'F-Type', 'E-Pace', 'F-Pace', 'I-Pace'],
            'Porsche'       => ['911', 'Cayenne', 'Macan', 'Panamera', 'Taycan', '718 Boxster', '718 Cayman'],
            'Tesla'         => ['Model 3', 'Model S', 'Model X', 'Model Y', 'Cybertruck'],
            'Genesis'       => ['G70', 'G80', 'G90', 'GV70', 'GV80', 'GV60'],
            'Mitsubishi'    => ['Outlander', 'Eclipse Cross', 'Galant', 'Lancer', 'Outlander Sport'],
            'MINI'          => ['Cooper', 'Countryman', 'Clubman', 'Paceman', 'Convertible'],
            'Fiat'          => ['500', '500X', '500L', '500e'],
            'Maserati'      => ['Ghibli', 'Quattroporte', 'GranTurismo', 'Levante', 'Grecale'],
            'Lamborghini'   => ['Huracan', 'Urus', 'Revuelto'],
            'Ferrari'       => ['Roma', 'F8 Tributo', 'SF90 Stradale', '488', 'Purosangue'],
            'Rolls-Royce'   => ['Ghost', 'Wraith', 'Cullinan', 'Spectre', 'Phantom'],
            'Bentley'       => ['Bentayga', 'Continental GT', 'Flying Spur', 'Mulsanne'],
            'Rivian'        => ['R1T', 'R1S'],
            'Polestar'      => ['Polestar 2', 'Polestar 3', 'Polestar 4'],
            'Lucid'         => ['Air', 'Gravity'],
            'Wagoneer'      => ['Wagoneer', 'Grand Wagoneer'],
            'Ram'           => ['1500', '2500', '3500', 'ProMaster', 'ProMaster City'],
        ];

        foreach ($data as $makeName => $models) {
            $make = Make::where('name', $makeName)->first();
            if (!$make) continue;

            foreach ($models as $modelName) {
                $slug = Str::slug($make->slug . '-' . $modelName);
                DB::table('make_models')->insertOrIgnore([
                    'make_id'    => $make->id,
                    'name'       => $modelName,
                    'slug'       => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
