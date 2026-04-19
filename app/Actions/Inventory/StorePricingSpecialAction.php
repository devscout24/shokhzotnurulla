<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\PricingSpecial;

final class StorePricingSpecialAction
{
    public function __invoke(Dealer $dealer, array $data): PricingSpecial
    {
        return PricingSpecial::create(
            array_merge($data, ['dealer_id' => $dealer->id])
        );
    }
}