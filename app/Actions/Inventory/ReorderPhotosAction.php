<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehiclePhoto;
use Illuminate\Support\Facades\DB;

class ReorderPhotosAction
{
    public function __invoke(Vehicle $vehicle, array $orderedIds): void
    {
        DB::transaction(function () use ($vehicle, $orderedIds) {

            foreach ($orderedIds as $index => $photoId) {
                VehiclePhoto::where('id', $photoId)
                    ->where('vehicle_id', $vehicle->id) // security: scope to vehicle
                    ->update([
                        'sort_order' => $index + 1,
                        'is_primary' => $index === 0,
                    ]);
            }
        });
    }
}
