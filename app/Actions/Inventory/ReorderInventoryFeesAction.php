<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\DealerInventoryFee;
use Illuminate\Support\Facades\DB;

class ReorderInventoryFeesAction
{
    public function __invoke(Dealer $dealer, array $ids): void
    {
        // Verify all IDs belong to this dealer
        $validIds = DealerInventoryFee::where('dealer_id', $dealer->id)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        DB::transaction(function () use ($ids, $validIds): void {
            foreach ($ids as $order => $id) {
                if (! in_array($id, $validIds, true)) continue;

                DealerInventoryFee::where('id', $id)
                    ->update(['sort_order' => $order]);
            }
        });
    }
}