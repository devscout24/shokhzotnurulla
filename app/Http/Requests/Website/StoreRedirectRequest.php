<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRedirectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dealerId = $this->user()->current_dealer_id;

        return [
            'source_url' => [
                'required',
                'string',
                'max:255',
                Rule::unique('redirects')->where(function ($query) use ($dealerId) {
                    return $query->where('dealer_id', $dealerId)
                                 ->where('is_regex', $this->input('is_regex'));
                }),
            ],
            'target_url'   => 'required|string|max:255',
            'is_regex'     => 'required|boolean',
            'status_code'  => 'required|in:301,302',
            'is_enabled'   => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'source_url.required'  => 'Redirect from is required.',
            'source_url.unique'   => 'A redirect with this source URL and regex setting already exists.',
            'target_url.required'  => 'Redirect to is required.',
            'status_code.in'       => 'Type must be Permanent (301) or Temporary (302).',
        ];
    }
}