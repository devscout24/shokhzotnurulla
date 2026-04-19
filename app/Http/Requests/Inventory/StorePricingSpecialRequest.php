<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StorePricingSpecialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:150'],
            'type'              => ['nullable', 'in:formfill,override'],
            'button_text'       => ['nullable', 'string', 'max:100'],
            'discount_label'    => ['nullable', 'string', 'max:100'],
            'stackable'         => ['boolean'],
            'priority'          => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_type'     => ['nullable', 'in:fixed,percentage,dollars,offsetdollar,special,offsetincrease,increase'],
            'amount'            => ['nullable', 'numeric', 'min:0'],
            'finance_rate'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'finance_term'      => ['nullable', 'integer', 'min:1'],
            'condition'         => ['nullable', 'string', 'max:50'],
            'is_certified'      => ['nullable', 'boolean'],
            'model_number'      => ['nullable', 'string', 'max:100'],
            'year'              => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 2)],
            'make_id'           => ['nullable', 'integer', 'exists:makes,id'],
            'make_model_id'     => ['nullable', 'integer', 'exists:make_models,id'],
            'trim'              => ['nullable', 'string', 'max:100'],
            'body_style'        => ['nullable', 'string', 'max:100'],
            'exterior_color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'stock_number'      => ['nullable', 'string', 'max:50'],
            'tag'               => ['nullable', 'string', 'max:100'],
            'min_days'          => ['nullable', 'integer', 'min:0'],
            'max_days'          => ['nullable', 'integer', 'min:0'],
            'send_email'        => ['boolean'],
            'hide_price'        => ['boolean'],
            'starts_at'         => ['nullable', 'date'],
            'ends_at'           => ['nullable', 'date', 'after_or_equal:starts_at'],
            'notes'             => ['nullable', 'string', 'max:5000'],
            'disclaimer'        => ['nullable', 'string', 'max:5000'],
        ];
    }
}