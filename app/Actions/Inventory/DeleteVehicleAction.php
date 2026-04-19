<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;

class DeleteVehicleAction
{
    public function __invoke(Vehicle $vehicle): void
    {
        // SoftDeletes — vehicle stays in DB, just marked deleted
        $vehicle->delete();
    }
}
