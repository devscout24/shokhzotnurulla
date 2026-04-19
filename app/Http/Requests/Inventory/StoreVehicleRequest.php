<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dealerId = $this->user()->current_dealer_id;

        return [
            // ── Core identity ──────────────────────────────────────────────────
            'vin'               => [
                'nullable', 'string', 'size:17',
                "unique:vehicles,vin,NULL,id,dealer_id,{$dealerId},deleted_at,NULL",
            ],
            'stock_number'      => [
                'required', 'string', 'max:50',
                "unique:vehicles,stock_number,NULL,id,dealer_id,{$dealerId},deleted_at,NULL",
            ],
            'mileage'           => ['required', 'integer', 'min:0', 'max:999999'],
            'year'              => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 2)],
            'make_id'           => ['required', 'integer', 'exists:makes,id'],
            'make_model_id'     => ['required', 'integer', 'exists:make_models,id'],
            'trim'              => ['nullable', 'string', 'max:100'],

            // ── Body ───────────────────────────────────────────────────────────
            'body_type_id'      => ['required', 'integer', 'exists:body_types,id'],
            'body_style_id'     => ['nullable', 'integer', 'exists:body_styles,id'],

            // ── Condition ─────────────────────────────────────────────────────
            'vehicle_condition' => ['required', 'string', 'in:Used,New,Certified Pre-Owned'],

            // ── Pricing ───────────────────────────────────────────────────────
            'list_price'        => ['nullable', 'numeric', 'min:0'],

            // ── Colors ────────────────────────────────────────────────────────
            'exterior_color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'interior_color_id' => ['nullable', 'integer', 'exists:colors,id'],

            // ── Mechanical — auto-populated from VIN decode (all nullable) ─────
            'fuel_type_id'          => ['nullable', 'integer', 'exists:fuel_types,id'],
            'transmission_type_id'  => ['nullable', 'integer', 'exists:transmission_types,id'],
            'drivetrain_type_id'    => ['nullable', 'integer', 'exists:drivetrain_types,id'],
            'doors'                 => ['nullable', 'integer', 'min:1', 'max:6'],
            'engine'                => ['nullable', 'string', 'max:150'],

            // ── Specs — seeded from VIN decode into vehicle_specs (all nullable)
            'cylinders'             => ['nullable', 'integer', 'min:1', 'max:16'],
            'displacement'          => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'max_horsepower'        => ['nullable', 'integer', 'min:0', 'max:2000'],
            'block_type'            => ['nullable', 'string', 'max:10'],
            'transmission_standard' => ['nullable', 'string', 'max:50'],
            'drivetrain_standard'   => ['nullable', 'string', 'max:20'],
            'gvwr'                  => ['nullable', 'integer', 'min:0', 'max:80000'],
        ];
    }
}
