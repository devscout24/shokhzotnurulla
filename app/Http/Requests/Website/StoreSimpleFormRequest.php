<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class StoreSimpleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:100'],
            'last_name'  => ['required', 'string', 'min:2', 'max:100'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'commpref'   => [$this->filled('commpref') ? 'required' : 'nullable', 'in:email,text,phone'],
            'comment'    => ['nullable', 'string', 'min:1', 'max:2000'],
            // 'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'vehicle_id' => ['nullable', 'integer'],
        ];
    }
}