<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Events\SubscriberCreated;
use App\Events\SubscriberDeleted;
use App\Notifications\SubscriberVerifyEmail;

class Subscriber extends Model
{
    use Notifiable;

    protected $table = 'subscribers';

    protected $fillable = [
        'news_id',
        'email',
    ];

    public function news()
    {
        return $this->belongsToMany('App\Models\News', 'news_subscribers', 'subscriber_id', 'news_id')->as('subscription')->withTimestamps();
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new SubscriberVerifyEmail);
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }
}