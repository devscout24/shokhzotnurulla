<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehicleNote;
use App\Models\Inventory\VehiclePrice;
use App\Models\Inventory\VehicleSpec;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateVehicleAction
{
    public function __invoke(Dealer $dealer, array $data): Vehicle
    {
        return DB::transaction(function () use ($dealer, $data) {

            $vehicle = Vehicle::create([
                'dealer_id'            => $dealer->id,
                'ulid'                 => Str::ulid(),
                'stock_number'         => $data['stock_number'],
                'mileage'              => $data['mileage'],
                'vin'                  => $data['vin']              ?? null,
                'model_number'         => $data['model_number']     ?? null,
                'year'                 => $data['year'],
                'make_id'              => $data['make_id'],
                'make_model_id'        => $data['make_model_id'],
                'trim'                 => $data['trim']             ?? null,
                'body_type_id'         => $data['body_type_id'],
                'body_style_id'        => $data['body_style_id']    ?? null,
                'vehicle_condition'    => $data['vehicle_condition'],
                'is_certified'         => $data['is_certified']     ?? false,
                'is_commercial'        => $data['is_commercial']    ?? false,
                'location_status'      => $data['location_status']  ?? 'lot',
                'fuel_type_id'         => $data['fuel_type_id']         ?? null,
                'transmission_type_id' => $data['transmission_type_id'] ?? null,
                'drivetrain_type_id'   => $data['drivetrain_type_id']   ?? null,
                'engine'               => $data['engine']               ?? null,
                'mileage'              => $data['mileage']              ?? null,
                'exterior_color_id'    => $data['exterior_color_id']    ?? null,
                'interior_color_id'    => $data['interior_color_id']    ?? null,
                'doors'                => $data['doors']                ?? null,
                'seating_capacity'     => $data['seating_capacity']     ?? null,
                'list_price'           => $data['list_price']           ?? null,
                'status'               => 'draft',
                'source'               => 'manual',
                'inventory_date'       => $data['inventory_date']       ?? now()->toDateString(),
                'listed_at'            => now(),
            ]);

            // Always create VehiclePrice — avoids null checks downstream
            VehiclePrice::create(['vehicle_id' => $vehicle->id]);

            // VehicleSpec — seed with VIN decode data when available.
            // Remaining fields left null, dealer fills them on the VDP Details tab.
            VehicleSpec::create([
                'vehicle_id'            => $vehicle->id,
                'block_type'            => $data['block_type']            ?? null,
                'cylinders'             => $data['cylinders']             ?? null,
                'displacement'          => $data['displacement']          ?? null,
                'max_horsepower'        => $data['max_horsepower']        ?? null,
                'transmission_standard' => $data['transmission_standard'] ?? null,
                'drivetrain_standard'   => $data['drivetrain_standard']   ?? null,
                'gvwr'                  => $data['gvwr']                  ?? null,
            ]);

            // Always create VehicleNote — avoids null checks downstream
            VehicleNote::create(['vehicle_id' => $vehicle->id]);

            return $vehicle;
        });
    }
}
