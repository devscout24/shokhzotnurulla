<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Incentive;

final class DeleteIncentiveAction
{
    public function __invoke(Incentive $incentive): void
    {
        $incentive->delete();
    }
}