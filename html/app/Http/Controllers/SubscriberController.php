<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Subscriber;
use App\Models\News;
use App\Models\Watchdog;
use Carbon\Carbon;

class SubscriberController extends Controller
{
    public function index($email = null)
    {
        $subscriber = null;
        if ($email) {
            $subscriber = Subscriber::findByEmail($email);
        } else {
            if (Auth::check()) {
                $email = Auth::user()->email;
                $subscriber = Subscriber::findByEmail($email);
            }
        }
        $news = News::all();
        $this->removeUnverify();
        return view('app.subscriber', ['email' => $email, 'subscriber' => $subscriber, 'news' => $news]);
    }

    public function resent(Request $request, $email = null)
    {
        $subscriber = null;
        if ($email) {
            $subscriber = Subscriber::findByEmail($email);
        } else {
            if (Auth::check()) {
                $email = Auth::user()->email;
                $subscriber = Subscriber::findByEmail($email);
            }
        }
        $subscriber->sendEmailVerificationNotification();
        Watchdog::watch($request, '寄送郵件信箱確認信到 ' . $subscriber->email);
        return redirect()->route('subscriber')->with('success', '驗證信已經寄送到您的電子郵件信箱，請收信並進行驗證！');
    }

    public function subscription(Request $request, $news = null)
    {
        if (!$news) return redirect()->route('subscriber');
        $news = News::find($news);
        $subscriber = Subscriber::findByEmail($request->input('email'));
        if (!$subscriber) {
            $subscriber = Subscriber::create([
                'email' => $request->input('email'),
            ]);
            Watchdog::watch($request, '建立訂閱戶：' . $subscriber->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        if ($subscriber) {
            $subscriber->subscription($news->id);
            Watchdog::watch($request, $subscriber->email . '訂閱電子報：' . $news->name);
        }

        if (config('subscribers.verify')) {
            if (!($subscriber->verified)) {
                $subscriber->sendEmailVerificationNotification();
                Watchdog::watch($request, '寄送郵件信箱確認信到 ' . $subscriber->email);
                return redirect()->route('subscriber')->with('success', '電子報：'.$news->name.'的驗證信已經寄送到您的電子郵件信箱，請收信並進行驗證！');
            }
        }

        return redirect()->route('home')->with('success', '恭喜您！您已經成功訂閱電子報：'.$news->name.'!');
    }

    public function remove(Request $request, $news = null)
    {
        if (!$news) return redirect()->route('subscriber');
        $news = News::find($news);
        $subscriber = Subscriber::findByEmail($request->input('email'));
        if ($subscriber) {
            $subscriber->cancel($news->id);
            Watchdog::watch($request, $subscriber->email . '取消訂閱電子報：' . $news->name);
        }
        if (empty($subscriber->news)) {
            Watchdog::watch($request, '移除訂閱戶：' . $subscriber->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $subscriber->delete();
        }
        return redirect()->route('subscriber')->with('success', '您已經取消訂閱電子報：'.$news->name.'!');
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

        if ($subscriber->verified) {
            return redirect()->route('home')->with('success', '您先前已經是電子報訂戶，無需再次驗證！');
        }

        if ($subscriber->markEmailAsVerified()) {
            Watchdog::watch($request, '訂閱戶郵件驗證成功：' . $subscriber->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('home')->with('success', '恭喜您成為國語實小電子報訂戶！');
        }

        return redirect()->route('home')->with('success', '因為不明原因，驗證失敗！');
    }

    public function removeUnverify($days = null) {
        if (!$days) $days = 30;
        $expired = Carbon::now()->subDays($days);
        Subscriber::whereNull('email_verified_at')->where('created_at', '<', $expired)->delete();
    }
}