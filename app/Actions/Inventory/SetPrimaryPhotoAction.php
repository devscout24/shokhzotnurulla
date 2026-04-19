<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehiclePhoto;
use Illuminate\Support\Facades\DB;

class SetPrimaryPhotoAction
{
    public function __invoke(Vehicle $vehicle, VehiclePhoto $photo): void
    {
        DB::transaction(function () use ($vehicle, $photo) {

            // Remove primary from all photos of this vehicle
            VehiclePhoto::where('vehicle_id', $vehicle->id)
                ->update(['is_primary' => false]);

            // Set this photo as primary
            $photo->update(['is_primary' => true]);
        });
    }
}
