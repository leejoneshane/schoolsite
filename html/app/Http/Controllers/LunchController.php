<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\LunchSurvey;
use App\Models\Watchdog;

class LunchController extends Controller
{

    public function index($section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        $settings = LunchSurvey::settings($section);
        if (!$section) $section = current_section();
        $sections = LunchSurvey::sections();
        if (!in_array($section, $sections)) $sections[] = $section;
        $count = (object) ['classes' => LunchSurvey::count_classes($section), 'students' => LunchSurvey::count($section)];
        $survey = $surveys = null;
        if ($user->user_type == 'Student') {
            $survey = LunchSurvey::findBy($user->uuid, $section);
        } elseif ($manager) {
            $surveys = LunchSurvey::section_survey($section)->paginate(16);
        } elseif ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $class_id = $teacher->tutor_class;
            $surveys = LunchSurvey::class_survey($class_id, $section)->paginate(16);
        }
        return view('app.lunch_survey', ['user' => $user, 'manager' => $manager, 'section' => $section, 'sections' => $sections, 'settings' => $settings, 'count' => $count, 'survey' => $survey, 'surveys' => $surveys]);
    }

    public function setting()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能設定午餐調查期程！');
        }
        $settings = LunchSurvey::settings();
        if (!$settings) $settings = LunchSurvey::latest_settings();
        return view('app.lunch_config', ['settings' => $settings]);
    }

    public function save(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能設定午餐調查期程！');
        }
        DB::table('lunch')->upsert([
            'section' => current_section(),
            'money' => $request->input('money'),
            'survey_at' => $request->input('survey'),
            'expired_at' => $request->input('expire'),
            'description' => $request->input('desc'),
        ],[
            'section',
        ]);
        $settings = LunchSurvey::settings();
        Watchdog::watch($request, '設定午餐調查期程：' . json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('lunch')->with('success', '午餐調查期程設定完成！');
    }

    public function survey(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $student = Student::find($user->uuid);
        $survey = LunchSurvey::updateOrCreate([
            'section' => current_section(),
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

    public function download($section = null)
    {
    }

}
