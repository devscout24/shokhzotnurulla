<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StorePremiumOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'factory_code' => ['required', 'string', 'max:50'],
            'category'     => ['required', 'string', 'max:100'],
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'msrp'         => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
