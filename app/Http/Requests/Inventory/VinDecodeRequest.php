<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class VinDecodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // VINs are exactly 17 chars; cannot contain I, O, Q (ISO 3779 standard)
            'vin'        => ['required', 'string', 'size:17', 'regex:/^[A-HJ-NPR-Z0-9]{17}$/i'],
            'model_year' => ['nullable', 'integer', 'min:1981', 'max:' . (now()->year + 1)],
        ];
    }

    public function messages(): array
    {
        return [
            'vin.required' => 'VIN is required.',
            'vin.size'     => 'VIN must be exactly 17 characters.',
            'vin.regex'    => 'VIN contains invalid characters. Letters I, O, and Q are not allowed in a valid VIN.',
            'model_year.min' => 'Model year must be 1981 or later (NHTSA standard VIN format).',
            'model_year.max' => 'Model year cannot exceed :max.',
        ];
    }

    /**
     * Normalize VIN to uppercase before validation runs.
     */
    public function prepareForValidation(): void
    {
        if ($this->filled('vin')) {
            $this->merge([
                'vin' => strtoupper(trim($this->input('vin'))),
            ]);
        }
    }
}
