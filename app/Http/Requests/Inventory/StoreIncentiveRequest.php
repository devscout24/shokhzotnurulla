<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncentiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:150'],
            'type'          => ['required', 'in:cash,finance,ivc_dvc,lease,percentage_off'],
            'category'      => ['required', 'in:all,used,new,cpo'],
            'description'   => ['nullable', 'string', 'max:2000'],
            'amount'        => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'amount_type'   => ['nullable', 'in:fixed,percent'],
            'program_code'  => ['nullable', 'string', 'max:100'],
            'is_guaranteed' => ['boolean'],
            'is_featured'   => ['boolean'],
            'is_enabled'    => ['boolean'],
            'expires_at'    => ['nullable', 'date'],
        ];
    }
}