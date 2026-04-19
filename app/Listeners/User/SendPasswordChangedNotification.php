<?php

namespace App\Listeners\User;

use App\Events\User\PasswordChanged;
use App\Notifications\User\PasswordChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPasswordChangedNotification implements ShouldQueue
{
    public function handle(PasswordChanged $event): void
    {
        $event->user->notify(
            new PasswordChangedNotification(
                ip: request()->ip(),
                changedAt: now()->format('D, M d Y — h:i A'),
            )
        );
    }
}