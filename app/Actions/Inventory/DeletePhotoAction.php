<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\VehiclePhoto;
use App\Models\Website\Media;
use Illuminate\Support\Facades\Storage;

class DeletePhotoAction
{
    public function __invoke(VehiclePhoto $photo): void
    {
        $vehicleId  = $photo->vehicle_id;
        $wasPrimary = $photo->is_primary;

        Storage::disk($photo->disk)->delete($photo->path);

        Media::where('path', $photo->path)->delete();

        $photo->delete();

        if ($wasPrimary) {
            $next = VehiclePhoto::where('vehicle_id', $vehicleId)
                ->orderBy('sort_order')
                ->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }
    }
}