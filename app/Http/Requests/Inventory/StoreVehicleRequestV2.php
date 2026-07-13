<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequestV2 extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dealerId = $this->user()->current_dealer_id;

        return [
            // ── Core ───────────────────────────────────────────────────────────
            'location_id' => [
                'required', 'integer',
                "exists:locations,id,dealer_id,{$dealerId}",
            ],
            'vin' => [
                'nullable', 'string', 'size:17',
                "unique:vehicles,vin,NULL,id,dealer_id,{$dealerId}",
            ],
            'stock_number' => [
                'required', 'string', 'max:50',
                "unique:vehicles,stock_number,NULL,id,dealer_id,{$dealerId}",
            ],
            'mileage' => ['required', 'integer', 'min:0', 'max:999999'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 2)],
            'make_id' => ['required', 'integer', 'exists:makes,id'],
            'make_model_id' => ['nullable', 'integer', 'exists:make_models,id'],
            'make_model_name' => ['nullable', 'string', 'max:255'],
            'trim' => ['nullable', 'string', 'max:255'],
            'model_number' => ['nullable', 'string', 'max:50'],

            // ── Body ───────────────────────────────────────────────────────────
            'body_type_id' => ['required', 'integer', 'exists:body_types,id'],
            'body_style_id' => ['nullable', 'integer', 'exists:body_styles,id'],
            'doors' => ['nullable', 'integer', 'min:1', 'max:6'],

            // ── Condition ─────────────────────────────────────────────────────
            'vehicle_condition' => ['required', 'string', 'in:Used,New,Certified Pre-Owned'],
            'is_certified' => ['nullable', 'boolean'],
            'is_commercial' => ['nullable', 'boolean'],
            'location_status' => ['nullable', 'string', 'max:20'],

            // ── Colors ────────────────────────────────────────────────────────
            'exterior_color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'interior_color_id' => ['nullable', 'integer', 'exists:colors,id'],

            // ── Mechanical ─────────────────────────────────────────────────────
            'fuel_type_id' => ['nullable', 'integer', 'exists:fuel_types,id'],
            'transmission_type_id' => ['nullable', 'integer', 'exists:transmission_types,id'],
            'drivetrain_type_id' => ['nullable', 'integer', 'exists:drivetrain_types,id'],
            'engine_string' => ['nullable', 'string', 'max:150'],
            'engine' => ['nullable', 'string', 'max:150'],
            'seating_capacity' => ['nullable', 'integer', 'min:1', 'max:99'],

            // ── Pricing ───────────────────────────────────────────────────────
            'list_price' => ['nullable', 'numeric', 'min:0'],
            'msrp' => ['nullable', 'numeric', 'min:0'],
            'dealer_cost' => ['nullable', 'numeric', 'min:0'],

            // ── Engine specs (V1 form names + V2 VIN decode names) ────────────
            'block_type' => ['nullable', 'string', 'max:10'],
            'cylinders' => ['nullable', 'integer', 'min:1', 'max:16'],
            'engine_cylinders' => ['nullable', 'integer', 'min:1', 'max:16'],
            'displacement' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'engine_displacement_l' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'max_horsepower' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'engine_hp' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'max_horsepower_at' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'engine_hp_rpm' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'max_torque' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'torque' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'max_torque_at' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'torque_rpm' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'compression' => ['nullable', 'numeric', 'min:0', 'max:99'],
            'engine_valves' => ['nullable', 'integer', 'min:0', 'max:99'],
            'engine_model' => ['nullable', 'string', 'max:255'],

            // ── Transmission / Drivetrain ─────────────────────────────────────
            'transmission_standard' => ['nullable', 'string', 'max:50'],
            'drivetrain_standard' => ['nullable', 'string', 'max:20'],
            'gvwr' => ['nullable', 'integer', 'min:0', 'max:80000'],

            // ── Weight / Fuel economy ─────────────────────────────────────────
            'empty_weight' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'fuel_tank' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'mpg_city' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'mpg_highway' => ['nullable', 'numeric', 'min:0', 'max:999'],

            // ── Dimensions ────────────────────────────────────────────────────
            'dimension_width' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'dimension_length' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'dimension_height' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'wheelbase' => ['nullable', 'numeric', 'min:0', 'max:999'],

            // ── Axle / Tires / Wheels ─────────────────────────────────────────
            'axle_ratio' => ['nullable', 'numeric', 'min:0', 'max:99'],
            'front_tire' => ['nullable', 'string', 'max:50'],
            'rear_tire' => ['nullable', 'string', 'max:50'],
            'front_wheel' => ['nullable', 'string', 'max:50'],
            'rear_wheel' => ['nullable', 'string', 'max:50'],

            // ── Features (JSON array → vehicle_notes.key_highlights) ──────────
            'features' => ['nullable'],

            // ── Meta ──────────────────────────────────────────────────────────
            'inventory_date' => ['nullable', 'date'],
        ];
    }
}
