<?php

namespace App\Http\Requests\Website;

use App\Http\Requests\Website\StoreSimpleFormRequest;

class StoreScheduleServiceRequest extends StoreSimpleFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'min:2', 'max:100'],
            'last_name'     => ['required', 'string', 'min:2', 'max:100'],
            'email'         => ['required', 'email', 'max:255'],
            'phone'         => ['required', 'string', 'max:20'],
            'commpref'      => ['nullable', 'in:email,text,phone'],
            'year'          => ['nullable', 'string', 'max:4'],
            'make'          => ['nullable', 'string', 'max:100'],
            'model'         => ['nullable', 'string', 'max:100'],
            'mileage'       => ['nullable', 'integer', 'min:0'],
            'vin'           => ['nullable', 'string', 'max:17'],
            'warranty'      => ['nullable', 'in:yes,no'],
            'services'      => ['nullable', 'array'],
            'services.*'    => ['string', 'max:100'],
            'comment'       => ['nullable', 'string', 'max:2000'],
            'preferreddate' => ['nullable', 'string', 'max:500'],
            'vehicle'       => ['nullable', 'string', 'max:255'],
            'vehicle_id'    => ['nullable', 'integer'],
        ];
    }
}
