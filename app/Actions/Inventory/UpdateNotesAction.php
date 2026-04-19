<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;

class UpdateNotesAction
{
    public function __invoke(Vehicle $vehicle, array $data): void
    {
        $vehicle->notes()->updateOrCreate(
            ['vehicle_id' => $vehicle->id],
            $data
        );
    }
}