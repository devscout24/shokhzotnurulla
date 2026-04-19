<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDigitalRetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'free_shipping_miles' => 'required|integer|min:0',
            'shipping_discount'   => 'required|numeric|min:0',
            'deposit_amount'      => 'required|numeric|min:0',
            'deposit_hold_hours'  => 'required|integer|min:0',
            'retail_hold_hours'   => 'required|integer|min:0',
            'trade_offer_days'    => 'required|integer|min:0',
        ];
    }
}