<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Events\SubscriberVerified;
use App\Models\Subscriber;
use App\Models\News;

class SubscriberController extends Controller
{
    public function store(Request $request, $id)
    {
        $news = News::find($id);
        $subscriber = Subscriber::create([
            'news_id' => $id,
            'email' => $request->input('email'),
        ]);

        if (config('subscribers.verify')) {
            $subscriber->sendEmailVerificationNotification();
            return redirect()->route('home')
                ->with('success', '電子報：'.$news->name.'的驗證信已經寄送到您的電子郵件信箱，請收信並進行驗證！');
        }

        return redirect()->route('home')
            ->with('success', '恭喜您！您已經成功訂閱電子報：'.$news->name.'!');
    }

    public function delete(Request $request, $id)
    {
        $sub = Subscriber::where('news_id', $id)->where('email', $request->input('email'))->firstOrFail();
        if ($sub) $sub->delete();
        return view('subscribe.deleted');
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

        $news = $subscriber->news->name;
        if ($subscriber->hasVerifiedEmail()) {
            return redirect()->route('home')->with('success', '您先前已經訂閱過電子報：'.$news.'！');
        }

        if ($subscriber->markEmailAsVerified()) {
            broadcast(new SubscriberVerified($subscriber));
        }

        return redirect()->route('home')->with('success', '電子報訂閱成功！');
    }
}