<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Subscriber;
use App\Models\News;

class SubscriberController extends Controller
{
    public function subscription(Request $request, $id)
    {
        $news = News::find($id);
        $subscriber = Subscriber::where('email', $request->input('email'))->first();
        if (!$subscriber) {
            Subscriber::create([
                'email' => $request->input('email'),
            ]);    
        }

        if ($subscriber) {
            DB::table('news_subscribers')->insert([
                'news_id' => $id,
                'subscriber_id' => $subscriber->id,
            ]);
        }

        if (config('subscribers.verify')) {
            if (! $subscriber->hasVerifiedEmail()) {
                $subscriber->sendEmailVerificationNotification();
                return redirect()->route('home')
                    ->with('success', '電子報：'.$news->name.'的驗證信已經寄送到您的電子郵件信箱，請收信並進行驗證！');
            }
        }

        return redirect()->route('home')
            ->with('success', '恭喜您！您已經成功訂閱電子報：'.$news->name.'!');
    }

    public function remove(Request $request, $id)
    {
        $news = News::find($id);
        $subscriber = Subscriber::where('email', $request->input('email'))->first();
        if ($subscriber) {
            DB::table('news_subscribers')->where('news_id', $id)->where('subscriber_id', $subscriber->id)->delete();
        }
        if (empty($subscriber->news)) {
            $subscriber->delete();    
        }
        return redirect()->route('home')
            ->with('success', '您已經取消訂閱電子報：'.$news->name.'!');
    }

    public function verify(Request $request, $id, $hash)
    {
        $subscriber = Subscriber::find($id);
        if (!hash_equals((string) $id, (string) $subscriber->getKey())) {
            return redirect()->route('home')->with('error', '您的電子郵件信箱驗證失敗！');
        }

        if (!hash_equals((string) $hash, sha1($subscriber->getEmailForVerification()))) {
            return redirect()->route('home')->with('error', '您的電子郵件信箱驗證失敗！');
        }

        if ($subscriber->hasVerifiedEmail()) {
            return redirect()->route('home')->with('success', '您先前已經是電子報訂戶，無需再次驗證！');
        }

        if ($subscriber->markEmailAsVerified()) {
            return redirect()->route('home')->with('success', '恭喜您成為國語實小電子報訂戶！');
        }

        return redirect()->route('home')->with('success', '因為不明原因，驗證失敗！');
    }
}