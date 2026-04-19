<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\PricingSpecial;

final class DeletePricingSpecialAction
{
    public function __invoke(PricingSpecial $pricingSpecial): void
    {
        $pricingSpecial->delete();
    }
}