<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\DealerInterestRate;

class CloneInterestRateAction
{
    public function __invoke(DealerInterestRate $rate): DealerInterestRate
    {
        $maxSortOrder = DealerInterestRate::where('dealer_id', $rate->dealer_id)
            ->where('min_model_year', $rate->min_model_year)
            ->where('max_model_year', $rate->max_model_year)
            ->max('sort_order') ?? 0;

        return DealerInterestRate::create([
            'dealer_id'        => $rate->dealer_id,
            'make'             => $rate->make,
            'min_model_year'   => $rate->min_model_year,
            'max_model_year'   => $rate->max_model_year,
            'min_term'         => $rate->min_term,
            'max_term'         => $rate->max_term,
            'min_credit_score' => $rate->min_credit_score,
            'max_credit_score' => $rate->max_credit_score,
            'condition'        => $rate->condition,
            'rate'             => $rate->rate,
            'sort_order'       => $maxSortOrder + 1,
        ]);
    }
}