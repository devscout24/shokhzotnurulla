<?php

namespace App\Actions\Website;

use App\Models\Website\Redirect;

class UpdateRedirectAction
{
    public function execute(Redirect $redirect, array $data): Redirect
    {
        $redirect->update([
            'source_url'  => $data['source_url'],
            'target_url'  => $data['target_url'],
            'is_regex'    => $data['is_regex'],
            'status_code' => $data['status_code'],
            'is_enabled'  => $data['is_enabled'],
        ]);

        return $redirect;
    }
}