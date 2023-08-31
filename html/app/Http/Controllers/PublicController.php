<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\PublicClass;
use App\Models\IcsCalendar;
use App\Models\User;
use App\Models\Unit;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\Domain;
use App\Models\Classroom;
use App\Models\Watchdog;
use App\Models\Permission;
use App\Exports\PublicExport;
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
        $date = null;
        if (!$section || $section == current_section()) {
            $date = Carbon::today();
            $section = current_section();
        }
        if ($request->has('date')) {
            $date = Carbon::createFromFormat('Y-m-d', $request->input('date'));
        }
        if (!$date) {
            $between = section_between_date($section);
            $date = Carbon::createFromFormat('Y-m-d', $between->mindate);
        }
        $manager = ($user->is_admin || $user->hasPermission('public.manager'));
        $domainmanager = $user->hasPermission('public.domain');
        if ($manager) {
            $publics = PublicClass::bySection($section);
        } elseif ($domainmanager) {
            $domain = Teacher::find($user->uuid)->domains->first();
            $publics = PublicClass::byDomain($domain->id, $section);
        } else {
            $publics = PublicClass::byUser($user->uuid, $section);
        }
        $calendar = IcsCalendar::forPublic();
        $sections = PublicClass::sections();
        $temp = current_section();
        if (!$sections->contains('section', $temp)) {
            $seme = substr($temp, -1);
            if ($seme == 1) {
                $strseme = '上學期';
            } else {
                $strseme = '下學期';
            }
            $sections->push((object)[ 'section' => $temp, 'name' => '第'.substr($temp, 0, -1).'學年'.$strseme ]);
        }
        $temp = next_section();
        if (!$sections->contains('section', $temp)) {
            $seme = substr($temp, -1);
            if ($seme == 1) {
                $strseme = '上學期';
            } else {
                $strseme = '下學期';
            }
            $sections->push((object)[ 'section' => $temp, 'name' => '第'.substr($temp, 0, -1).'學年'.$strseme ]);
        }
        $schedule = PublicClass::weekly($date);
        return view('app.public', ['manager' => $manager, 'domain_manager' => $domainmanager, 'calendar' => $calendar, 'section' => $section, 'sections' => $sections, 'mydate' => $date, 'publics' => $publics, 'sessions' => self::$sessionMap, 'schedule' => $schedule]);
    }

    public function add(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能新增公開課！');
        }
        $section = $request->input('section');
        $date = $request->input('date');
        $weekday = $request->input('weekday');
        $session = $request->input('session');
        $manager = ($user->is_admin || $user->hasPermission('public.manager'));
        $domain_manager = $user->hasPermission('public.domain');
        if ($manager || $domain_manager) {
            $domains = Domain::all();
            $classes = Classroom::all();
            $teachers = Teacher::leftJoin('belongs', 'belongs.uuid', '=', 'teachers.uuid')
                ->leftJoin('domains', 'domains.id', '=', 'belongs.domain_id')
                ->where('belongs.year', current_year())
                ->orderBy('belongs.domain_id')
                ->get();
            $teacher = $user->profile;
            $teacher_list = $teachers;
            $domain = null;
            if ($domain_manager && $teacher->domains->isNotEmpty()) {
                $domain = $teacher->domains->first();
                $teacher_list = $domain->teachers;
            }
            return view('app.public_add', ['section' => $section, 'domain' => $domain, 'domains' => $domains, 'teacher' => $teacher, 'teacher_list' => $teacher_list, 'teachers' => $teachers, 'classes' => $classes, 'mydate' => $date, 'weekday' => $weekday, 'session' => $session, 'sessions' => self::$sessionMap]);
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
            $class_id = $request->input('classroom');
            $myclass = Classroom::find($class_id);
            $public = PublicClass::create([
                'section' => $request->input('section'),
                'domain_id' => $request->input('domain'),
                'teach_unit' => $request->input('unit'),
                'teach_grade'  => $myclass->grade_id,
                'teach_class' => $class_id,
                'reserved_at' => $request->input('date'),
                'weekday' => $request->input('weekday'),
                'session' => $request->input('session'),
                'location'  => $request->input('location'),
                'uuid' => $request->input('uuid'),
                'partners' => $request->input('teachers'),
            ]);
            if ($request->hasFile('eduplan')) {
                $extension = $request->file('eduplan')->getClientOriginalExtension();
                $fileName = $public->id . '_eduplan' . '.' . $extension;
                $request->file('eduplan')->move(public_path('public_class'), $fileName);
                $url = asset('public_class/' . $fileName);
                Watchdog::watch($request, '上傳教案：' . $url);
                $public->eduplan = $fileName;
            }
            if ($request->hasFile('discuss')) {
                $extension = $request->file('discuss')->getClientOriginalExtension();
                $fileName = $public->id . '_discuss' . '.' . $extension;
                $request->file('discuss')->move(public_path('public_class'), $fileName);
                $url = asset('public_class/' . $fileName);
                Watchdog::watch($request, '上傳觀課後會談：' . $url);
                $public->discuss = $fileName;
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
            $domains = Domain::all();
            $classes = Classroom::all();
            $teachers = Teacher::leftJoin('belongs', 'belongs.uuid', '=', 'teachers.uuid')
                ->leftJoin('domains', 'domains.id', '=', 'belongs.domain_id')
                ->where('belongs.year', current_year())
                ->orderBy('belongs.domain_id')
                ->get();
            return view('app.public_edit', ['public' => $public, 'domains' => $domains, 'teachers' => $teachers, 'classes' => $classes, 'sessions' => self::$sessionMap]);

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
            $class_id = $request->input('classroom');
            $myclass = Classroom::find($class_id);
            $public->update([
                'teach_unit' => $request->input('unit'),
                'teach_grade'  => $myclass->grade_id,
                'teach_class' => $class_id,
                'reserved_at' => $request->input('date'),
                'weekday' => $request->input('weekday'),
                'session' => $request->input('session'),
                'location'  => $request->input('location'),
                'partners' => $request->input('teachers'),
            ]);
            if ($request->input('del_eduplan') == 'yes') {
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
                $public->eduplan = $fileName;
            }
            if ($request->input('del_discuss') == 'yes') {
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
                $public->discuss = $fileName;
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
        $perm = Permission::findByName('public.domain');
        $units = Unit::main();
        $already = $perm->teachers()->orderBy('uuid')->get();
        $teachers = Teacher::leftJoin('belongs', 'belongs.uuid', '=', 'teachers.uuid')
            ->leftJoin('domains', 'domains.id', '=', 'belongs.domain_id')
            ->where('belongs.year', current_year())
            ->orderBy('belongs.domain_id')
            ->get();
        return view('app.public_grant', ['permission' => $perm, 'already' => $already, 'units' => $units, 'teachers' => $teachers]);
    }

    public function updatePerm(Request $request) {
        $users = $request->input('teachers');
        $perm = Permission::findByName('public.domain')->removeAll();
        if (!empty($users)) {
            $perm->assign($users);
            foreach ($users as $u) {
                $user_list[] = Teacher::find($u)->realname;
            }
            $log = '授予權限' . $perm->description . '給' . implode('、', $user_list);
            Watchdog::watch($request, $log);
        } else {
            $log = '已經移除所有授權！';
            Watchdog::watch($request, '移除' . $perm->description . '所有已授權人員！');
        }
        return back()->with('success', $log);
    }

    public function export($section) {
    }

    public function download($section, $domain_id) {
        $user = User::find(Auth::user()->id);
        $manager = ($user->is_admin || $user->hasPermission('public.manager'));
        if ($manager) {
            $domain = Domain::find($domain_id);
            $filename = $domain->name . '公開課成果報告.pdf';
            $exporter = new PublicExport($section, $domain_id);
            return $exporter->download($filename);
        } else {
            return redirect()->route('home')->with('error', '只有管理員才能下載公開課成果報告！');
        }
    }

}
