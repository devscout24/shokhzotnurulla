<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\DealerInventoryFee;

class StoreInventoryFeeAction
{
    public function __invoke(Dealer $dealer, array $data): DealerInventoryFee
    {
        $maxSortOrder = DealerInventoryFee::where('dealer_id', $dealer->id)
            ->max('sort_order') ?? -1;

        return DealerInventoryFee::create([
            'dealer_id'   => $dealer->id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'],
            'value'       => $data['value'],
            'tax'         => $data['tax'],
            'is_optional' => $data['is_optional'],
            'condition'   => $data['condition'],
            'sort_order'  => $maxSortOrder + 1,
        ]);
    }
}