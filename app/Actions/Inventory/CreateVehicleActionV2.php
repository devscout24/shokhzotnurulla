<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehicleNote;
use App\Models\Inventory\VehiclePrice;
use App\Models\Inventory\VehicleSpec;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateVehicleActionV2
{
    public function __invoke(Dealer $dealer, array $data): Vehicle
    {
        return DB::transaction(function () use ($dealer, $data) {

            $vehicle = Vehicle::create([
                'dealer_id'            => $dealer->id,
                'location_id'          => $data['location_id'],
                'ulid'                 => Str::ulid(),
                'stock_number'         => $data['stock_number'],
                'mileage'              => $data['mileage'],
                'vin'                  => $data['vin']                  ?? null,
                'model_number'         => $data['model_number']         ?? null,
                'year'                 => $data['year'],
                'make_id'              => $data['make_id'],
                'make_model_id'        => $data['make_model_id'],
                'trim'                 => $data['trim']                 ?? null,
                'body_type_id'         => $data['body_type_id'],
                'body_style_id'        => $data['body_style_id']        ?? null,
                'vehicle_condition'    => $data['vehicle_condition'],
                'is_certified'         => $data['is_certified']         ?? false,
                'is_commercial'        => $data['is_commercial']        ?? false,
                'location_status'      => $data['location_status']      ?? 'lot',
                'fuel_type_id'         => $data['fuel_type_id']             ?? null,
                'transmission_type_id' => $data['transmission_type_id']     ?? null,
                'drivetrain_type_id'   => $data['drivetrain_type_id']       ?? null,
                'engine'               => $data['engine_string']            ?? $data['engine'] ?? null,
                'exterior_color_id'    => $data['exterior_color_id']        ?? null,
                'interior_color_id'    => $data['interior_color_id']        ?? null,
                'doors'                => $data['doors']                    ?? null,
                'seating_capacity'     => $data['seating_capacity']         ?? null,
                'list_price'           => $data['msrp']                     ?? $data['list_price'] ?? null,
                'status'               => 'draft',
                'source'               => 'manual',
                'inventory_date'       => $data['inventory_date']       ?? now()->toDateString(),
                'listed_at'            => now(),
            ]);

            VehiclePrice::create([
                'vehicle_id'  => $vehicle->id,
                'msrp'        => $data['msrp']         ?? null,
                'dealer_cost' => $data['dealer_cost']  ?? null,
            ]);

            VehicleSpec::create([
                'vehicle_id'            => $vehicle->id,
                'block_type'            => $data['block_type']            ?? null,
                'cylinders'             => $data['engine_cylinders']      ?? null,
                'displacement'          => $data['engine_displacement_l'] ?? null,
                'max_horsepower'        => $data['engine_hp']             ?? null,
                'max_horsepower_at'     => $data['engine_hp_rpm']         ?? null,
                'max_torque'            => $data['torque']                ?? null,
                'max_torque_at'         => $data['torque_rpm']            ?? null,
                'transmission_standard' => $data['transmission_standard'] ?? null,
                'drivetrain_standard'   => $data['drivetrain_standard']   ?? null,
                'gvwr'                  => $data['gvwr']                  ?? null,
                'empty_weight'          => $data['empty_weight']          ?? null,
                'fuel_tank'             => $data['fuel_tank']             ?? null,
                'mpg_city'              => $data['mpg_city']              ?? null,
                'mpg_highway'           => $data['mpg_highway']           ?? null,
                'dimension_width'       => $data['dimension_width']       ?? null,
                'dimension_length'      => $data['dimension_length']      ?? null,
                'dimension_height'      => $data['dimension_height']      ?? null,
                'wheelbase'             => $data['wheelbase']             ?? null,
                'axle_ratio'            => $data['axle_ratio']            ?? null,
                'front_tire'            => $data['front_tire']            ?? null,
                'rear_tire'             => $data['rear_tire']             ?? null,
                'front_wheel'           => $data['front_wheel']           ?? null,
                'rear_wheel'            => $data['rear_wheel']            ?? null,
            ]);

            VehicleNote::create([
                'vehicle_id'    => $vehicle->id,
                'key_highlights' => $data['features'] ?? [],
            ]);

            return $vehicle;
        });
    }
}
