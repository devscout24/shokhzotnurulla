<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicle  = $this->route('vehicle');
        $dealerId = $this->user()->current_dealer_id;

        return [
            'stock_number'         => [
                'required', 'string', 'max:50',
                "unique:vehicles,stock_number,{$vehicle->id},id,dealer_id,{$dealerId},deleted_at,NULL",
            ],
            'location_id'          => [
                'required', 'integer',
                "exists:locations,id,dealer_id,{$dealerId}",
            ],
            'vin'                  => [
                'required', 'string', 'size:17',
                "unique:vehicles,vin,{$vehicle->id},id,dealer_id,{$dealerId},deleted_at,NULL",
            ],
            'model_number'         => ['nullable', 'string', 'max:50'],
            'year'                 => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 2)],
            'make_id'              => ['required', 'integer', 'exists:makes,id'],
            // 'make_model_id'        => ['required', 'integer', 'exists:make_models,id'],
            'make_model_name'        => ['required', 'string'],
            'trim'                 => ['nullable', 'string', 'max:255'],
            'body_type_id'         => ['required', 'integer', 'exists:body_types,id'],
            'body_style_id'        => ['nullable', 'integer', 'exists:body_styles,id'],
            'vehicle_condition'    => ['required', 'string', 'in:Used,New,Certified Pre-Owned'],
            'is_certified'         => ['boolean'],
            'location_status'      => ['nullable', 'string', 'in:lot,transit,order,preorder'],
            'fuel_type_id'         => ['nullable', 'integer', 'exists:fuel_types,id'],
            'transmission_type_id' => ['nullable', 'integer', 'exists:transmission_types,id'],
            'drivetrain_type_id'   => ['nullable', 'integer', 'exists:drivetrain_types,id'],
            'engine'               => ['nullable', 'string', 'max:150'],
            'mileage'              => ['nullable', 'integer', 'min:0', 'max:999999'],
            'exterior_color_id'    => ['nullable', 'integer', 'exists:colors,id'],
            'interior_color_id'    => ['nullable', 'integer', 'exists:colors,id'],
            'doors'                => ['nullable', 'integer', 'min:1', 'max:6'],
            'seating_capacity'     => ['nullable', 'integer', 'min:1', 'max:20'],
            'inventory_date'       => ['nullable', 'date'],
            'is_commercial'        => ['boolean'],

            // ─── Specs ────────────────────────────────────────────────────────
            'aspiration'           => ['nullable', 'string', 'max:100'],
            'block_type'           => ['nullable', 'string', 'max:10'],
            'cylinders'            => ['nullable', 'integer', 'min:1', 'max:16'],
            'displacement'         => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'power_cycle'          => ['nullable', 'string', 'max:50'],
            'transmission_standard'=> ['nullable', 'string', 'max:150'],
            'drivetrain_standard'  => ['nullable', 'string', 'max:150'],
            'max_horsepower'       => ['nullable', 'integer', 'min:0', 'max:2000'],
            'max_horsepower_at'    => ['nullable', 'integer', 'min:0', 'max:20000'],
            'max_torque'           => ['nullable', 'integer', 'min:0', 'max:2000'],
            'max_torque_at'        => ['nullable', 'integer', 'min:0', 'max:20000'],
            'towing_capacity'      => ['nullable', 'integer', 'min:0', 'max:50000'],
            'payload_capacity'     => ['nullable', 'integer', 'min:0', 'max:20000'],
            'gvwr'                 => ['nullable', 'integer', 'min:0', 'max:50000'],
            'empty_weight'         => ['nullable', 'integer', 'min:0', 'max:50000'],
            'load_capacity'        => ['nullable', 'integer', 'min:0', 'max:50000'],
            'fuel_tank'            => ['nullable', 'numeric', 'min:0', 'max:100'],
            'mpg_city'             => ['nullable', 'numeric', 'min:0', 'max:150'],
            'mpg_highway'          => ['nullable', 'numeric', 'min:0', 'max:150'],
            'ev_range'             => ['nullable', 'integer', 'min:0', 'max:1000'],
            'ev_battery_capacity'  => ['nullable', 'numeric', 'min:0', 'max:500'],
            'ev_charger_rating'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'dimension_width'      => ['nullable', 'numeric', 'min:0', 'max:500'],
            'dimension_length'     => ['nullable', 'numeric', 'min:0', 'max:500'],
            'dimension_height'     => ['nullable', 'numeric', 'min:0', 'max:500'],
            'wheelbase'            => ['nullable', 'numeric', 'min:0', 'max:400'],
            'bed_length'           => ['nullable', 'numeric', 'min:0', 'max:200'],
            'axle'                 => ['nullable', 'string', 'max:100'],
            'axle_ratio'           => ['nullable', 'numeric', 'min:0', 'max:10'],
            'rear_door_gate'       => ['nullable', 'string', 'max:100'],
            'front_wheel'          => ['nullable', 'string', 'max:30'],
            'rear_wheel'           => ['nullable', 'string', 'max:30'],
            'front_tire'           => ['nullable', 'string', 'max:30'],
            'rear_tire'            => ['nullable', 'string', 'max:30'],

            // ─── Certification & Color Codes ─────────────────────────────────
            'factory_certified'    => ['nullable', 'boolean'],
            'dealer_certified'     => ['nullable', 'boolean'],
            'chrome_style_id'      => ['nullable', 'string', 'max:50'],
            'exterior_color_code'  => ['nullable', 'string', 'max:50'],
            'interior_color_code'  => ['nullable', 'string', 'max:50'],
            'interior_material'    => ['nullable', 'string', 'max:100'],
            'listed_at'            => ['nullable', 'date'],
            'expire_time'          => ['nullable', 'date'],
        ];
    }
}
