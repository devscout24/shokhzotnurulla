<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'type'        => ['required', 'in:amount,percentage'],
            'value'       => ['required', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'tax'         => ['required', 'in:pre-tax,post-tax'],
            'is_optional' => ['required', 'boolean'],
            'condition'   => ['required', 'in:any,new,used,cpo,vpo'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Fee name is required.',
            'type.required'  => 'Fee type is required.',
            'value.required' => 'Fee amount is required.',
            'tax.required'   => 'Tax type is required.',
        ];
    }
}