<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\VehiclePrintable;

class UpdatePrintableAction
{
    public function __invoke(VehiclePrintable $printable, array $data): void
    {
        $printable->update($data);
    }
}
