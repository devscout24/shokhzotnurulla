<?php

namespace App\Actions\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Support\Facades\DB;

class ReorderLocationsAction
{
    public function __invoke(Dealer $dealer, array $order): void
    {
        DB::transaction(function () use ($dealer, $order) {
            foreach ($order as $index => $locationId) {
                $dealer->locations()
                    ->where('id', $locationId)
                    ->update(['order' => $index]);
            }
        });
    }
}