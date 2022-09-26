<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\News;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewsLetter;

class SendNewsLetters implements ShouldQueue
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
        $monthday = Carbon::today()->day;
        $weekday = Carbon::today()->dayOfWeek;
        $news = News::where('cron', 'monthly.'.$monthday)->orWhere('cron', 'weekly.'.$weekday)->get();
        foreach ($news as $new) {
            $subscribers = $new->subscribers;
            $data_model = new $new->model;
            $view = $data_model::template();
            $content = $data_model::newsletter();
            Notification::sendNow($subscribers, new NewsLetter($new->name, $view, $content));
        }
    }
}
