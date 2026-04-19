<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class SyncInterestRatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Creates — new rows (no id)
            'creates'                          => ['present', 'array'],
            'creates.*.make'                   => ['nullable', 'string', 'max:100'],
            'creates.*.min_model_year'         => ['required', 'integer', 'min:2000', 'max:2099'],
            'creates.*.max_model_year'         => ['required', 'integer', 'min:2000', 'max:2099'],
            'creates.*.min_term'               => ['required', 'integer', 'min:0', 'max:200'],
            'creates.*.max_term'               => ['required', 'integer', 'min:0', 'max:200'],
            'creates.*.min_credit_score'       => ['nullable', 'integer', 'min:300', 'max:850'],
            'creates.*.max_credit_score'       => ['nullable', 'integer', 'min:300', 'max:850'],
            'creates.*.condition'              => ['required', 'in:any,new,used,cpo,vpo'],
            'creates.*.rate'                   => ['required', 'numeric', 'min:0', 'max:99.99'],

            // Updates — existing rows (have real id)
            'updates'                          => ['present', 'array'],
            'updates.*.id'                     => ['required', 'integer', 'exists:dealer_interest_rates,id'],
            'updates.*.make'                   => ['nullable', 'string', 'max:100'],
            'updates.*.min_model_year'         => ['required', 'integer', 'min:2000', 'max:2099'],
            'updates.*.max_model_year'         => ['required', 'integer', 'min:2000', 'max:2099'],
            'updates.*.min_term'               => ['required', 'integer', 'min:0', 'max:200'],
            'updates.*.max_term'               => ['required', 'integer', 'min:0', 'max:200'],
            'updates.*.min_credit_score'       => ['nullable', 'integer', 'min:300', 'max:850'],
            'updates.*.max_credit_score'       => ['nullable', 'integer', 'min:300', 'max:850'],
            'updates.*.condition'              => ['required', 'in:any,new,used,cpo,vpo'],
            'updates.*.rate'                   => ['required', 'numeric', 'min:0', 'max:99.99'],

            // Deletes — IDs to remove
            'deletes'                          => ['present', 'array'],
            'deletes.*'                        => ['integer', 'exists:dealer_interest_rates,id'],
        ];
    }
}