<?php

namespace App\Events;

use App\Models\Dealership\DealerIntegration;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IntegrationSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public DealerIntegration $integration)
    {
        //
    }
}
