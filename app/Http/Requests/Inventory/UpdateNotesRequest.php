<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dealer_notes'        => ['nullable', 'string'],
            'ai_description'      => ['nullable', 'string'],
            'internal_notes'      => ['nullable', 'string'],
            'key_highlights'      => ['nullable', 'array'],
            'key_highlights.*'    => ['required', 'string', 'max:200'],
            'lock_highlights'     => ['boolean'],
            'warranty_dealer'     => ['nullable', 'string', 'in:AS IS,Full,Limited'],
            'warranty_labor'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'warranty_parts'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'warranty_systems'    => ['nullable', 'string', 'max:1000'],
            'warranty_duration'   => ['nullable', 'string', 'max:100'],
            'service_contract'    => ['boolean'],
        ];
    }
}