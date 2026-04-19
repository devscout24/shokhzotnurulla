<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FactoryOptionSeeder extends Seeder
{
    public function run(): void
    {
        // Structure: category_name => [ group_name => [ [option_key, label, sub_label] ] ]
        // sub_label = null means direct option (no nesting)
        // sub_label = string means nested under that sub-label
        $data = [

            'Entertainment and Technology' => [
                'Audio System' => [
                    ['option_key' => 'auxiliaryaudioinput_bluetooth',        'label' => 'Bluetooth',                                    'sub_label' => 'Auxiliary audio input'],
                    ['option_key' => 'auxiliaryaudioinput_ipod',             'label' => 'IPod/iPhone',                                  'sub_label' => 'Auxiliary audio input'],
                    ['option_key' => 'auxiliaryaudioinput_jack',             'label' => 'Jack',                                         'sub_label' => 'Auxiliary audio input'],
                    ['option_key' => 'auxiliaryaudioinput_usb',              'label' => 'USB',                                          'sub_label' => 'Auxiliary audio input'],
                    ['option_key' => 'in-dashcd_singledisc',                 'label' => 'Single Disc In-Dash CD',                       'sub_label' => null],
                    ['option_key' => 'radio_amfm',                           'label' => 'AM/FM',                                        'sub_label' => 'Radio'],
                    ['option_key' => 'radio_touchscreen',                    'label' => 'Touch Screen Display',                         'sub_label' => 'Radio'],
                    ['option_key' => 'radio_voiceoperated',                  'label' => 'Voice Operated',                               'sub_label' => 'Radio'],
                    ['option_key' => 'radiodatasystem',                      'label' => 'Radio data system',                            'sub_label' => null],
                    ['option_key' => 'speedsensitivevolumecontrol',          'label' => 'Speed sensitive volume control',                'sub_label' => null],
                    ['option_key' => 'totalspeakers',                        'label' => '6 Total speakers',                             'sub_label' => null],
                ],
                'In Car Entertainment' => [
                    ['option_key' => 'infotainment_entune',                  'label' => 'Entune Infotainment',                          'sub_label' => null],
                    ['option_key' => 'infotainmentscreensize',               'label' => '6.1 In. Infotainment screen size',             'sub_label' => null],
                ],
                'Telematics' => [
                    ['option_key' => 'electronicmessaging_voice',            'label' => 'Voice Operated',                               'sub_label' => 'Electronic messaging assistance'],
                    ['option_key' => 'electronicmessaging_read',             'label' => 'With Read Function',                           'sub_label' => 'Electronic messaging assistance'],
                    ['option_key' => 'hands-freephonecallintegration',       'label' => 'Voice Operated Hands-free phone call integration', 'sub_label' => null],
                    ['option_key' => 'wirelessdatalink_bluetooth',           'label' => 'Bluetooth Wireless data link',                 'sub_label' => null],
                ],
            ],

            'Exterior' => [
                'Exterior Features' => [
                    ['option_key' => 'exhausttipcolor_stainless',            'label' => 'Stainless Steel',                              'sub_label' => 'Exhaust tip color'],
                    ['option_key' => 'exhausttipcolor_black',                'label' => 'Black',                                        'sub_label' => 'Exhaust tip color'],
                    ['option_key' => 'exhausttipcolor_chrome',               'label' => 'Chrome',                                       'sub_label' => 'Exhaust tip color'],
                    ['option_key' => 'grillecolor_black',                    'label' => 'Black Grille color',                           'sub_label' => null],
                    ['option_key' => 'sidedoortype_dual',                    'label' => 'Dual Rear-hinged Access Side door',            'sub_label' => null],
                    ['option_key' => 'skidplates_front',                     'label' => 'Front Skid plate(s)',                          'sub_label' => null],
                    ['option_key' => 'windowtrim_black',                     'label' => 'Black Window trim',                            'sub_label' => null],
                    ['option_key' => 'mudguards_front',                      'label' => 'Front',                                        'sub_label' => 'Mudguards'],
                    ['option_key' => 'mudguards_rear',                       'label' => 'Rear',                                         'sub_label' => 'Mudguards'],
                    ['option_key' => 'paintprotection_clearfilm',            'label' => 'Clear Film',                                   'sub_label' => 'Paint protection'],
                    ['option_key' => 'paintprotection_dooredge',             'label' => 'Door Edge Guards',                             'sub_label' => 'Paint protection'],
                    ['option_key' => 'pickupbedextender',                    'label' => 'Pickup bed extender',                          'sub_label' => null],
                    ['option_key' => 'pickupbedlight_led',                   'label' => 'LED Pickup bed light',                         'sub_label' => null],
                    ['option_key' => 'pickupbedrubbermat',                   'label' => 'Pickup bed rubber mat',                        'sub_label' => null],
                    ['option_key' => 'pickuptonneaucover_hard',              'label' => 'Hard Pickup tonneau cover',                    'sub_label' => null],
                    ['option_key' => 'runningboardcolor_black',              'label' => 'Black',                                        'sub_label' => 'Running board color'],
                    ['option_key' => 'runningboardcolor_chrome',             'label' => 'Chrome',                                       'sub_label' => 'Running board color'],
                    ['option_key' => 'runningboards',                        'label' => 'Running boards',                               'sub_label' => null],
                ],
                'Lights' => [
                    ['option_key' => 'daytimerunninglights',                 'label' => 'Daytime running lights',                       'sub_label' => null],
                    ['option_key' => 'headlights_autodelayoff',              'label' => 'Auto Delay Off',                               'sub_label' => 'Headlights'],
                    ['option_key' => 'headlights_autohighbeam',              'label' => 'Auto High Beam Dimmer',                        'sub_label' => 'Headlights'],
                    ['option_key' => 'headlights_halogen',                   'label' => 'Halogen',                                      'sub_label' => 'Headlights'],
                ],
                'Mirrors' => [
                    ['option_key' => 'sidemirror_manualfolding',             'label' => 'Manual Folding',                               'sub_label' => 'Side mirror adjustments'],
                    ['option_key' => 'sidemirror_power',                     'label' => 'Power',                                        'sub_label' => 'Side mirror adjustments'],
                    ['option_key' => 'sidemirrors_heated',                   'label' => 'Heated Side mirrors',                          'sub_label' => null],
                ],
                'Wheels and Tires' => [
                    ['option_key' => 'sparetiresize_fullsize',               'label' => 'Full-size Spare tire size',                    'sub_label' => null],
                    ['option_key' => 'sparewheeltype_steel',                 'label' => 'Steel Spare wheel',                            'sub_label' => null],
                    ['option_key' => 'tirepressuremonitoringsystem',         'label' => 'Tire Pressure Monitoring System',              'sub_label' => null],
                    ['option_key' => 'wheels_steel',                         'label' => 'Steel',                                        'sub_label' => 'Wheels'],
                    ['option_key' => 'wheels_paintedaluminum',               'label' => 'Painted Aluminum Alloy',                       'sub_label' => 'Wheels'],
                    ['option_key' => 'wheellocks_frontandrear',              'label' => 'Front And Rear',                               'sub_label' => 'Wheel locks'],
                    ['option_key' => 'wheellocks_spareonly',                 'label' => 'Spare Only',                                   'sub_label' => 'Wheel locks'],
                ],
                'Windows' => [
                    ['option_key' => 'pickupslidingrearwindow_manual',       'label' => 'Manual Pickup sliding rear window',            'sub_label' => null],
                    ['option_key' => 'powerwindows',                         'label' => 'Power windows',                                'sub_label' => null],
                    ['option_key' => 'rearprivacyglass',                     'label' => 'Rear privacy glass',                           'sub_label' => null],
                ],
            ],

            'Interior' => [
                'Air Conditioning' => [
                    ['option_key' => 'airfiltration',                        'label' => 'Air filtration',                               'sub_label' => null],
                    ['option_key' => 'frontairconditioning',                 'label' => 'Front air conditioning',                       'sub_label' => null],
                    ['option_key' => 'frontairconditioningzones_single',     'label' => 'Single Front air conditioning zones',          'sub_label' => null],
                ],
                'Comfort Features' => [
                    ['option_key' => 'floormaterial_carpet',                 'label' => 'Carpet Floor material',                        'sub_label' => null],
                    ['option_key' => 'doorsilltrim_scuffplate',              'label' => 'Scuff Plate Door sill trim',                   'sub_label' => null],
                    ['option_key' => 'floormatmaterial_carpet',              'label' => 'Carpet',                                       'sub_label' => 'Floor mat material'],
                    ['option_key' => 'floormatmaterial_rubber',              'label' => 'Rubber/vinyl',                                 'sub_label' => 'Floor mat material'],
                    ['option_key' => 'floormats_front',                      'label' => 'Front',                                        'sub_label' => 'Floor mats'],
                    ['option_key' => 'floormats_rear',                       'label' => 'Rear',                                         'sub_label' => 'Floor mats'],
                ],
                'Convenience Features' => [
                    ['option_key' => 'adaptivecruisecontrol',                'label' => 'Adaptive cruise control',                      'sub_label' => null],
                    ['option_key' => 'centerconsole_frontarmrest',           'label' => 'Front Console With Armrest And Storage Center console', 'sub_label' => null],
                    ['option_key' => 'cupholders_front',                     'label' => 'Front',                                        'sub_label' => 'Cupholders'],
                    ['option_key' => 'cupholders_rear',                      'label' => 'Rear',                                         'sub_label' => 'Cupholders'],
                    ['option_key' => 'one-touchwindows',                     'label' => '1 One-touch windows',                          'sub_label' => null],
                    ['option_key' => 'overheadconsole_front',                'label' => 'Front Overhead console',                       'sub_label' => null],
                    ['option_key' => 'poweroutlets_12v',                     'label' => '12V Front Power outlet(s)',                    'sub_label' => null],
                    ['option_key' => 'powersteering_variable',               'label' => 'Variable/speed-proportional Power steering',   'sub_label' => null],
                    ['option_key' => 'readinglights_front',                  'label' => 'Front',                                        'sub_label' => 'Reading lights'],
                    ['option_key' => 'readinglights_rear',                   'label' => 'Rear',                                         'sub_label' => 'Reading lights'],
                    ['option_key' => 'rearviewmirror_manual',                'label' => 'Manual Day/night Rearview mirror',             'sub_label' => null],
                    ['option_key' => 'steeringwheel_tilttelescopic',         'label' => 'Tilt And Telescopic Steering wheel',           'sub_label' => null],
                    ['option_key' => 'steeringwheelcontrols_audio',          'label' => 'Audio',                                        'sub_label' => 'Steering wheel mounted controls'],
                    ['option_key' => 'steeringwheelcontrols_cruise',         'label' => 'Cruise Control',                               'sub_label' => 'Steering wheel mounted controls'],
                    ['option_key' => 'steeringwheelcontrols_multi',          'label' => 'Multi-function',                               'sub_label' => 'Steering wheel mounted controls'],
                    ['option_key' => 'steeringwheelcontrols_phone',          'label' => 'Phone',                                        'sub_label' => 'Steering wheel mounted controls'],
                    ['option_key' => 'steeringwheelcontrols_voice',          'label' => 'Voice Control',                                'sub_label' => 'Steering wheel mounted controls'],
                    ['option_key' => 'ashtray',                              'label' => 'Ashtray',                                      'sub_label' => null],
                ],
                'Instrumentation' => [
                    ['option_key' => 'externaltemperaturedisplay',           'label' => 'External temperature display',                 'sub_label' => null],
                    ['option_key' => 'fueleconomydisplay_mpg',               'label' => 'MPG',                                          'sub_label' => 'Fuel economy display'],
                    ['option_key' => 'fueleconomydisplay_range',             'label' => 'Range',                                        'sub_label' => 'Fuel economy display'],
                    ['option_key' => 'gauge_tachometer',                     'label' => 'Tachometer Gauge',                             'sub_label' => null],
                    ['option_key' => 'instrumentclusterscreensize',          'label' => '4.2 In. Instrument cluster screen size',       'sub_label' => null],
                    ['option_key' => 'multi-functiondisplay',                'label' => 'Multi-function display',                       'sub_label' => null],
                ],
                'Seats' => [
                    ['option_key' => 'frontseattype_bucket',                 'label' => 'Bucket Front seat',                            'sub_label' => null],
                    ['option_key' => 'rearseatfolding_foldsup',              'label' => 'Folds Up Rear seat folding',                   'sub_label' => null],
                    ['option_key' => 'rearseattype_jumpseats',               'label' => 'Jumpseats Rear seat',                          'sub_label' => null],
                    ['option_key' => 'upholstery_cloth',                     'label' => 'Cloth Upholstery',                             'sub_label' => null],
                ],
            ],

            'Performance' => [
                'Powertrain' => [
                    ['option_key' => 'batterysaver',                         'label' => 'Battery saver',                                'sub_label' => null],
                ],
                'Suspension' => [
                    ['option_key' => 'frontstabilizerbar_30mm',              'label' => 'Diameter 30 Mm Front stabilizer bar',          'sub_label' => null],
                    ['option_key' => 'frontstruts',                          'label' => 'Front struts',                                 'sub_label' => null],
                    ['option_key' => 'frontsuspensionclassification',        'label' => 'Independent Front suspension classification',   'sub_label' => null],
                    ['option_key' => 'frontsuspensiontype_doublewishbone',   'label' => 'Double Wishbone Front suspension',             'sub_label' => null],
                    ['option_key' => 'rearstabilizerbar',                    'label' => 'Rear stabilizer bar',                          'sub_label' => null],
                    ['option_key' => 'rearsuspensionclassification',         'label' => 'Solid Live Axle Rear suspension classification','sub_label' => null],
                ],
                'Towing and Hauling' => [
                    ['option_key' => 'pickupbedcargo_tiedown',               'label' => 'Tie-down Anchors Pickup bed cargo management', 'sub_label' => null],
                ],
            ],

            'Safety and Security' => [
                'Airbags' => [
                    ['option_key' => 'airbagdeactivation_occupant',          'label' => 'Occupant Sensing Passenger Airbag deactivation','sub_label' => null],
                    ['option_key' => 'frontairbags_dual',                    'label' => 'Dual Front airbags',                           'sub_label' => null],
                    ['option_key' => 'kneeairbags_dualfront',                'label' => 'Dual Front Knee airbags',                      'sub_label' => null],
                    ['option_key' => 'sideairbags_front',                    'label' => 'Front Side airbags',                           'sub_label' => null],
                    ['option_key' => 'sidecurtainairbags_front',             'label' => 'Front',                                        'sub_label' => 'Side curtain airbags'],
                    ['option_key' => 'sidecurtainairbags_rear',              'label' => 'Rear',                                         'sub_label' => 'Side curtain airbags'],
                ],
                'Brakes' => [
                    ['option_key' => 'abs_4wheel',                           'label' => '4-wheel ABS',                                  'sub_label' => null],
                    ['option_key' => 'brakingassist',                        'label' => 'Braking assist',                               'sub_label' => null],
                    ['option_key' => 'electronicbrakeforcedistribution',     'label' => 'Electronic brakeforce distribution',           'sub_label' => null],
                    ['option_key' => 'frontbraketype_ventilateddisc',        'label' => 'Ventilated Disc Front brake',                  'sub_label' => null],
                    ['option_key' => 'powerbrakes',                          'label' => 'Power brakes',                                 'sub_label' => null],
                    ['option_key' => 'rearbraketype_drum',                   'label' => 'Drum Rear brake',                              'sub_label' => null],
                ],
                'Safety' => [
                    ['option_key' => 'activeheadrestraints_dualfront',       'label' => 'Dual Front Active head restraints',            'sub_label' => null],
                    ['option_key' => 'automaticemergencybraking_front',      'label' => 'Front',                                        'sub_label' => 'Automatic emergency braking'],
                    ['option_key' => 'automaticemergencybraking_pedestrian', 'label' => 'Front Pedestrian',                             'sub_label' => 'Automatic emergency braking'],
                    ['option_key' => 'camerasystem_rearview',                'label' => 'Rearview Camera system',                       'sub_label' => null],
                    ['option_key' => 'childseatanchors_latch',               'label' => 'LATCH System Child seat anchors',              'sub_label' => null],
                    ['option_key' => 'impactsensor_fuelcutoff',              'label' => 'Fuel Cut-off Impact sensor',                   'sub_label' => null],
                    ['option_key' => 'lanedeviationsensors',                 'label' => 'Lane deviation sensors',                       'sub_label' => null],
                    ['option_key' => 'precollisionwarning_audible',          'label' => 'Audible Warning',                              'sub_label' => 'Pre-collision warning system'],
                    ['option_key' => 'precollisionwarning_pedestrian',       'label' => 'Pedestrian Detection',                         'sub_label' => 'Pre-collision warning system'],
                    ['option_key' => 'precollisionwarning_visual',           'label' => 'Visual Warning',                               'sub_label' => 'Pre-collision warning system'],
                    ['option_key' => 'rearviewmonitor_indash',               'label' => 'In Dash Rearview monitor',                     'sub_label' => null],
                    ['option_key' => 'firstaidkit',                          'label' => 'First aid kit',                                'sub_label' => null],
                ],
                'Seatbelts' => [
                    ['option_key' => 'emergencylockingretractors_front',     'label' => 'Front',                                        'sub_label' => 'Emergency locking retractors'],
                    ['option_key' => 'emergencylockingretractors_rear',      'label' => 'Rear',                                         'sub_label' => 'Emergency locking retractors'],
                    ['option_key' => 'seatbeltforcelimiters_front',          'label' => 'Front Seatbelt force limiters',                'sub_label' => null],
                    ['option_key' => 'seatbeltpretensioners_front',          'label' => 'Front Seatbelt pretensioners',                 'sub_label' => null],
                    ['option_key' => 'seatbeltwarningsensor_front',          'label' => 'Front Seatbelt warning sensor',                'sub_label' => null],
                ],
                'Security' => [
                    ['option_key' => 'antitheft_immobilizer',                'label' => 'Vehicle Immobilizer',                          'sub_label' => 'Anti-theft system'],
                    ['option_key' => 'antitheft_alarm',                      'label' => 'Alarm',                                        'sub_label' => 'Anti-theft system'],
                    ['option_key' => 'antitheft_glassbreakage',              'label' => 'Glass Breakage Sensor',                        'sub_label' => 'Anti-theft system'],
                    ['option_key' => 'powerdoorlocks_autolocking',           'label' => 'Auto-locking Power door locks',                'sub_label' => null],
                ],
                'Stability and Traction' => [
                    ['option_key' => 'hillholdercontrol',                    'label' => 'Hill holder control',                          'sub_label' => null],
                    ['option_key' => 'stabilitycontrol',                     'label' => 'Stability control',                            'sub_label' => null],
                    ['option_key' => 'tractioncontrol',                      'label' => 'Traction control',                             'sub_label' => null],
                ],
            ],
        ];

        foreach ($data as $categoryName => $groups) {
            $category = DB::table('factory_option_categories')
                ->where('name', $categoryName)
                ->first();

            if (!$category) continue;

            foreach ($groups as $groupName => $options) {
                $group = DB::table('factory_option_groups')
                    ->where('name', $groupName)
                    ->where('category_id', $category->id)
                    ->first();

                foreach ($options as $option) {
                    DB::table('factory_options')->insertOrIgnore([
                        'category_id' => $category->id,
                        'group_id'    => $group?->id,
                        'option_key'  => $option['option_key'],
                        'label'       => $option['label'],
                        'sub_label'   => $option['sub_label'],
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }
    }
}