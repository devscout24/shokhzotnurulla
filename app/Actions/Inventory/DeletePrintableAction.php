<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\VehiclePrintable;

class DeletePrintableAction
{
    public function __invoke(VehiclePrintable $printable): void
    {
        $printable->delete();
    }
}
