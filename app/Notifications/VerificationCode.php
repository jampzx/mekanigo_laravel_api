<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationCode extends Notification
{
    public $verificationCode;

    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('MEKANIGO EMAIL VERIFICATION')
            ->greeting('Hello!')
            ->line('Thank you for registering with MEKANIGO. To complete your registration, please verify your email address.')
            ->line('Your verification code:')
            ->line($this->verificationCode)
            ->line('If you did not register on MEKANIGO, you can safely ignore this email.')
            ->line('Thank you for choosing MEKANIGO!')
            ->salutation('MEKANIGO Team');
    }
}
