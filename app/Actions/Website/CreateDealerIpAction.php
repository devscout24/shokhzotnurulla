<?php

namespace App\Actions\Website;

use App\Models\Dealership\Dealer;
use App\Models\Dealership\DealerIp;

class CreateDealerIpAction
{
    public function execute(Dealer $dealer, array $data): DealerIp
    {
        return $dealer->dealerIps()->create([
            'ip_address'  => $data['ip_address'],
            'description' => $data['description'],
        ]);
    }
}