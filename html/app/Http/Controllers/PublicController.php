<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\PublicClass;
use App\Models\IcsCalendar;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Domain;
use App\Models\Classroom;
use App\Models\Watchdog;
use Carbon\Carbon;

class PublicController extends Controller
{
    protected static $sessionMap = [
        1 => '第一節',
        2 => '第二節',
        3 => '第三節',
        4 => '第四節',
        5 => '午休',
        6 => '第五節',
        7 => '第六節',
        8 => '第七節',
    ];

    public function index(Request $request, $section = null)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能瀏覽公開課！');
        }
        if (!$section) {
            $date = Carbon::today();
            $section = current_section();
        }
        if ($request->has('date')) {
            $date = Carbon::createFromFormat('Y-m-d', $request->input('date'));
        } elseif (!isset($date)) {
            $between = section_between_date($section);
            $date = Carbon::createFromFormat('Y-m-d', $between->mindate);
        }
        $manager = ($user->is_admin || $user->hasPermission('public.manager'));
        $domainmanager = $user->hasPermission('public.domain');
        if ($manager) {
            $publics = PublicClass::section($section);
        } elseif ($domainmanager) {
            $domain = Teacher::find('uuid')->domains->first();
            $publics = PublicClass::byDomain($domain->id, $section);
        } else {
            $publics = PublicClass::byUser($user->uuid, $section);
        }
        $calendar = IcsCalendar::forPublic();
        $sections = PublicClass::sections();
        $schedule = PublicClass::weekly();
        return view('app.public', ['manager' => $manager, 'domain_manager' => $domainmanager, 'calendar' => $calendar, 'section' => $section, 'sections' => $sections, 'mydate' => $date, 'publics' => $publics, 'sessions' => self::$sessionMap, 'schedule' => $schedule]);
    }

    public function add(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能新增公開課！');
        }
        $date = $request->input('date');
        $weekday = $request->input('weekday');
        $session = $request->input('session');
        $manager = ($user->is_admin || $user->hasPermission('public.manager') || $user->hasPermission('public.domain'));
        if ($manager) {
            $selections = [ 'all' => '全部'];
            $domains = Domain::all();
            foreach ($domains as $dom) {
                $selections[] = [ 'd'.$dom->id => $dom->name ];
            }
            $classes = Classroom::all();
            foreach ($classes as $cls) {
                $selections[] = [ 'c'.$cls->id => $cls->name ];
            }
            $current = 'all';
            $teachers = Teacher::all();
            $teacher = $user->profile;
            if ($teacher->domains->isNotEmpty()) {
                $domain = $teacher->domains->first();
                $teachers = $domain->teachers();
                $current = 'd'.$domain->id;
            } else {
                if ($teacher->tutor_classroom->isNotEmpty()) {
                    $tclass = $teacher->tutor_classroom;
                    $teachers = $tclass->teachers();
                    $current = 'c'.$tclass->id;
                }
            }
            return view('app.public_add', ['current' => $current, 'selections' => $selections, 'teachers' => $teachers, 'mydate' => $date, 'weekday' => $weekday, 'session' => $session, 'sessions' => self::$sessionMap]);
        } else {
            return redirect()->route('public')->with('error', '只有管理員或已授權的群召才能新增公開課！');
        }
    }

    public function insert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能新增公開課！');
        }
        $manager = ($user->is_admin || $user->hasPermission('public.manager') || $user->hasPermission('public.domain'));
        if ($manager) {
            $class_id = $request->input('class');
            $myclass = Classroom::find($class_id);
            $public = PublicClass::create([
                'domain_id' => $request->input('domain'),
                'teach_unit' => $request->input('unit'),
                'teach_grade'  => $myclass->grade_id,
                'teach_class' => $class_id,
                'reserved_at' => $request->input('date'),
                'weekday' => $request->input('weekday'),
                'session' => $request->input('session'),
                'location'  => $request->input('location'),
                'uuid' => $request->input('teacher'),
                'partners' => $request->input('partners'),
            ]);
            if ($request->hasFile('eduplan')) {
                $extension = $request->file('eduplan')->getClientOriginalExtension();
                $fileName = $public->id . '_eduplan' . '.' . $extension;
                $request->file('eduplan')->move(public_path('public_class'), $fileName);
                $url = asset('public_class/' . $fileName);
                Watchdog::watch($request, '上傳教案：' . $url);
                $public->eduplan = $url;
            }
            if ($request->hasFile('discuss')) {
                $extension = $request->file('discuss')->getClientOriginalExtension();
                $fileName = $public->id . '_discuss' . '.' . $extension;
                $request->file('discuss')->move(public_path('public_class'), $fileName);
                $url = asset('public_class/' . $fileName);
                Watchdog::watch($request, '上傳觀課後會談：' . $url);
                $public->discuss = $url;
            }
            $public->save();
            Watchdog::watch($request, '新增公開課資訊：' . $public->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('public')->with('success', '公開課新增完成！');
        } else {
            return redirect()->route('public')->with('error', '只有管理員或已授權的群召才能新增公開課！');
        }
    }

    public function edit($id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能修改公開課資訊！');
        }
        $public = PublicClass::find($id);
        if (!$public) return redirect()->route('public')->with('error', '找不到此公開課，因此無法編輯！');
        $manager = ($user->is_admin || $user->hasPermission('public.manager') || $user->hasPermission('public.domain') || $public->uuid == $user->uuid);
        if ($manager) {
            return view('app.public_edit', ['public' => $public]);
        } else {
            return redirect()->route('public')->with('error', '只有管理員才能修改公開課資訊！');
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能修改場地或設備！');
        }
        $public = PublicClass::find($id);
        if (!$public) return redirect()->route('public')->with('error', '找不到此公開課，因此無法編輯！');
        $manager = ($user->is_admin || $user->hasPermission('public.manager') || $user->hasPermission('public.domain') || $public->uuid == $user->uuid);
        if ($manager) {
            $public = PublicClass::find($id);
            $class_id = $request->input('class');
            $myclass = Classroom::find($class_id);
            $public->update([
                'teach_unit' => $request->input('unit'),
                'teach_grade'  => $myclass->grade_id,
                'teach_class' => $class_id,
                'reserved_at' => $request->input('date'),
                'weekday' => $request->input('weekday'),
                'session' => $request->input('session'),
                'location'  => $request->input('location'),
            ]);
            if ($request->has('del_eduplan')) {
                $path = public_path('public_class/' . $public->eduplan);
                if (file_exists($path)) {
                    unlink($path);
                }
                $public->eduplan = null;
            } elseif ($request->hasFile('eduplan')) {
                if ($public->eduplan) {
                    $path = public_path('public_class/' . $public->eduplan);
                    if (file_exists($path)) {
                        unlink($path);
                    }                        
                }
                $extension = $request->file('eduplan')->getClientOriginalExtension();
                $fileName = $public->id . '_eduplan' . '.' . $extension;
                $request->file('eduplan')->move(public_path('public_class'), $fileName);
                $url = asset('public_class/' . $fileName);
                Watchdog::watch($request, '上傳教案：' . $url);
                $public->eduplan = $url;
            }
            if ($request->has('del_discuss')) {
                $path = public_path('public_class/' . $public->discuss);
                if (file_exists($path)) {
                    unlink($path);
                }
                $public->discuss = null;            
            } elseif ($request->hasFile('discuss')) {
                if ($public->discuss) {
                    $path = public_path('public_class/' . $public->discuss);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $extension = $request->file('discuss')->getClientOriginalExtension();
                $fileName = $public->id . '_discuss' . '.' . $extension;
                $request->file('discuss')->move(public_path('public_class'), $fileName);
                $url = asset('public_class/' . $fileName);
                Watchdog::watch($request, '上傳觀課後會談：' . $url);
                $public->discuss = $url;
            }
            $public->save();
            Watchdog::watch($request, '更新公開課資訊：' . $public->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('public')->with('success', '公開課更新完成！');
        } else {
            return redirect()->route('public')->with('error', '只有管理員才能修改公開課資訊！');
        }
    }

    public function remove(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能刪除公開課資訊！');
        }
        $manager = ($user->is_admin || $user->hasPermission('public.manager') || $user->hasPermission('public.domain'));
        if ($manager) {
            $public = PublicClass::find($id);
            Watchdog::watch($request, '移除公開課資訊：' . $public->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $public->delete();
            return redirect()->route('public')->with('success', '公開課資訊已經移除！');
        } else {
            return redirect()->route('public')->with('error', '只有管理員才能刪除公開課資訊！');
        }
    }

    public function show(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能瀏覽公開課資訊！');
        }
        $reserve = PublicClass::find($request->input('id'));
        $header = '公開課資訊';
        if (!$reserve) {
            $body = '找不到公開課記錄！';
        } else {
            $body = view('app.public_log', ['public' => $reserve])->render();
        }
        return response()->json((object) [ 'header' => $header, 'body' => $body]);
    }

    public function perm() {

    }

    public function updatePerm(Request $request) {

    }

    public function export($section) {

    }

}
