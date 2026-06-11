<?php

namespace App\Services\Menu;

use App\Models\Dealership\Dealer;
use Database\Seeders\MenuSeeder;

class MenuService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function buildForDealer(Dealer $dealer)
    {
        $dealer->load('locations');
        MenuSeeder::seedForDealer($dealer);

    }
}
