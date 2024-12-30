<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Watchdog;
use App\Providers\TpeduServiceProvider;

class TpeduController extends Controller
{

    private $sso = null;

    public function __construct()
    {
        if (is_null($this->sso)) {
            $this->sso = new TpeduServiceProvider;
        }
    }

    public function redirect(Request $request)
    {
        if (Auth::check()) return redirect()->route('home');
//        Watchdog::watch($request, '嘗試登入網站！');
        return redirect()->away(
            $this->sso->login()
        );
    }

    public function handleCallback(Request $request)
    {
        try {
            $auth_code = $request->input('code');
            if (!$auth_code) {
                return redirect()->route('login')->with('error', '無法取得介接驗證碼，因此無法登入！');
            }
            if ($auth_code) {
                $result = $this->sso->get_tokens($auth_code);
                if (!$result) {
                    return redirect()->route('login')->with('error', '無法從單一身份驗證取得存取金鑰，因此無法登入！');
                }
                $uuid = $this->sso->who();
                if (!$uuid) {
                    return redirect()->route('login')->with('error', '無法從單一身份驗證取得您的唯一編號，因此無法登入！');
                }
                $user = User::where('uuid', $uuid)->first();
                if ($user) { //user exists
                    if ($user->user_type == 'Teacher') {
                        $tpuser = Teacher::find($uuid);
                        if (!$tpuser) {
                            return redirect()->route('login')->with('error', '您已經從本校離職，因此無法登入！');
                        } else {
                            if ($tpuser->expired()) $this->sso->fetch_user($uuid);
                        }
                    }
                    if ($user->user_type == 'Student') {
                        $tpuser = Student::find($uuid);
                        if (!$tpuser) {
                            return redirect()->route('login')->with('error', '只有目前就讀本校的學生才能登入！');
                        } else {
                            if ($tpuser->expired()) $this->sso->fetch_user($uuid);
                        }
                    }
                    //Log::notice($user->toJson());
                    Auth::login($user, true);
                    Watchdog::watch($request, '登入網站');
                    return redirect()->route('home');
                } else { //new user
                    $tpuser = false;
                    $result = $this->sso->fetch_user($uuid);
                    $temp = Student::find($uuid);
                    if ($temp) {
                        $user_type = 'Student';
                        $tpuser = $temp;
                    } else {
                        $temp = Teacher::find($uuid);
                        if ($temp) {
                            $user_type = 'Teacher';
                            $tpuser = $temp;
                        }
                    }
                    if ($tpuser) {
                        $email = $tpuser->email; 
                        if (!$email) {
                            $email = 'meps' . $tpuser->id . '@tc.meps.tp.edu.tw';
                        }
                        $user = User::create([
                            'uuid' => $uuid,
                            'user_type' => $user_type,
                            'name' => $tpuser->account,
                            'email' => $email,
                        ]);
                        $user->reset_password(substr($tpuser->idno, -6));
                        if ($tpuser->character) {
                            $characters = explode(',', $tpuser->character);
                            if (in_array('TPECadmin1', $characters)) {
                                $user->is_admin = true;
                                $user->save();
                            }
                        }
                        if ($user) {
                            Auth::login($user, true);
                            event(new Registered($user));
                            return redirect()->route('home');
                        } else {
                            return redirect()->route('login')->with('error', '建立您的登入帳號失敗，請聯絡系統管理人員為您排除障礙！');
                        }
                    } else {
                        return redirect()->route('login')->with('error', '您的帳號並非隸屬於本校，因此無法登入！');
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', "使用臺北市單一身份驗證登入失敗");
        }
    }

}