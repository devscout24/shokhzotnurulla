<?php

namespace App\Actions\Website;

use App\Models\Website\Location;
use Illuminate\Support\Facades\DB;

class DeleteLocationAction
{
    public function __invoke(Location $location): void
    {
        DB::transaction(function () use ($location) {
            $location->phones()->delete();
            $location->emails()->delete();
            $location->hours()->delete();
            $location->specialHours()->delete();
            $location->delete();
        });
    }
}