<?php

namespace App\Listeners\User;

use App\Events\User\SecuritySettingsUpdated;
use Illuminate\Support\Facades\Log;

class LogSecuritySettingsUpdate
{
    public function handle(SecuritySettingsUpdated $event): void
    {
        Log::channel('audit-dealer')->info('Security settings updated', [
            'user_id' => $event->user->id,
            'email'   => $event->user->email,
            'ip'      => request()->ip(),
            'at'      => now(),
        ]);
    }
}