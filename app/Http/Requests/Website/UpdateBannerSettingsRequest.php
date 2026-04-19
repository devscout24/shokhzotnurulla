<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banner_text'               => 'nullable|string|max:255',
            'banner_hover_title'        => 'nullable|string|max:255',
            'banner_text_color'         => 'nullable|string|max:7|regex:/^#[0-9a-fA-F]{6}$/',
            'banner_bg_color'           => 'nullable|string|max:7|regex:/^#[0-9a-fA-F]{6}$/',
            'banner_desktop_media_id'   => 'nullable|exists:media,id',
            'banner_mobile_media_id'    => 'nullable|exists:media,id',
            // For file uploads (optional)
            'banner_desktop_image'      => 'nullable|image|max:2048|dimensions:min_width=1420,min_height=40',
            'banner_mobile_image'       => 'nullable|image|max:2048|dimensions:min_width=600,min_height=100',
        ];
    }
}