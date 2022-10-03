<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClubNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $info = '';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->info = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $enroll = $notifiable; 
        $club = $enroll->club;
        $student = $enroll->student;
        return (new MailMessage)->subject('國語實驗國民小學學生課外社團通知')
            ->view('emails.club', ['enroll' => $enroll, 'club' => $club, 'student' => $student, 'info' => $this->info]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'info' => $this->info,
        ];
    }
}
