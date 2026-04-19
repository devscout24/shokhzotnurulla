<?php

namespace App\Http\Requests\Inventory;

use App\Models\Inventory\VehiclePrintable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrintableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicle = $this->route('vehicle');

        return [
            'name' => [
                'required',
                'string',
                Rule::in(array_keys(VehiclePrintable::TYPES)),
                Rule::unique('vehicle_printables')
                    ->where(fn ($q) => $q->where('vehicle_id', $vehicle->id)),
            ],
            'cta' => [
                'nullable',
                'string',
                Rule::in(array_keys(VehiclePrintable::CTA_OPTIONS)),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'layout'      => ['nullable', 'string', Rule::in(['portrait', 'landscape'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please select a printable name.',
            'name.in'       => 'The selected printable name is invalid.',
            'name.unique'   => 'A printable of this type already exists for this vehicle.',
        ];
    }
}
