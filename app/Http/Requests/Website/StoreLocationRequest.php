<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'street1' => 'required|string|max:255',
            'street2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'postalcode' => 'required|string|max:10',
            'country' => 'required|string|size:2',
            'map_override' => 'nullable|string|max:255',

            // Phones
            'phone_main' => 'nullable|string|max:20',
            'phone_sales' => 'nullable|string|max:20',
            'phone_service' => 'nullable|string|max:20',
            'phone_parts' => 'nullable|string|max:20',
            'phone_rentals' => 'nullable|string|max:20',
            'phone_collision' => 'nullable|string|max:20',

            // Emails
            'email_main' => 'nullable|email|max:255',
            'email_sales' => 'nullable|email|max:255',
            'email_service' => 'nullable|email|max:255',
            'email_parts' => 'nullable|email|max:255',
            'email_rentals' => 'nullable|email|max:255',
            'email_collision' => 'nullable|email|max:255',

            // Hours
            'hours' => 'nullable|array',
            'hours.sales' => 'nullable|array',
            'hours.sales.*.open' => 'nullable|string',
            'hours.sales.*.close' => 'nullable|string',
            'hours.sales.*.is_closed' => 'nullable|boolean',
            'hours.sales.*.appointment_only' => 'nullable|boolean',

            'hours.service' => 'nullable|array',
            'hours.parts' => 'nullable|array',
            'hours.rentals' => 'nullable|array',
            'hours.collision' => 'nullable|array',

            // Special hours
            'special_hours' => 'nullable|array',
            'special_hours.*.department' => 'nullable|string|max:50',
            'special_hours.*.date' => 'nullable|date',
            'special_hours.*.open_time' => 'nullable|string',
            'special_hours.*.close_time' => 'nullable|string',
            'special_hours.*.is_closed' => 'nullable|boolean',
            'special_hours.*.appointment_only' => 'nullable|boolean',
        ];
    }
}