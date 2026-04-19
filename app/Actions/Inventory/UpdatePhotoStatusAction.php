<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\VehiclePhoto;

class UpdatePhotoStatusAction
{
    public function __invoke(VehiclePhoto $photo, string $status): void
    {
        $photo->update(['status' => $status]);
    }
}
