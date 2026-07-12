<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'legal_name'           => 'nullable|string|max:255',
            'corporate_address'    => 'nullable|string|max:500',
            'support_email'        => 'nullable|email|max:255',
            'abandoned_form_minutes' => 'required|integer|min:1',
            'logo'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'printable_logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'google_places_api_key' => 'nullable|string|max:500',
            'google_place_id'      => 'nullable|string|max:500',
        ];
    }
}
