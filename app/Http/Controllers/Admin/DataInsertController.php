<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DataInsertController extends Controller
{
    public function makeModelInsert()
    {
        $data = [
            'Toyota'        => ['Camry', 'Corolla', 'RAV4', 'Highlander', 'Tacoma', 'Tundra', 'Sienna', '4Runner', 'Prius', 'Avalon', 'C-HR', 'Venza', 'Sequoia', 'Land Cruiser', 'GR86', 'Supra', 'bZ4X', 'Corolla Cross', 'Crown', 'Celica', 'Yaris'],
            'Honda'         => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Odyssey', 'HR-V', 'Ridgeline', 'Passport', 'Insight', 'Prologue', 'Fit', 'Element', 'S2000', 'Crosstour'],
            'Ford'          => ['F-150', 'Mustang', 'Explorer', 'Escape', 'Edge', 'Expedition', 'Bronco', 'Taurus', 'Focus', 'Fusion', 'Maverick', 'Ranger', 'Transit', 'F-250', 'F-350', 'F-450', 'EcoSport', 'Lightning', 'Fiesta', 'Flex', 'C-Max', 'Crown Victoria'],
            'Chevrolet'     => ['Silverado', 'Equinox', 'Traverse', 'Tahoe', 'Suburban', 'Malibu', 'Camaro', 'Colorado', 'Blazer', 'Trailblazer', 'Corvette', 'Trax', 'Express', 'Cruze', 'Impala', 'Volt', 'Sonic', 'Spark', 'Avalanche'],
            'GMC'           => ['Sierra', 'Terrain', 'Acadia', 'Yukon', 'Canyon', 'Envoy', 'Hummer EV', 'Savana', 'Safari', 'Jimmy'],
            'Dodge'         => ['Charger', 'Challenger', 'Durango', 'Grand Caravan', 'Journey', 'Dart', 'Neon', 'Viper', 'Hornet'],
            'Ram'           => ['1500', '2500', '3500', '4500', '5500', 'ProMaster', 'ProMaster City', 'Dakota', 'Rampage'],
            'Jeep'          => ['Wrangler', 'Grand Cherokee', 'Cherokee', 'Compass', 'Renegade', 'Gladiator', 'Wagoneer', 'Grand Wagoneer', 'Patriot', 'Liberty', 'Commander'],
            'Chrysler'      => ['300', 'Pacifica', 'Voyager', '200', 'Crossfire', 'Town & Country', 'PT Cruiser'],
            'Nissan'        => ['Altima', 'Sentra', 'Maxima', 'Rogue', 'Murano', 'Pathfinder', 'Frontier', 'Titan', 'Armada', 'Kicks', 'Versa', 'Leaf', 'Ariya', '370Z', '350Z', 'Z', 'Xterra', 'Juke', 'Quest'],
            'Hyundai'       => ['Sonata', 'Elantra', 'Tucson', 'Santa Fe', 'Palisade', 'Kona', 'Venue', 'Ioniq 5', 'Ioniq 6', 'Santa Cruz', 'Accent', 'Veloster', 'Genesis Coupe', 'Tiburon', 'Ioniq'],
            'Kia'           => ['K5', 'Forte', 'Sportage', 'Sorento', 'Telluride', 'Soul', 'Stinger', 'Carnival', 'EV6', 'Niro', 'Seltos', 'Rio', 'Optima', 'Sedona', 'Cadenza', 'EV9'],
            'Subaru'        => ['Outback', 'Forester', 'Impreza', 'Legacy', 'Crosstrek', 'Ascent', 'WRX', 'BRZ', 'Solterra', 'Baja', 'Tribeca', 'XV Crosstrek'],
            'Mazda'         => ['Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'CX-30', 'MX-5 Miata', 'CX-50', 'CX-90', 'CX-3', 'Mazda2', 'CX-7', 'RX-8', 'RX-7'],
            'Volkswagen'    => ['Jetta', 'Passat', 'Golf', 'Tiguan', 'Atlas', 'Taos', 'ID.4', 'Arteon', 'Beetle', 'GTI', 'Golf R', 'CC', 'Touareg', 'ID.Buzz'],
            'BMW'           => ['3 Series', '5 Series', '7 Series', 'X1', 'X3', 'X5', 'X7', '4 Series', '2 Series', 'M3', 'M5', 'i4', 'iX', 'i7', 'M8', '8 Series', '6 Series', 'X4', 'X6', 'Z4', 'i3', 'i8'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE', 'GLS', 'A-Class', 'CLA', 'AMG GT', 'EQS', 'EQE', 'G-Class', 'GLA', 'GLB', 'SL-Class', 'CLS', 'SLK', 'Metris', 'Sprinter', 'EQB'],
            'Audi'          => ['A3', 'A4', 'A6', 'A8', 'Q3', 'Q5', 'Q7', 'Q8', 'e-tron', 'RS6', 'TT', 'Q4 e-tron', 'R8', 'A5', 'A7', 'Q5 Sportback', 'e-tron GT'],
            'Lexus'         => ['ES', 'IS', 'GS', 'LS', 'NX', 'RX', 'GX', 'LX', 'UX', 'LC', 'RC', 'RZ', 'TX', 'CT', 'SC', 'LFA'],
            'Acura'         => ['TLX', 'RDX', 'MDX', 'ILX', 'NSX', 'Integra', 'TSX', 'TL', 'RLX', 'ZDX'],
            'INFINITI'      => ['Q50', 'Q60', 'QX50', 'QX60', 'QX80', 'QX55', 'G35', 'G37', 'FX35', 'QX30'],
            'Cadillac'      => ['CT4', 'CT5', 'XT4', 'XT5', 'XT6', 'Escalade', 'Lyriq', 'ATS', 'CTS', 'XTS', 'SRX', 'DTS', 'Celestiq'],
            'Buick'         => ['Encore', 'Encore GX', 'Enclave', 'Envision', 'Envista', 'Regal', 'LaCrosse', 'Verano', 'LeSabre'],
            'Lincoln'       => ['Corsair', 'Nautilus', 'Aviator', 'Navigator', 'MKZ', 'MKX', 'MKS', 'Continental'],
            'Volvo'         => ['S60', 'S90', 'V60', 'XC40', 'XC60', 'XC90', 'C40', 'V90', 'C30', 'V40', 'EX30', 'EX90'],
            'Land Rover'    => ['Defender', 'Discovery', 'Discovery Sport', 'Range Rover', 'Range Rover Sport', 'Range Rover Evoque', 'Range Rover Velar', 'LR4', 'LR2', 'Freelander'],
            'Jaguar'        => ['XE', 'XF', 'XJ', 'F-Type', 'E-Pace', 'F-Pace', 'I-Pace', 'XK', 'S-Type', 'X-Type'],
            'Porsche'       => ['911', 'Cayenne', 'Macan', 'Panamera', 'Taycan', '718 Boxster', '718 Cayman', '918 Spyder', 'Carrera GT', '928'],
            'Tesla'         => ['Model 3', 'Model S', 'Model X', 'Model Y', 'Cybertruck', 'Roadster'],
            'Genesis'       => ['G70', 'G80', 'G90', 'GV70', 'GV80', 'GV60', 'GV90'],
            'Mitsubishi'    => ['Outlander', 'Eclipse Cross', 'Galant', 'Lancer', 'Outlander Sport', 'Mirage', 'Montero', '3000GT', 'Eclipse'],
            'MINI'          => ['Cooper', 'Countryman', 'Clubman', 'Paceman', 'Convertible', 'Hardtop', 'Coupe', 'Roadster'],
            'Fiat'          => ['500', '500X', '500L', '500e', '124 Spider', 'Panda'],
            'Maserati'      => ['Ghibli', 'Quattroporte', 'GranTurismo', 'Levante', 'Grecale', 'MC20', 'Spyder'],
            'Lamborghini'   => ['Huracan', 'Urus', 'Revuelto', 'Aventador', 'Gallardo', 'Murcielago', 'Diablo', 'Temerario'],
            'Ferrari'       => ['Roma', 'F8 Tributo', 'SF90 Stradale', '488', 'Purosangue', '458 Italia', 'LaFerrari', 'Portofino', '812 Superfast', '296 GTB'],
            'Rolls-Royce'   => ['Ghost', 'Wraith', 'Cullinan', 'Spectre', 'Phantom', 'Dawn', 'Drophead Coupé', 'Silver Shadow'],
            'Bentley'       => ['Bentayga', 'Continental GT', 'Flying Spur', 'Mulsanne', 'Arnage', 'Azure', 'Continental'],
            'Rivian'        => ['R1T', 'R1S', 'R2', 'R3', 'R3X'],
            'Polestar'      => ['Polestar 2', 'Polestar 3', 'Polestar 4', 'Polestar 1', 'Polestar 5', 'Polestar 6'],
            'Lucid'         => ['Air', 'Gravity', 'Earth'],
            'Wagoneer'      => ['Wagoneer', 'Grand Wagoneer'],
        ];

        try {
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
        } catch (Exception $e) {
            return back()->with('error', 'Make and model data insertion failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Make and model data inserted successfully');
    }
}
