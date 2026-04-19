<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\PricingSpecial;

final class UpdatePricingSpecialAction
{
    public function __invoke(PricingSpecial $pricingSpecial, array $data): void
    {
        $pricingSpecial->update($data);
    }
}