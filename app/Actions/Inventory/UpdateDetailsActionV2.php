<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehicleNote;
use App\Models\Inventory\VehiclePrice;
use App\Models\Inventory\VehicleSpec;
use Illuminate\Support\Facades\DB;

class UpdateDetailsActionV2
{
    public function __construct(
        private readonly ExtractPremiumOptionsAction $extractPremiumOptions,
    ) {}

    private const VEHICLE_FIELDS = [
        'stock_number', 'vin', 'model_number', 'year', 'make_id',
        'make_model_id',
        'trim', 'body_type_id', 'body_style_id',
        'vehicle_condition', 'is_certified', 'is_commercial',
        'location_status', 'fuel_type_id', 'transmission_type_id',
        'drivetrain_type_id', 'engine', 'mileage', 'exterior_color_id',
        'interior_color_id', 'doors', 'seating_capacity', 'inventory_date',
        'location_id', 'expire_time', 'listed_at',
        'list_price', 'original_price',
    ];

    public function __invoke(Vehicle $vehicle, array $data): void
    {
        DB::transaction(function () use ($vehicle, $data) {

            $vehicle->update(
                array_intersect_key($data, array_flip(self::VEHICLE_FIELDS))
            );

            VehicleSpec::updateOrCreate(
                ['vehicle_id' => $vehicle->id],
                array_merge(
                    array_intersect_key($data, array_flip([
                        'block_type', 'transmission_standard', 'drivetrain_standard',
                        'gvwr', 'empty_weight', 'fuel_tank', 'mpg_city', 'mpg_highway',
                        'dimension_width', 'dimension_length', 'dimension_height',
                        'wheelbase', 'axle_ratio', 'front_tire', 'rear_tire',
                        'front_wheel', 'rear_wheel',
                        'aspiration', 'power_cycle', 'towing_capacity',
                        'payload_capacity', 'load_capacity', 'ev_range',
                        'ev_battery_capacity', 'ev_charger_rating', 'bed_length',
                        'axle', 'rear_door_gate',
                    ])),
                    $this->resolveSpecField($data, 'cylinders', 'engine_cylinders'),
                    $this->resolveSpecField($data, 'displacement', 'engine_displacement_l'),
                    $this->resolveSpecField($data, 'max_horsepower', 'engine_hp'),
                    $this->resolveSpecField($data, 'max_horsepower_at', 'engine_hp_rpm'),
                    $this->resolveSpecField($data, 'max_torque', 'torque'),
                    $this->resolveSpecField($data, 'max_torque_at', 'torque_rpm'),
                )
            );

            if (isset($data['msrp']) || isset($data['dealer_cost'])) {
                VehiclePrice::updateOrCreate(
                    ['vehicle_id' => $vehicle->id],
                    array_filter([
                        'msrp' => $data['msrp'] ?? null,
                        'dealer_cost' => $data['dealer_cost'] ?? null,
                    ], fn ($v) => $v !== null)
                );
            }

            if (isset($data['features'])) {
                $features = $data['features'];
                if (is_string($features)) {
                    $decoded = json_decode($features, true);
                    $features = is_array($decoded) ? $decoded : [];
                }

                VehicleNote::updateOrCreate(
                    ['vehicle_id' => $vehicle->id],
                    ['key_highlights' => $features]
                );
            }
        });

        ($this->extractPremiumOptions)($vehicle);
    }

    private function resolveSpecField(array $data, string $v1Key, string $v2Key): array
    {
        if (array_key_exists($v1Key, $data)) {
            return [$v1Key => $data[$v1Key]];
        }
        if (array_key_exists($v2Key, $data)) {
            return [$v1Key => $data[$v2Key]];
        }

        return [];
    }
}
