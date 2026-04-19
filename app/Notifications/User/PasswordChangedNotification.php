<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $ip,
        private readonly string $changedAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Password Has Been Changed — ' . config('app.name'))
            ->view('emails.user.password-changed', [
                'user'      => $notifiable,
                'ip'        => $this->ip,
                'changedAt' => $this->changedAt,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ip'         => $this->ip,
            'changed_at' => $this->changedAt,
        ];
    }
}