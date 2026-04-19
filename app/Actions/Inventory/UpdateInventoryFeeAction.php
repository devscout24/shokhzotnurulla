<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\DealerInventoryFee;

class UpdateInventoryFeeAction
{
    public function __invoke(DealerInventoryFee $fee, array $data): DealerInventoryFee
    {
        $fee->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'],
            'value'       => $data['value'],
            'tax'         => $data['tax'],
            'is_optional' => $data['is_optional'],
            'condition'   => $data['condition'],
        ]);

        return $fee->fresh();
    }
}