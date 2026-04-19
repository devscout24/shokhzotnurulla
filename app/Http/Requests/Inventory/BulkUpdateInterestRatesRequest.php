<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateInterestRatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rates'                    => ['required', 'array', 'min:1'],
            'rates.*.id'               => ['required', 'integer', 'exists:dealer_interest_rates,id'],
            'rates.*.make'             => ['nullable', 'string', 'max:100'],
            'rates.*.min_model_year'   => ['required', 'integer', 'min:2000', 'max:2099'],
            'rates.*.max_model_year'   => ['required', 'integer', 'min:2000', 'max:2099'],
            'rates.*.min_term'         => ['required', 'integer', 'min:0', 'max:200'],
            'rates.*.max_term'         => ['required', 'integer', 'min:0', 'max:200'],
            'rates.*.min_credit_score' => ['nullable', 'integer', 'min:300', 'max:850'],
            'rates.*.max_credit_score' => ['nullable', 'integer', 'min:300', 'max:850'],
            'rates.*.condition'        => ['required', 'in:any,new,used,cpo,vpo'],
            'rates.*.rate'             => ['required', 'numeric', 'min:0', 'max:99.99'],
        ];
    }
}