<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehiclePrintable;

class StorePrintableAction
{
    public function __invoke(Vehicle $vehicle, int $dealerId, array $data): VehiclePrintable
    {
        return VehiclePrintable::create([
            'vehicle_id'  => $vehicle->id,
            'dealer_id'   => $dealerId,
            'name'        => $data['name'],
            'cta'         => $data['cta']         ?? null,
            'description' => $data['description'] ?? null,
            'layout'      => $data['layout']      ?? 'portrait',
        ]);
    }
}
