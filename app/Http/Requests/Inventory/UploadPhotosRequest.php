<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photos'   => ['required', 'array', 'min:1', 'max:50'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'], // 10MB max
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required'   => 'Please select at least one photo.',
            'photos.*.image'    => 'Only image files are allowed.',
            'photos.*.mimes'    => 'Accepted formats: JPEG, PNG, WebP.',
            'photos.*.max'      => 'Each photo must be under 10MB.',
        ];
    }
}
