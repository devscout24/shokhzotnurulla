<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tags'   => ['nullable', 'array', 'max:20'],
            'tags.*' => ['required', 'string', 'max:100'],
        ];
    }
}