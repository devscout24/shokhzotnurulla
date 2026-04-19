<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use Illuminate\Support\Facades\DB;

class UpdateDetailsAction
{
    // Vehicle table fields — sirf ye vehicles table mein jayenge
    private const VEHICLE_FIELDS = [
        'stock_number', 'vin', 'model_number', 'year', 'make_id',
        'make_model_id', 'trim', 'body_type_id', 'body_style_id',
        'vehicle_condition', 'is_certified', 'is_commercial',
        'location_status', 'fuel_type_id', 'transmission_type_id',
        'drivetrain_type_id', 'engine', 'mileage', 'exterior_color_id',
        'interior_color_id', 'doors', 'seating_capacity', 'inventory_date',
    ];

    public function __invoke(Vehicle $vehicle, array $data): void
    {
        DB::transaction(function () use ($vehicle, $data) {

            $vehicle->update(
                array_intersect_key($data, array_flip(self::VEHICLE_FIELDS))
            );

            $vehicle->specs()->updateOrCreate(
                ['vehicle_id' => $vehicle->id],
                $data
            );
        });
    }
}