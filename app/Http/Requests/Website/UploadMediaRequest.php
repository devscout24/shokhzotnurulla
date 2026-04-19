<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files'   => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp,svg,mp4,mov,avi,webm,pdf,doc,docx,xls,xlsx,csv',
                'max:204800', // 200MB (KB me hota hai)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required'   => 'Please select at least one file.',
            'files.max'        => 'Maximum 20 files at a time.',
            'files.*.mimes'    => 'Only images (jpg, png, gif, webp, svg) and videos (mp4, mov, avi, webm) are allowed.',
            'files.*.max'      => 'Each file must be under 100MB.',
        ];
    }
}