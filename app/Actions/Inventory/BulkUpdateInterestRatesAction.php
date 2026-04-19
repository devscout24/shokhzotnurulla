<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\DealerInterestRate;
use Illuminate\Support\Facades\DB;

class BulkUpdateInterestRatesAction
{
    public function __invoke(Dealer $dealer, array $rates): void
    {
        // Collect IDs and verify all belong to this dealer (security check)
        $ids = array_column($rates, 'id');

        $validIds = DealerInterestRate::where('dealer_id', $dealer->id)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        DB::transaction(function () use ($rates, $validIds): void {
            foreach ($rates as $index => $data) {
                // Skip if ID doesn't belong to this dealer
                if (! in_array($data['id'], $validIds, true)) {
                    continue;
                }

                DealerInterestRate::where('id', $data['id'])->update([
                    'make'             => $data['make'] ?: null,
                    'min_model_year'   => $data['min_model_year'],
                    'max_model_year'   => $data['max_model_year'],
                    'min_term'         => $data['min_term'],
                    'max_term'         => $data['max_term'],
                    'min_credit_score' => $data['min_credit_score'] ?? null,
                    'max_credit_score' => $data['max_credit_score'] ?? null,
                    'condition'        => $data['condition'],
                    'rate'             => $data['rate'],
                    'sort_order'       => $index,
                ]);
            }
        });
    }
}