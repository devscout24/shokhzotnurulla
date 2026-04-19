<?php

namespace App\Actions\Inventory;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\DealerInterestRate;

class StoreInterestRateAction
{
    public function __invoke(Dealer $dealer, array $data): DealerInterestRate
    {
        $sortOrder = DealerInterestRate::where('dealer_id', $dealer->id)
            ->where('min_model_year', $data['min_model_year'])
            ->where('max_model_year', $data['max_model_year'])
            ->max('sort_order') ?? 0;

        return DealerInterestRate::create([
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
            'sort_order'       => $sortOrder + 1,
        ]);
    }
}