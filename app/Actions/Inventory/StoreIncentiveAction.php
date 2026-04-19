<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Incentive;

final class StoreIncentiveAction
{
    public function __invoke(Dealer $dealer, array $data): Incentive
    {
        return Incentive::create(array_merge($data, ['dealer_id' => $dealer->id]));
    }
}