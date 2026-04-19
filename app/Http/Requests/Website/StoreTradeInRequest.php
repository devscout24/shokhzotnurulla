<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class StoreTradeInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Vehicle info ──────────────────────────────────────────────────
            'input_method'      => ['required', 'in:ymm,vin'],
            'year'              => ['required_if:input_method,ymm', 'nullable', 'integer', 'min:1900', 'max:2100'],
            'make'              => ['required_if:input_method,ymm', 'nullable', 'string', 'max:100'],
            'model'             => ['required_if:input_method,ymm', 'nullable', 'string', 'max:100'],
            'trim'              => ['nullable', 'string', 'max:100'],
            'body'              => ['nullable', 'string', 'max:100'],
            'engine'            => ['nullable', 'string', 'max:100'],
            'drivetrain'        => ['nullable', 'string', 'max:100'],
            'vin'               => ['required_if:input_method,vin', 'nullable', 'string', 'max:17'],

            // ── Condition ─────────────────────────────────────────────────────
            'mileage'           => ['required', 'string', 'max:20'],
            'postal_code'       => ['required', 'digits:5'],
            'exterior_color'    => ['required', 'string', 'max:50'],
            'interior_color'    => ['required', 'string', 'max:50'],
            'keys'              => ['required', 'in:1,2+'],
            'ownership'         => ['required', 'in:Loan,Lease,I own it'],
            'lienholder'        => ['nullable', 'string', 'max:200'],
            'remaining_balance' => ['nullable', 'string', 'max:50'],

            // ── History ───────────────────────────────────────────────────────
            'condition'         => ['required', 'in:new,average,poor'],
            'clean_title'       => ['required', 'in:yes,no'],
            'run_drive'         => ['required', 'in:yes,no'],
            'accident'          => ['required', 'in:yes,no'],
            'warning_lights'    => ['required', 'in:yes,no'],
            'smoked_in'         => ['required', 'in:yes,no'],
            'damage'            => ['required', 'array'],
            'damage.*'          => ['string', 'max:100'],
            'tire_condition'    => ['nullable', 'string', 'max:100'],

            // ── Contact ───────────────────────────────────────────────────────
            'first_name'        => ['required', 'string', 'min:2', 'max:100'],
            'last_name'         => ['required', 'string', 'min:2', 'max:100'],
            'email'             => ['required', 'email', 'max:255'],
            'phone'             => ['required', 'string', 'max:20'],
            'commpref'          => ['required', 'in:email,text,phone'],
            'comment'           => ['nullable', 'string', 'max:2000'],
        ];
    }
}
