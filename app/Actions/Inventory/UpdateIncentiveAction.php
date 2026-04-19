<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Incentive;

final class UpdateIncentiveAction
{
    public function __invoke(Incentive $incentive, array $data): void
    {
        $incentive->update($data);
    }
}