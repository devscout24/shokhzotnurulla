<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class UploadFormEntryPhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'form_entry_id' => ['required', 'integer', 'exists:form_entries,id'],
            'photos'        => ['required', 'array', 'max:20'],
            'photos.*'      => ['image', 'max:10240'],
        ];
    }
}