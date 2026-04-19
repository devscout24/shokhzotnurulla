<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class BulkFormEntryIdsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:form_entries,id'],
        ];
    }
}