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
            'video_url'    => ['nullable', 'string', 'max:500'],
            'video_source' => ['nullable', 'string', 'in:youtube,glo3d,lesautomotive,dealerimagepro,dealervideopro,spincar,unityworks,flickfusion,overfuel'],
            'video_file'   => ['nullable', 'file', 'mimes:mp4', 'max:51200'], // 50MB max
        ];
    }
}
