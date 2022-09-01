<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
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
        return redirect()->away(
            $this->sso->login()
        );
    }

    public function handleCallback(Request $request)
    {
        try {
            $auth_code = $request->input('code');
            if ($auth_code) {
                $this->sso->get_tokens($auth_code);
                $uuid = $this->sso->who();
                $user = User::where('uuid', $uuid)->first();
                if ($user) { //user exists
                    if ($user->email != $user->profile->email) {
                        $user->email = $user->profile->email;
                        $user->save();
                    }
                    Auth::login($user);
                    return redirect()->route('home');
                } else { //new user
                    $tpuser = false;
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
                        if ($tpuser->expired()) $this->sso->fetch_user($uuid);
                        $user = User::create([
                            'uuid' => $uuid,
                            'user_type' => $user_type,
                            'name' => $tpuser->account,
                            'email' => $tpuser->email,
                            'password' => Hash::make(substr($tpuser->idno, -6)),
                        ]);
                        $characters = explode(',', $tpuser->character);
                        if (in_array('TPECadmin1', $characters)) {
                            $user->is_admin = true;
                            $user->save();
                        }
                    } else {
                        return redirect()->route('login')->with('error', "您的帳號並非隸屬於本校，因此無法登入！");
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', "使用臺北市單一身份驗證登入失敗");
        }
    }

}