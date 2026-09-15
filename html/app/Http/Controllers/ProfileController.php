<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Domain;
use App\Models\Subject;
use App\Models\Watchdog;

class ProfileController extends Controller
{
    public $year;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->year = current_year();
    }

    /**
     * 顯示教師個人資料編輯頁面
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '學生身份不提供此功能！');
        }

        $teacher = Teacher::find($user->uuid);
        if (!$teacher) {
            return redirect()->route('home')->with('error', '找不到對應的教師資料！');
        }

        $referer = $request->headers->get('referer') ?: route('home');
        $classes = Classroom::all();
        $domains = Domain::all();
        $subjects = Subject::all();
        $assignment = DB::table('assignment')->where('year', $this->year)->where('uuid', $user->uuid)->get();

        return view('app.profile', [
            'referer' => $referer,
            'teacher' => $teacher,
            'assignment' => $assignment,
            'classes' => $classes,
            'domains' => $domains,
            'subjects' => $subjects,
        ]);
    }

    /**
     * 儲存更新後的教師個人資料
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '學生身份不提供此功能！');
        }

        $uuid = $user->uuid;
        $teacher = Teacher::find($uuid);
        if (!$teacher) {
            return redirect()->route('home')->with('error', '找不到對應的教師資料！');
        }

        // 依需求，「擔任職務」和「特殊身份註記」欄位隱藏不允許修改，故此處完全不更動 roles 與 character

        // 更新隸屬領域
        DB::table('belongs')->where('year', $this->year)->where('uuid', $uuid)->delete();
        $new_domain = $request->input('domain');
        if ($new_domain) {
            DB::table('belongs')->insert([
                'year' => $this->year,
                'uuid' => $uuid,
                'domain_id' => $new_domain,
            ]);
        }

        // 更新配課資訊
        $new_classes = $request->input('classes') ?? [];
        $new_subjects = $request->input('subjects') ?? [];
        $old_assign = $teacher->assignment();
        foreach ($old_assign as $old) {
            $found = false;
            foreach ($new_classes as $i => $nc) {
                if ($nc == $old->class_id && isset($new_subjects[$i]) && $new_subjects[$i] == $old->subject_id) {
                    $found = true;
                    unset($new_classes[$i]);
                    unset($new_subjects[$i]);
                }
            }
            if (!$found) {
                DB::table('assignment')->where('id', $old->id)->delete();
            }
        }
        if (is_array($new_classes)) $new_classes = array_values($new_classes);
        if (is_array($new_subjects)) $new_subjects = array_values($new_subjects);
        if (!empty($new_classes)) {
            for ($i = 0; $i < count($new_classes); $i++) {
                if (isset($new_classes[$i]) && isset($new_subjects[$i])) {
                    DB::table('assignment')->insertOrIgnore([
                        'year' => $this->year,
                        'uuid' => $uuid,
                        'class_id' => $new_classes[$i],
                        'subject_id' => $new_subjects[$i],
                    ]);
                }
            }
        }

        // 更新教師基本資料
        $teacher->idno = $request->input('idno');
        $teacher->sn = $request->input('sn');
        $teacher->gn = $request->input('gn');
        $teacher->realname = $request->input('sn') . $request->input('gn');
        $teacher->gender = $request->input('gender');
        $teacher->birthdate = $request->input('birth');
        $teacher->email = $request->input('email');
        $teacher->mobile = $request->input('mobile');
        $teacher->telephone = $request->input('telephone');
        $teacher->address = $request->input('address');
        $teacher->www = $request->input('www');
        $teacher->save();

        // 同步更新關聯 User 資料表之姓名與 Email
        $user->name = $teacher->realname;
        $user->email = $teacher->email;
        $user->save();

        Watchdog::watch($request, '教師更新個人資訊：' . $teacher->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $referer = $request->input('referer') ? urldecode($request->input('referer')) : route('profile.edit');
        return redirect($referer)->with('success', '個人資料已經更新完成！');
    }
}
