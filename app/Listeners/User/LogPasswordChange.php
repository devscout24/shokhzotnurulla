<?php

namespace App\Listeners\User;

use App\Events\User\PasswordChanged;
use Illuminate\Support\Facades\Log;

class LogPasswordChange
{
    public function handle(PasswordChanged $event): void
    {
        Log::channel('audit-dealer')->info('Password changed', [
            'user_id' => $event->user->id,
            'email'   => $event->user->email,
            'ip'      => request()->ip(),
            'at'      => now(),
        ]);
    }
}