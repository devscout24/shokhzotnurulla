<?php

namespace App\Actions\Website;

use App\Models\Dealership\Dealer;
use App\Models\Website\Redirect;
use Illuminate\Support\Arr;

class CreateRedirectAction
{
    public function execute(Dealer $dealer, array $data): Redirect
    {
        $redirect = $dealer->redirects()->create([
            'source_url'  => $data['source_url'],
            'target_url'  => $data['target_url'],
            'is_regex'    => $data['is_regex'],
            'status_code' => $data['status_code'],
            'is_enabled'  => $data['is_enabled'],
        ]);

        return $redirect;
    }
}