<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    /**
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirma tu correo electronico - '.config('brand.name'))
            ->view('mail.verify-email', [
                'userName' => $notifiable->name,
                'verificationUrl' => $verificationUrl,
                'logoUrl' => url('/images/logo-cafe.png'),
            ])
            ->text('mail.verify-email-text', [
                'userName' => $notifiable->name,
                'verificationUrl' => $verificationUrl,
            ]);
    }
}
