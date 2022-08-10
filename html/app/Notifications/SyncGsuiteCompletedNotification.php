<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SyncGsuiteCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $job;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job)
    {
        $this->job = $job;
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
        $start = Carbon::createFromTimestamp($this->job->start)->format('Y-m-d H:m:s l');
        $end = Carbon::createFromTimestamp($this->job->end)->format('Y-m-d H:m:s l');
        $times = $this->job->attempts();
        $mail = (new MailMessage)
            ->subject('Google 同步完成通知')
            ->line("Google 同步作業於 $start 開始進行，中斷重試共 $times 次,已經於 $end 順利完成！");
        if ($this->job->leave == 'onduty') {
            $mail->line('本次同步作業為同步在校生帳號，詳細記錄如下：');
        } elseif ($this->job->leave == 'suspend') {
            $mail->line('本次同步作業為停用畢業生帳號，詳細記錄如下：');
        } else {
            $mail->line('本次同步作業為移除畢業生帳號，詳細記錄如下：');
        }
        foreach ($this->job->log as $line) {
            $mail->line($line);
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
            'start' => $this->job->start,
            'end' => $this->job->end,
            'times' => $this->job->attempts(),
            'log' => $this->log,
        ];
    }
}
