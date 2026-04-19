<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use Illuminate\Support\Facades\DB;

class UpdatePricingAction
{
    public function __invoke(Vehicle $vehicle, array $data): void
    {
        DB::transaction(function () use ($vehicle, $data) {
            $vehicle->update(['list_price' => $data['list_price'] ?? null]);
            $vehicle->prices()->updateOrCreate(
                ['vehicle_id' => $vehicle->id],
                $data
            );
        });
    }
}