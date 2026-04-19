<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDisclaimersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'finance_disclaimer' => 'nullable|string',
            'inventory_disclaimer' => 'nullable|string',
            'deposit_disclaimer' => 'nullable|string',
            'pricing_disclaimer' => 'nullable|string',
            'optional_disclaimer' => 'nullable|string',
        ];
    }
}