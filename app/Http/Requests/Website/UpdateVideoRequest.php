<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video_source' => ['nullable', 'string', 'in:overfuel'],
            'video_file'   => ['nullable', 'file', 'mimes:mp4', 'max:51200'], // 50MB max
        ];
    }
}
