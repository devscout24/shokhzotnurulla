<?php

namespace App\Actions\Website;

use App\Models\Dealership\DealerIp;

class UpdateDealerIpAction
{
    public function execute(DealerIp $dealerIp, array $data): DealerIp
    {
        $dealerIp->update([
            'ip_address'  => $data['ip_address'],
            'description' => $data['description'],
        ]);

        return $dealerIp;
    }
}