<?php

namespace App\Actions\Website;

use App\Models\Website\FormEntry;
use App\Models\Website\FormEntryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadFormEntryPhotosAction
{
    public function __invoke(Request $request, FormEntry $entry): array
    {
        $photos = [];

        foreach ($request->file('photos') as $index => $file) {
            $path = $file->store("form-entries/{$entry->id}/photos", 'public');

            $photo = FormEntryPhoto::create([
                'form_entry_id' => $entry->id,
                'path'          => $path,
                'disk'          => 'public',
                'url'           => Storage::disk('public')->url($path),
                'sort_order'    => $index,
            ]);

            $photos[] = [
                'id'  => $photo->id,
                'url' => $photo->url,
            ];
        }

        return $photos;
    }
}