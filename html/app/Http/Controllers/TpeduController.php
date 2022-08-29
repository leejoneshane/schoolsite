<?php

namespace App\Http\Controllers;

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
            $uuid = $request->uuid;
            $user = User::find($uuid);
            if ($user) { //user exists
                if ($user->email != $user->profile()->email) {
                    $user->email = $user->profile()->email;
                    $user->save();    
                }
                Auth::login($user);
                return redirect()->route('home');
            } else { //new user
                if ($this->sso->fetch_user($uuid)) {
                    $user_type = $this->sso->user_type($uuid);
                    if ($user_type == 'Student') {
                        $tpuser = Student::find($uuid);
                    } else {
                        $tpuser = Teacher::find($uuid);
                    }
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
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', "使用臺北市單一身份驗證登入失敗");
        }
    }

}