<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehiclePremiumOption;

class StorePremiumOptionAction
{
    public function __invoke(Vehicle $vehicle, array $data): VehiclePremiumOption
    {
        return $vehicle->premiumOptions()->create($data);
    }
}
