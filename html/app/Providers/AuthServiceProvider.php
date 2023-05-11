<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         'App\Models\IcsCalendar' => 'App\Policies\IcsCalendarPolicy',
         'App\Models\IcsEvent' => 'App\Policies\IcsEventPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('驗證您的電子郵件信箱')
                ->line('請點擊下方按鈕，以便驗證您的電子郵件信箱！')
                ->action('驗證我的信箱', $url)
                ->line('如果您並未在'.config('app.name').'進行首次登入，請勿理會此封郵件！');
        });
    }
}
