<?php

namespace App\Listeners\User;

use App\Events\User\ProfileUpdated;
use Illuminate\Support\Facades\Log;

class LogProfileUpdate
{
    public function handle(ProfileUpdated $event): void
    {
        Log::channel('audit-dealer')->info('Profile updated', [
            'user_id' => $event->user->id,
            'email'   => $event->user->email,
            'ip'      => request()->ip(),
            'at'      => now(),
        ]);
    }
}