<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\SubscriberVerifyEmail;
use Illuminate\Support\Facades\DB;

class Subscriber extends Model
{
    use Notifiable;

    protected $table = 'subscribers';

    protected $fillable = [
        'news_id',
        'email',
    ];

    protected $appends = [
        'verified',
    ];

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function getVerifiedAttribute()
    {
        return ! is_null($this->email_verified_at);
    }

    public static function findByEmail($email)
    {
        return Subscriber::where('email', $email)->first();
    }

    public function news()
    {
        return $this->belongsToMany('App\Models\News', 'news_subscribers', 'subscriber_id', 'news_id')->as('subscription')->withTimestamps();
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

    public function subscription($news_id)
    {
        $rec = DB::table('news_subscribers')->where('news_id', $news_id)->where('subscriber_id', $this->id)->first();
        if ($rec) {
            return false;
        } else {
            return DB::table('news_subscribers')->insert([
                'news_id' => $news_id,
                'subscriber_id' => $this->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function cancel($news_id)
    {
        return DB::table('news_subscribers')->where('news_id', $news_id)->where('subscriber_id', $this->id)->delete();
    }

}