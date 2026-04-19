<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use Illuminate\Support\Facades\DB;

class UpdateVehicleStatusAction
{
    public function __invoke(Vehicle $vehicle, array $data): void
    {
        $updates = [];

        if (isset($data['status'])) {
            $updates['status'] = $data['status'];

            // Mark sold_at timestamp when status flips to sold
            if ($data['status'] === 'sold' && $vehicle->status !== 'sold') {
                $updates['sold_at'] = now();
            }

            // Clear sold_at if un-selling a vehicle
            if ($data['status'] !== 'sold' && $vehicle->status === 'sold') {
                $updates['sold_at'] = null;
            }
        }

        if (isset($data['is_on_hold'])) {
            $updates['is_on_hold'] = (bool) $data['is_on_hold'];
        }

        if (isset($data['is_spotlight'])) {
            $updates['is_spotlight'] = (bool) $data['is_spotlight'];
        }

        if (isset($data['ignore_feed_updates'])) {
            $updates['ignore_feed_updates'] = (bool) $data['ignore_feed_updates'];
        }

        if (! empty($updates)) {
            $vehicle->update($updates);
        }
    }
}
