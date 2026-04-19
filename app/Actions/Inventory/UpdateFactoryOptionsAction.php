<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;

class UpdateFactoryOptionsAction
{
    public function __invoke(Vehicle $vehicle, array $data): void
    {
        $selectedIds = $data['selected_ids'] ?? [];
        $starredIds  = $data['starred_ids']  ?? [];

        $syncData = collect($selectedIds)->mapWithKeys(fn ($id) => [
            $id => ['is_starred' => in_array($id, $starredIds)],
        ])->all();

        $vehicle->factoryOptions()->sync($syncData);
    }
}