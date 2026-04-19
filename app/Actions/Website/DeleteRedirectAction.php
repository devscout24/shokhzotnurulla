<?php

namespace App\Actions\Website;

use App\Models\Website\Redirect;

class DeleteRedirectAction
{
    public function execute(Redirect $redirect): void
    {
        $redirect->delete();
    }
}