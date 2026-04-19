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
            'legal_name' => 'nullable|string|max:255',
            'corporate_address' => 'nullable|string|max:500',
            'support_email' => 'nullable|email|max:255',
            'abandoned_form_minutes' => 'required|integer|min:1',
        ];
    }
}