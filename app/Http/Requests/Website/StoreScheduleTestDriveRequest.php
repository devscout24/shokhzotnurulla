<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleTestDriveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preferred_date'      => ['required', 'date', 'after_or_equal:today'],
            'preferred_day_label' => ['required', 'string', 'max:100'],
            'preferred_time'      => ['nullable', 'string', 'max:20'],
            'first_name'          => ['required', 'string', 'min:2', 'max:100'],
            'last_name'           => ['required', 'string', 'min:2', 'max:100'],
            'email'               => ['required', 'email', 'max:255'],
            'phone'               => ['required', 'string', 'max:20'],
            'commpref'            => ['required', 'in:email,text,phone'],
            'comment'             => ['nullable', 'string', 'max:2000'],
            'vehicle_id'          => ['nullable', 'integer', 'exists:vehicles,id'],
        ];
    }
}