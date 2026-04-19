<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehiclePhoto;
use App\Models\Website\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadPhotosAction
{
    public function __invoke(Vehicle $vehicle, array $files): array
    {
        $uploaded   = [];
        $nextOrder  = VehiclePhoto::where('vehicle_id', $vehicle->id)->max('sort_order') + 1;
        $hasPrimary = VehiclePhoto::where('vehicle_id', $vehicle->id)->exists();

        foreach ($files as $file) {

            // ── Path — media folder structure follow karo ─────────────────
            $year   = now()->format('Y');
            $month  = now()->format('m');
            $ext    = $file->getClientOriginalExtension();
            $unique = Str::uuid() . '.' . $ext;
            $path   = "dealers/{$vehicle->dealer_id}/media/images/{$year}/{$month}/{$unique}";

            Storage::disk('public')->putFileAs(
                "dealers/{$vehicle->dealer_id}/media/images/{$year}/{$month}",
                $file,
                $unique
            );

            [$width, $height] = getimagesize($file->getRealPath());

            // ── VehiclePhoto record ───────────────────────────────────────
            $photo = VehiclePhoto::create([
                'vehicle_id' => $vehicle->id,
                'path'       => $path,
                'disk'       => 'public',
                'url'        => Storage::disk('public')->url($path),
                'sort_order' => $nextOrder++,
                'is_primary' => ! $hasPrimary,
                'status'     => 'draft',
                'width'      => $width,
                'height'     => $height,
            ]);

            $hasPrimary = true;

            // ── Media record — same file, same path ─────────────────
            Media::create([
                'dealer_id'     => $vehicle->dealer_id,
                'user_id'       => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'name'          => $unique,
                'path'          => $path,
                'disk'          => 'public',
                'url'           => Storage::disk('public')->url($path),
                'type'          => 'image',
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'width'         => $width,
                'height'        => $height,
                'title'         => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            ]);

            $uploaded[] = $photo;
        }

        return $uploaded;
    }
}