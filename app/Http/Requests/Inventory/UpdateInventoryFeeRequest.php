<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryFeeRequest extends FormRequest
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
}