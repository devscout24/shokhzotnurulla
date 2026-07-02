<?php

namespace App\Actions\Inventory;

use App\Jobs\ApplyPhotoOverlay;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehiclePhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SetPrimaryPhotoAction
{
    private const OVERLAY_PATH = 'assets/Images/overlay/motors-logo-top-dealer-logo.webp';

    public function __invoke(Vehicle $vehicle, VehiclePhoto $photo): void
    {
        DB::transaction(function () use ($vehicle, $photo) {

            // Restore the previous primary photo's original image
            $previousPrimary = VehiclePhoto::where('vehicle_id', $vehicle->id)
                ->where('is_primary', true)
                ->first();

            if ($previousPrimary && $previousPrimary->original_path) {
                // Destroy the old overlaid primary image (only if it differs from original)
                if ($previousPrimary->path !== $previousPrimary->original_path) {
                    Storage::disk($previousPrimary->disk)->delete($previousPrimary->path);
                }

                $previousPrimary->update([
                    'path' => $previousPrimary->original_path,
                ]);
            }

            // Queue overlay processing for the new primary photo
            $overlay = public_path(self::OVERLAY_PATH);

            ApplyPhotoOverlay::dispatch($photo, $overlay);

            // Set this photo as primary
            VehiclePhoto::where('vehicle_id', $vehicle->id)
                ->update(['is_primary' => false]);

            $photo->update(['is_primary' => true]);
        });
    }
}
