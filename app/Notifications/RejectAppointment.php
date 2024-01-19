<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RejectAppointment extends Notification
{
    public $name;
    public $transaction_fee;
    public $remarks;

    public function __construct($name, $transaction_fee,$remarks)
    {
        $this->name = $name;
        $this->transaction_fee = $transaction_fee;       
        $this->remarks = $remarks;

    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('MEKANIGO CONFIRMATION')
            ->greeting('Good day '.$this->name.'!')
            ->line('You are receiving this email because your appointment was rejected due to the following reasons.')
            ->line($this->remarks)
            ->line('A refund of '.$this->transaction_fee.' for the transaction fee will be processed within 3-4 business days.')
            ->line('Feel free to find another mechanic who might suits your needs!')
            ->line('Thank you for using our app!')
            ->salutation('MEKANIGO Team');
    }
}
