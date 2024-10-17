<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RepairJob;

class RepairNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
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
        $job = RepairJob::find($this->id);
        return (new MailMessage)->subject('國語實驗國民小學修繕登記通知')
            ->view('emails.repair', ['manager' => employee($notifiable->uuid), 'job' => $job]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $job = RepairJob::find($this->id);
        return [
            'info' => $job->toJson(JSON_UNESCAPED_UNICODE),
        ];
    }
}
