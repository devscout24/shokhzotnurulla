<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehiclePhoto;
use Illuminate\Support\Facades\Storage;

class BulkDeletePhotosAction
{
    public function __invoke(Vehicle $vehicle): void
    {
        $photos = VehiclePhoto::where('vehicle_id', $vehicle->id)->get();

        foreach ($photos as $photo) {
            Storage::disk($photo->disk)->delete($photo->path);
            $photo->delete();
        }
    }
}