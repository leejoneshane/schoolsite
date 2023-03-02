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

    //以下屬性可以批次寫入
    protected $fillable = [
        'email',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'news',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'verified',
    ];

    //提供訂閱戶電郵驗證狀態
    public function getVerifiedAttribute()
    {
        return ! is_null($this->email_verified_at);
    }

    //篩選指定郵件的訂閱戶，靜態函式
    public static function findByEmail($email)
    {
        return Subscriber::where('email', $email)->first();
    }

    //取得訂閱戶所有已訂閱電子報
    public function news()
    {
        return $this->belongsToMany('App\Models\News', 'news_subscribers', 'subscriber_id', 'news_id')->as('subscription')->withTimestamps();
    }

    //檢查訂閱戶是否已訂閱指定電子報
    public function subscripted($news_id)
    {
        $checked = false;
        foreach ($this->news as $new) {
            if ($new->id == $news_id) $checked = true;
        }
        return $checked;
    }

    //將訂閱戶設定為已驗證狀態
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    //傳送驗證信給訂閱戶
    public function sendEmailVerificationNotification()
    {
        $this->notify(new SubscriberVerifyEmail);
    }

    //取得訂閱戶的電郵，以提供給驗證機制使用
    public function getEmailForVerification()
    {
        return $this->email;
    }

    //訂閱指定的電子報
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

    //取消訂閱指定的電子報
    public function cancel($news_id)
    {
        return DB::table('news_subscribers')->where('news_id', $news_id)->where('subscriber_id', $this->id)->delete();
    }

}