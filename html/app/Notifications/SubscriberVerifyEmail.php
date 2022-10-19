<?php

namespace App\Notifications;

use Illuminate\Support\Facades\URL;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;
use App\Models\News;

class SubscriberVerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $verificationUrl = $this->verificationUrl($notifiable);
        $mail = new MailMessage();
        $mail->subject(config('subscribers.mail.verify.subject'));
        $mail->greeting(config('subscribers.mail.verify.greeting'));
        if (!empty(config('subscribers.mail.verify.content'))) {
            foreach (config('subscribers.mail.verify.content') as $value) {
                $mail->line($value);
            }
        } else {
            $mail->line('請點擊下方的按鈕，以便驗證您的電子郵件信箱。');
        }
        $mail->action(config('subscribers.mail.verify.action'), $verificationUrl);
        if (!empty(config('subscribers.mail.verify.footer'))) {
            foreach (config('subscribers.mail.verify.footer') as $value) {
                $mail->line($value);
            }
        } else {
            $mail->line('如果您並未訂閱我們的電子報，請直接刪除無須理會本信件！');
        }

        return $mail;
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
            //
        ];
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verify',
            Carbon::now()->addMinutes(config('subscribers.mail.verify.expiration')),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}