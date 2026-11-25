<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = "/";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'name';
    }

    /**
     * 嘗試登入；若找不到對應的 User 記錄，依帳號前四碼是否為 meps 區分學生或教職員，
     * 並從 students / teachers 資料表比對帳號與預設密碼（身分證後 6 碼），自動建立 User 記錄後登入。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $username = $request->input($this->username());
        $password = (string) $request->input('password');

        if ($username && !User::where($this->username(), $username)->exists()) {
            $isStudent = str_starts_with(strtolower($username), 'meps');
            $userType = $isStudent ? 'Student' : 'Teacher';
            $record = $isStudent
                ? Student::where('account', $username)->first()
                : Teacher::where('account', $username)->first();

            if ($record && !empty($record->idno) && $password === substr($record->idno, -6)) {
                $user = User::where('uuid', $record->uuid)->first();
                if (!$user) {
                    $email = $record->email ?: ($record->id ? ('meps' . strtolower($record->id) . '@tc.meps.tp.edu.tw') : ($record->account . '@tc.meps.tp.edu.tw'));
                    if (User::where('email', $email)->exists()) {
                        $email = 'meps' . strtolower($record->id ?: $record->account) . '@tc.meps.tp.edu.tw';
                        if (User::where('email', $email)->exists()) {
                            $email = strtolower($record->idno) . '@tc.meps.tp.edu.tw';
                        }
                    }

                    $isAdmin = false;
                    if (!$isStudent && !empty($record->character)) {
                        $characters = explode(',', $record->character);
                        if (in_array('TPECadmin1', $characters)) {
                            $isAdmin = true;
                        }
                    }

                    $user = User::create([
                        'uuid' => $record->uuid,
                        'user_type' => $userType,
                        'name' => $record->account,
                        'email' => $email,
                        'is_admin' => $isAdmin,
                    ]);
                    $user->reset_password(substr($record->idno, -6));
                    event(new Registered($user));
                }
            }
        }

        return $this->guard()->attempt(
            $this->credentials($request), $request->boolean('remember')
        );
    }
}
