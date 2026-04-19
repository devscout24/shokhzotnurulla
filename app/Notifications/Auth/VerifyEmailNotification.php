<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->buildVerificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address — ' . config('app.name'))
            ->view('emails.auth.verify-email', [
                'user'            => $notifiable,
                'verificationUrl' => $verificationUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    // ─── Verification URL Builder ─────────────────────────────────────────────

    private function buildVerificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}