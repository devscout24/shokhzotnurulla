<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDealerIpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dealerId = $this->user()->current_dealer_id;
        $ipId = $this->route('dealerIp'); // assuming route parameter name 'dealerIp'

        return [
            'ip_address' => [
                'required',
                'ip',
                Rule::unique('dealer_ips')->where('dealer_id', $dealerId)->ignore($ipId),
            ],
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ip_address.required' => 'IP Address is required.',
            'ip_address.ip'       => 'Please enter a valid IP address (IPv4 or IPv6).',
            'ip_address.unique'   => 'This IP address is already in your list.',
        ];
    }
}