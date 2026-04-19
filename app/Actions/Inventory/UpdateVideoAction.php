<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;

class UpdateVideoAction
{
    public function __invoke(Vehicle $vehicle, array $data): void
    {
        if (empty($data['url'])) {
            $vehicle->video()->delete();
            return;
        }

        $vehicle->video()->updateOrCreate(
            ['vehicle_id' => $vehicle->id],
            $data
        );
    }
}