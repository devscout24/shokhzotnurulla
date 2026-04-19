<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterestRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'make'             => ['nullable', 'string', 'max:100'],
            'min_model_year'   => ['required', 'integer', 'min:2000', 'max:2099'],
            'max_model_year'   => ['required', 'integer', 'min:2000', 'max:2099'],
            'min_term'         => ['required', 'integer', 'min:0', 'max:200'],
            'max_term'         => ['required', 'integer', 'min:0', 'max:200'],
            'min_credit_score' => ['nullable', 'integer', 'min:300', 'max:850'],
            'max_credit_score' => ['nullable', 'integer', 'min:300', 'max:850'],
            'condition'        => ['required', 'in:any,new,used,cpo,vpo'],
            'rate'             => ['required', 'numeric', 'min:0', 'max:99.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'rate.required'           => 'Interest rate is required.',
            'min_model_year.required' => 'Min. model year is required.',
            'max_model_year.required' => 'Max. model year is required.',
            'condition.in'            => 'Invalid vehicle condition selected.',
        ];
    }
}