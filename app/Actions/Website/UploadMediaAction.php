<?php

namespace App\Actions\Website;

use App\Models\Website\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadMediaAction
{
    public function execute(int $dealerId, array $files): array
    {
        $uploaded = [];

        foreach ($files as $file) {
            $uploaded[] = $this->processFile($dealerId, $file);
        }

        return $uploaded;
    }

    private function processFile(int $dealerId, UploadedFile $file): Media
    {
        $mime     = $file->getMimeType();
        $type     = str_starts_with($mime, 'video') ? 'video'
                  : (str_starts_with($mime, 'image') ? 'image' : 'file');
        $year     = now()->format('Y');
        $month    = now()->format('m');
        $ext      = $file->getClientOriginalExtension();
        $original = $file->getClientOriginalName();
        $unique   = Str::uuid() . '.' . $ext;
        $folder = str_starts_with($mime, 'image') ? 'images'
        : (str_starts_with($mime, 'video') ? 'videos' : 'files');

        $path = "dealers/{$dealerId}/media/{$folder}/{$year}/{$month}/{$unique}";

        Storage::disk('public')->putFileAs(
            "dealers/{$dealerId}/media/{$folder}/{$year}/{$month}",
            $file,
            $unique
        );

        $width  = null;
        $height = null;

        // Width/height without any package — PHP GD
        if ($type === 'image') {
            [$width, $height] = $this->getImageDimensions($file, $mime);
        }

        return Media::create([
            'dealer_id'     => $dealerId,
            'original_name' => $original,
            'name'          => $unique,
            'path'          => $path,
            'disk'          => 'public',
            'url'           => '/storage/' . $path,
            'type'          => $type,
            'mime_type'     => $mime,
            'size'          => $file->getSize(),
            'width'         => $width,
            'height'        => $height,
            'title' => pathinfo($original, PATHINFO_FILENAME),
        ]);
    }

    private function getImageDimensions(UploadedFile $file, string $mime): array
    {
        try {
            // SVG — parse viewBox
            if ($mime === 'image/svg+xml') {
                $content = file_get_contents($file->getRealPath());
                if (preg_match('/viewBox=["\'][\d.]+ [\d.]+ ([\d.]+) ([\d.]+)["\']/', $content, $m)) {
                    return [(int) $m[1], (int) $m[2]];
                }
                return [null, null];
            }

            // GIF, WEBP, JPG, PNG — PHP built-in
            $size = getimagesize($file->getRealPath());
            if ($size) {
                return [(int) $size[0], (int) $size[1]];
            }
        } catch (\Throwable $e) {
            // silent fail — dimensions optional hain
        }

        return [null, null];
    }
}