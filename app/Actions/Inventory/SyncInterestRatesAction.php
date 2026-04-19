<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\DealerInterestRate;
use Illuminate\Support\Facades\DB;

class SyncInterestRatesAction
{
    public function __invoke(Dealer $dealer, array $creates, array $updates, array $deletes): void
    {
        DB::transaction(function () use ($dealer, $creates, $updates, $deletes): void {

            // ── 1. Deletes ────────────────────────────────────────────
            if (! empty($deletes)) {
                DealerInterestRate::where('dealer_id', $dealer->id)
                    ->whereIn('id', $deletes)
                    ->delete();
            }

            // ── 2. Creates ────────────────────────────────────────────
            foreach ($creates as $index => $data) {
                DealerInterestRate::create([
                    'dealer_id'        => $dealer->id,
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

            // ── 3. Updates ────────────────────────────────────────────
            foreach ($updates as $index => $data) {
                DealerInterestRate::where('id', $data['id'])
                    ->where('dealer_id', $dealer->id) // security: ensure ownership
                    ->update([
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