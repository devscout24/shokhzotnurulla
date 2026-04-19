<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url'    => ['nullable', 'string', 'url', 'max:500'],
            'source' => ['nullable', 'string', 'in:youtube,glo3d,lesautomotive,dealerimagepro,dealervideopro,spincar,unityworks,flickfusion'],
        ];
    }
}
