<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'              => ['nullable', 'string', 'in:draft,active,sold'],
            'is_on_hold'          => ['nullable', 'boolean'],
            'is_spotlight'        => ['nullable', 'boolean'],
            'ignore_feed_updates' => ['nullable', 'boolean'],
        ];
    }
}
