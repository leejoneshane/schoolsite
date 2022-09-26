<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SyncCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $job;
    public $start_time;
    public $end_time;
    public $logs;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job_name, $start_time, $end_time, $logs = [])
    {
        $this->job = $job_name;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->logs = $logs;
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
        $start = Carbon::createFromTimestamp($this->start_time)->format('Y-m-d H:m:s l');
        $end = Carbon::createFromTimestamp($this->end_time)->format('Y-m-d H:m:s l');
        switch ($this->job) {
            case 'SyncFromTpedu':
                $title = '資料庫同步完成通知';
                break;
            case 'SyncToAD':
                $title = 'AD 同步完成通知';
                break;
            case 'SyncToGoogle':
                $title = 'Google 同步完成通知';
            }
        $mail = (new MailMessage)
            ->subject($title)
            ->line("同步作業於 $start 開始進行，已經於 $end 順利完成！");
        if (!empty($this->logs)) {
            $mail->line('詳細記錄如下：');
            foreach ($this->logs as $line) {
                $mail->line($line);
            }    
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
            'start' => $this->start_time,
            'end' => $this->end_time,
            'logs' => $this->logs,
        ];
    }
}
