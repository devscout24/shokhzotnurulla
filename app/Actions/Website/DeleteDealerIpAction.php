<?php

namespace App\Actions\Website;

use App\Models\Dealership\DealerIp;

class DeleteDealerIpAction
{
    public function execute(DealerIp $dealerIp): void
    {
        $dealerIp->delete();
    }
}