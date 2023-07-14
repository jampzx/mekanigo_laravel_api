<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class Donation extends Notification
{
    public $name;
    public $age;
    public $contact_number;
    public $donation_type;
    public $donation_info;

    public function __construct($name, $age, $contact_number, $donation_type, $donation_info)
    {
        $this->name = $name;
        $this->age = $age;
        $this->contact_number = $contact_number;
        $this->donation_type = $donation_type;
        $this->donation_info = $donation_info;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('DONITE CONFIRMATION')
            ->greeting('Thank you for your help!')
            ->line('You are receiving this email because you made a donation using our app.')
            ->line('Your donation details are listed below:')
            ->line('Name: ' . $this->name)
            ->line('Age: ' . $this->age)
            ->line('Contact Number: ' . $this->contact_number)
            ->line('Donation Type: ' . $this->donation_type)
            ->line('Donation Info: ' . $this->donation_info)
            ->line('If you did not make any donation, no further action is required.')
            ->line('Thank you for using our application!')
            ->salutation('Donite Team');
    }
}
