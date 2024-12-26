<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\LunchSurvey;
use App\Models\Watchdog;
use App\Exports\LunchExport;
use App\Exports\LunchClassExport;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LunchController extends Controller
{

    public function index(Request $request, $section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        $sections = LunchSurvey::sections();
        $next = next_section();
        if (!in_array($next, $sections)) $sections[] = $next;
        if (!$section) $section = $next;
        $settings = LunchSurvey::settings($section);
        $image = public_path('images/lunch.png');
        if (!file_exists($image)) {
            QrCode::format('png')->size(300)
                ->merge(public_path('images/logo.jpg'), 0.25, true)
                ->generate($settings->qrcode, $image);
        }
        $count = (object) ['classes' => LunchSurvey::count_classes($section), 'students' => LunchSurvey::count($section)];
        $survey = $surveys = $classes = null;
        $classes = Classroom::all();
        if ($user->user_type == 'Student') {
            $class_id = employee()->class_id;
            $classroom = Classroom::find($class_id);
            $survey = LunchSurvey::findBy($user->uuid, $section);    
        } elseif ($manager) {
            $class_id = $request->input('class');
            if (!$class_id) $class_id = '101';
            $classroom = Classroom::find($class_id);
            $surveys = LunchSurvey::class_survey($class_id, $section);
        } elseif ($user->user_type == 'Teacher') {
            $class_id = employee()->tutor_class;
            if (!$class_id) {
                return redirect()->route('home')->with('error', '只有導師和管理員才能瀏覽午餐調查！');
            } 
            $classroom = Classroom::find($class_id);
            $surveys = LunchSurvey::class_survey($class_id, $section);
        }
        return view('app.lunch_survey', ['user' => $user, 'manager' => $manager, 'section' => $section, 'sections' => $sections, 'settings' => $settings, 'image' => $image, 'count' => $count, 'survey' => $survey, 'classroom' => $classroom, 'classes' => $classes, 'surveys' => $surveys]);
    }

    public function setting($section)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能設定午餐調查期程！');
        }
        $settings = LunchSurvey::settings();
        if (!$settings) $settings = LunchSurvey::latest_settings();
        return view('app.lunch_config', ['settings' => $settings, 'section' => $section]);
    }

    public function save(Request $request, $section)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能設定午餐調查期程！');
        }
        $settings = LunchSurvey::settings($section);
        $last = LunchSurvey::latest_settings();
        if ($request->input('qrcode') && $last->qrcode != $request->input('qrcode')) {
            $image = public_path('images/lunch.png');
            QrCode::format('png')->size(300)
                ->merge(public_path('images/logo.jpg'), 0.25, true)
                ->generate($request->input('qrcode'), $image);
        }
        DB::table('lunch')->upsert([
            'section' => $section,
            'money' => $request->input('money'),
            'survey_at' => $request->input('survey'),
            'expired_at' => $request->input('expire'),
            'description' => $request->input('desc'),
            'qrcode' => $request->input('qrcode'),
        ],[
            'section',
        ]);
        $settings = LunchSurvey::settings($section);
        Watchdog::watch($request, '設定午餐調查期程：' . json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('lunch')->with('success', '午餐調查期程設定完成！');
    }

    public function survey(Request $request)
    {
        $user = Auth::user();
        $student = Student::find($user->uuid);
        $survey = LunchSurvey::updateOrCreate([
            'section' => $request->input('section'),
            'uuid' => $student->uuid,
        ],[
            'class_id' => $student->class_id,
            'seat' => $student->seat,
            'by_school' => ($request->input('meal') == 'by_school') ? 1 : 0,
            'vegen' => ($request->input('type') == 'vegen') ? 1 : 0,
            'milk' => ($request->input('milk') == 'yes') ? 1 : 0,
            'by_parent' => ($request->input('meal') == 'by_parent') ? 1 : 0,
            'boxed_meal' => ($request->input('meal') == 'boxed_meal') ? 1 : 0,
        ]);
        Watchdog::watch($request, '提交午餐調查表：' . $survey->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('lunch')->with('success', '已為您儲存午餐調查表！');
    }

    public function downloadAll($section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        if (!$section) $section = next_section();
        if (LunchSurvey::count($section) == 0) {
            return redirect()->route('lunch', ['section' => $section])->with('error', '沒有調查結果可以匯出！');
        } else {
            $filename = substr($section, 0, -1) . '學年度' . ((substr($section, -1) == '1') ? '上' : '下') . '學期午餐調查結果彙整';
            return (new LunchExport($section))->download("$filename.xlsx");    
        }
    }

    public function download($section, $class_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        if (!$section) $section = next_section();
        if (LunchSurvey::countByClass($section, $class_id) == 0) {
            return redirect()->route('lunch', ['section' => $section])->with('error', '沒有調查結果可以匯出！');
        } else {
            $filename = substr($section, 0, -1) . '學年度' . ((substr($section, -1) == '1') ? '上' : '下') . '學期午餐調查結果彙整';
            return (new LunchClassExport($section, $class_id))->download("$filename.xlsx");    
        }
    }

}
