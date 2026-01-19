<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\LunchSurvey;
use App\Models\LunchCafeteria;
use App\Models\LunchTeacher;
use App\Models\Watchdog;
use App\Exports\LunchExport;
use App\Exports\LunchClassExport;
use App\Exports\LunchGradeExport;
use App\Exports\LunchLocationExport;
use App\Exports\LunchPaymentExport;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LunchController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if ($user->user_type == 'Student') {
            return redirect()->route('lunch.survey');
        } elseif ($user->user_type == 'Teacher') {
            $is_tutor = employee()->tutor_class;
            if ($manager || $is_tutor) {
                return redirect()->route('lunch.teacher');
            } else {
                return redirect()->route('lunch.teacher.edit');
            }
        }
    }

    public function survey(Request $request, $section = null)
    {
        $user = Auth::user();
        if ($user->user_type != 'Student') {
            return redirect()->route('lunch');
        }

        $sections = LunchSurvey::sections();
        $next = next_section();
        if (!in_array($next, $sections))
            $sections[] = $next;
        if (!$section)
            $section = $next;
        $settings = LunchSurvey::settings($section);
        $image = public_path('images/lunch.png');
        if (!file_exists($image)) {
            QrCode::format('png')->size(300)
                ->merge(public_path('images/logo.jpg'), 0.25, true)
                ->generate($settings->qrcode, $image);
        }

        $class_id = employee()->class_id;
        $classroom = Classroom::find($class_id);
        $survey = LunchSurvey::findBy($user->uuid, $section);
        $manager = false; // Students are not managers here

        return view('app.lunch_survey', [
            'user' => $user,
            'manager' => $manager,
            'section' => $section,
            'sections' => $sections,
            'settings' => $settings,
            'image' => $image,
            'survey' => $survey,
            'classroom' => $classroom
        ]);
    }

    public function manager(Request $request, $section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        $is_tutor = ($user->user_type == 'Teacher' && employee()->tutor_class);

        if (!$manager && !$is_tutor) {
            return redirect()->route('lunch.teacher');
        }

        $sections = LunchSurvey::sections();
        $next = next_section();
        if (!in_array($next, $sections))
            $sections[] = $next;
        if (!$section)
            $section = $next;

        $count = (object) ['classes' => LunchSurvey::count_classes($section), 'students' => LunchSurvey::count($section)];
        $surveys = $classes = $classroom = null;
        $classes = Classroom::all();

        if ($manager) {
            $class_id = $request->input('class');
            if (!$class_id)
                $class_id = '101';
            $classroom = Classroom::find($class_id);
            $surveys = LunchSurvey::class_survey($class_id, $section);
        } elseif ($is_tutor) {
            $class_id = employee()->tutor_class;
            $classroom = Classroom::find($class_id);
            $surveys = LunchSurvey::class_survey($class_id, $section);
        }

        return view('app.lunch_manager', [
            'user' => $user,
            'manager' => $manager,
            'section' => $section,
            'sections' => $sections,
            'count' => $count,
            'classroom' => $classroom,
            'classes' => $classes,
            'surveys' => $surveys
        ]);
    }

    public function teacherDashboard()
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher' && !$user->is_admin) {
            return redirect()->route('lunch');
        }
        return view('app.lunch_teacher_dashboard');
    }

    public function teacherLunch($section = null)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher' && !$user->is_admin) {
            return redirect()->route('lunch');
        }

        $next = next_section();
        if (!$section)
            $section = $next;

        $teacher = LunchTeacher::where('uuid', $user->uuid)->where('section', $section)->first();
        $cafeterias = LunchCafeteria::all();

        $in_class_opt = $cafeterias->first(function ($value) {
            return strpos($value->description, '隨班用餐') !== false;
        });
        $in_class_id = $in_class_opt ? $in_class_opt->id : null;

        $fixed_days = [];
        $is_tutor = ($user->user_type == 'Teacher' && employee()->tutor_class);

        if ($is_tutor) {
            $class_id = employee()->tutor_class;
            $grade = substr($class_id, 0, 1);

            if ($grade == 1 || $grade == 2) {
                // Low Grade: Thu(3)
                $fixed_days[3] = $in_class_id;
            } elseif ($grade == 3 || $grade == 4) {
                // Middle Grade: Mon(0), Tue(1), Thu(3)
                $fixed_days[0] = $in_class_id;
                $fixed_days[1] = $in_class_id;
                $fixed_days[3] = $in_class_id;
            } elseif ($grade >= 5) {
                // High Grade: Mon(0), Tue(1), Thu(3), Fri(4)
                $fixed_days[0] = $in_class_id;
                $fixed_days[1] = $in_class_id;
                $fixed_days[3] = $in_class_id;
                $fixed_days[4] = $in_class_id;
            }
        } else {
            if ($in_class_id) {
                $cafeterias = $cafeterias->reject(function ($value) use ($in_class_id) {
                    return $value->id == $in_class_id;
                });
            }
        }

        return view('app.lunch_teacher_edit', [
            'section' => $section,
            'teacher' => $teacher,
            'cafeterias' => $cafeterias,
            'fixed_days' => $fixed_days,
            'in_class_id' => $in_class_id
        ]);
    }

    public function storeTeacherLunch(Request $request)
    {
        $user = Auth::user();
        $section = $request->input('section');

        $data = [
            'section' => $section,
            'uuid' => $user->uuid,
            'tutor' => $request->has('tutor'),
            'vegen' => $request->has('vegen'),
            'milk' => $request->has('milk'),
            'weekdays' => $request->input('weekdays', []), // array of booleans/checkboxes
            'places' => $request->input('places', []),
        ];

        // Ensure weekdays and places are properly formatted as arrays if needed, though Eloquent cast should handle arrays.
        // Checkboxes return "1" if checked, missing if not. Let's normalize weekdays.
        $weekdays = [];
        $places = [];
        $inputPlaces = $data['places'];

        for ($i = 0; $i < 5; $i++) {
            $weekdays[$i] = isset($data['weekdays'][$i]) ? true : false;
            if ($weekdays[$i]) {
                $places[$i] = (isset($inputPlaces[$i]) && $inputPlaces[$i]) ? $inputPlaces[$i] : 0;
            } else {
                $places[$i] = 0;
            }
        }
        $data['weekdays'] = $weekdays;
        $data['places'] = $places;

        // Update upsert logic or updateOrCreate
        LunchTeacher::updateOrCreate(
            ['section' => $section, 'uuid' => $user->uuid],
            $data
        );

        return redirect()->route('lunch.teacher')->with('success', '已儲存您的午餐設定！');
    }

    public function setting($section)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能設定午餐調查期程！');
        }
        $settings = LunchSurvey::settings();
        if (!$settings)
            $settings = LunchSurvey::latest_settings();
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
        ], [
            'section',
        ]);
        $settings = LunchSurvey::settings($section);
        Watchdog::watch($request, '設定午餐調查期程：' . json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('lunch')->with('success', '午餐調查期程設定完成！');
    }

    public function storeSurvey(Request $request)
    {
        $user = Auth::user();
        $student = Student::find($user->uuid);
        $survey = LunchSurvey::updateOrCreate([
            'section' => $request->input('section'),
            'uuid' => $student->uuid,
        ], [
            'class_id' => $student->class_id,
            'seat' => $student->seat,
            'by_school' => ($request->input('meal') == 'by_school') ? 1 : 0,
            'vegen' => ($request->input('type') == 'vegen') ? 1 : 0,
            'milk' => ($request->input('milk') == 'yes') ? 1 : 0,
            'by_parent' => ($request->input('meal') == 'by_parent') ? 1 : 0,
            'boxed_meal' => ($request->input('meal') == 'boxed_meal') ? 1 : 0,
        ]);
        Watchdog::watch($request, '提交午餐調查表：' . $survey->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('lunch.survey')->with('success', '已為您儲存午餐調查表！'); // Redirect to survey view
    }

    public function downloadAll($section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        if (!$section)
            $section = next_section();
        if (LunchSurvey::count($section) == 0) {
            return redirect()->route('lunch', ['section' => $section])->with('error', '沒有調查結果可以匯出！');
        } else {
            $filename = substr($section, 0, -1) . '學年度' . ((substr($section, -1) == '1') ? '上' : '下') . '學期午餐調查結果彙整';
            return (new LunchExport($section))->download("$filename.xlsx");
        }
    }

    public function downloadGrade($section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        if (!$section)
            $section = next_section();

        $filename = substr($section, 0, -1) . '學年度' . ((substr($section, -1) == '1') ? '上' : '下') . '學期年級用餐確認表';
        return (new LunchGradeExport($section))->download("$filename.xlsx");
    }

    public function downloadLocation($section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        if (!$section)
            $section = next_section();

        $filename = substr($section, 0, -1) . '學年度' . ((substr($section, -1) == '1') ? '上' : '下') . '學期各地點用餐名錄';
        return (new LunchLocationExport($section))->download("$filename.xlsx");
    }

    public function downloadPayment($section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        if (!$section)
            $section = next_section();

        $filename = substr($section, 0, -1) . '學年度' . ((substr($section, -1) == '1') ? '上' : '下') . '學期收費明細對帳單';
        return (new LunchPaymentExport($section))->download("$filename.xlsx");
    }

    public function download($section, $class_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        if (!$section)
            $section = next_section();
        if (LunchSurvey::countByClass($section, $class_id) == 0) {
            return redirect()->route('lunch', ['section' => $section])->with('error', '沒有調查結果可以匯出！');
        } else {
            $filename = substr($section, 0, -1) . '學年度' . ((substr($section, -1) == '1') ? '上' : '下') . '學期午餐調查結果彙整';
            return (new LunchClassExport($section, $class_id))->download("$filename.xlsx");
        }
    }

    public function cafeterias()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能管理供餐地點！');
        }
        $cafeterias = LunchCafeteria::all();
        return view('app.lunch_cafeterias', ['cafeterias' => $cafeterias]);
    }

    public function storeCafeteria(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能管理供餐地點！');
        }
        $validated = $request->validate([
            'description' => 'required|string|max:255',
        ]);
        LunchCafeteria::create($validated);
        return redirect()->route('lunch.cafeterias')->with('success', '已新增供餐地點！');
    }

    public function updateCafeteria(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能管理供餐地點！');
        }
        $cafeteria = LunchCafeteria::find($id);
        if ($cafeteria) {
            $validated = $request->validate([
                'description' => 'required|string|max:255',
            ]);
            $cafeteria->update($validated);
            return redirect()->route('lunch.cafeterias')->with('success', '已更新供餐地點！');
        }
        return redirect()->route('lunch.cafeterias')->with('error', '找不到該供餐地點！');
    }

    public function deleteCafeteria($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('lunch.manager');
        if (!$manager) {
            return redirect()->route('home')->with('error', '只有管理員才能管理供餐地點！');
        }
        $cafeteria = LunchCafeteria::find($id);
        if ($cafeteria) {
            $cafeteria->delete();
            return redirect()->route('lunch.cafeterias')->with('success', '已刪除供餐地點！');
        }
        return redirect()->route('lunch.cafeterias')->with('error', '找不到該供餐地點！');
    }

}
