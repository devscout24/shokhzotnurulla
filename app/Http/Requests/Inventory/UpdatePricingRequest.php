<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'list_price'              => ['nullable', 'numeric', 'min:0'],
            'msrp'                    => ['nullable', 'numeric', 'min:0'],
            'dealer_cost'             => ['nullable', 'numeric', 'min:0'],
            'pricing_disclaimer'      => ['nullable', 'string', 'max:5000'],
            'special_price'           => ['nullable', 'numeric', 'min:0'],
            'special_price_label'     => ['nullable', 'string', 'max:100'],
            'addon_price'             => ['nullable', 'numeric', 'min:0'],
            'addon_price_label'       => ['nullable', 'string', 'max:100'],
            'addon_price_description' => ['nullable', 'string', 'max:1000'],
            'adjustment_label'        => ['nullable', 'string', 'max:100'],
            'internet_price'          => ['nullable', 'numeric', 'min:0'],
            'asking_price'            => ['nullable', 'numeric', 'min:0'],
            'sold_price'              => ['nullable', 'numeric', 'min:0'],
            'sold_date'               => ['nullable', 'date'],
            'sold_to'                 => ['nullable', 'string', 'max:200'],
        ];
    }
}