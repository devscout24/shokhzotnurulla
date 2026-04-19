<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFactoryOptionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selected_ids'   => ['nullable', 'array'],
            'selected_ids.*' => ['integer', 'exists:factory_options,id'],
            'starred_ids'    => ['nullable', 'array'],
            'starred_ids.*'  => ['integer', 'exists:factory_options,id'],
        ];
    }
}