<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $passwordRule = [
            'required',
            'string',
            'different:old_password',
            'confirmed',
        ];

        if ($this->user()?->password_complexity) {
            $passwordRule[] = Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        }

        return [
            'old_password' => ['required', 'string', 'current_password'],
            'password'     => $passwordRule,
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required'         => 'Old password is required.',
            'old_password.current_password' => 'Your old password is incorrect.',
            'password.different'            => 'New password must be different from your old password.',
            'password.confirmed'            => 'Password confirmation does not match.',
            'password.min'                  => 'Password must be at least 8 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'old_password' => 'old password',
            'password'     => 'new password',
        ];
    }
}