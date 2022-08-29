<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialiteAccount;

class SocialiteController extends Controller
{

    public function redirect($provider)
    {
        return Socialite::with($provider)->redirect();
    }

    public function handleCallback(Request $request, $provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
            $userID = $user->getId();
            if (Auth::check()) {
                $myuser = Auth::user();
                SocialiteAccount::create([
                    'uuid' => $myuser->uuid,
                    'socialite' => $provider,
                    'userID' => $userID,
                ]);

                return redirect()->route('socialite')->with('success', "$provider 社群帳號： $userID 綁定完成！");
            } else {
                $account = SocialiteAccount::where('userId', $userID)->where('socialite', $provider)->first();
                if ($account) {
                    $myuser = $account->user;
                    if ($myuser) {
                        Auth::login($myuser);
                        return redirect()->route('home');
                    }
                }

                return redirect()->route('login')->with('error', '這個社群帳號尚未綁定使用者，所以無法登入！');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', "使用 $provider 帳號登入失敗");
        }
    }

    public function socialite(Request $request)
    {
        $user = Auth::user();
        $google = false;
        $facebook = false;
        $yahoo = false;
        $line = false;
        $accounts = $user->socialite_accounts;
        foreach ($accounts as $a) {
            if ($a->socialite == 'google') {
                $google = $a;
            }
            if ($a->socialite == 'facebook') {
                $facebook = $a;
            }
            if ($a->socialite == 'yahoo') {
                $yahoo = $a;
            }
            if ($a->socialite == 'line') {
                $line = $a;
            }
        }

        return view('auth.socialiteManager', ['google' => $google, 'facebook' => $facebook, 'yahoo' => $yahoo, 'line' => $line]);
    }

    public function removeSocialite(Request $request)
    {
        $user = Auth::user();
        $socialite = $request->input('socialite');
        $userid = $request->input('userid');
        $account = SocialiteAccount::where('uuid', $user->uuid)->where('socialite', $socialite)->where('userId', $userid)->delete();

        return redirect()->route('socialite');
    }
}