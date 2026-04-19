<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $token,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = $this->buildResetUrl($notifiable);

        return (new MailMessage)
            ->subject('Reset Your Password — ' . config('app.name'))
            ->view('emails.auth.reset-password', [
                'user'     => $notifiable,
                'resetUrl' => $resetUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    // Reset URL Builder

    private function buildResetUrl(object $notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}