<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Meeting;
use App\Models\News;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewsLetter;

class SendMeeting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 12000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $news = News::findByModel('App\Models\Meeting');
        $subscribers = $news->verified;
        if ($news->loop['loop'] == 'weekly') {
            //$search = Carbon::getDays()[$news->loop['day']];
            //$last = Carbon::createFromTimeStamp(strtotime("last $search", Carbon::now()->timestamp));
            $meets = Meeting::inTimeOpen(Carbon::now()->format('Y-m-d'));
            if ($meets->count() > 0 && $subscribers->count() > 0) {
                foreach ($subscribers as $sub) {
                    Notification::sendNow($sub, new NewsLetter($news->name, Meeting::template, [ 'meets' => $meets ]));
                }
            }
        }
    }
}
