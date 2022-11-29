<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Unit;
use App\Models\Role;
use App\Models\Grade;
use App\Models\Domain;
use App\Models\Teacher;
use App\Models\OrganizeSettings;
use App\Models\OrganizeSurvey;
use App\Models\OrganizeVacancy;
use App\Models\Seniority;


class OrganizeController extends Controller
{

    public function index($year = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $teacher = Teacher::find($user->uuid);
        $reserve = DB::table('organize_reserved')->where('syear', current_year())->where('uuid', $user->uuid)->first();
        $reserved = ($reserve) ? true : false;
        $current = current_year();
        if (!$year) $year = $current;
        $years = OrganizeSettings::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $flow = OrganizeSettings::where('syear', $year)->first();
        $stage1 = OrganizeVacancy::current_stage(1);
        $stage2 = OrganizeVacancy::current_stage(1);
        return view('app.organize', ['current' => $current, 'year' => $year, 'years' => $years, 'flow' => $flow, 'reserved' => $reserved, 'teacher' => $teacher, 'stage1' => $stage1, 'stage2' => $stage2]);
    }

    public function list($year = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $teacher = Teacher::find($user->uuid);
        $reserve = DB::table('organize_reserved')->where('syear', current_year())->where('uuid', $user->uuid)->first();
        $reserved = ($reserve) ? true : false;
        $current = current_year();
        if (!$year) $year = $current;
        $years = OrganizeSettings::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $flow = OrganizeSettings::where('syear', $year)->first();
        $stage1 = OrganizeVacancy::current_stage(1);
        $stage2 = OrganizeVacancy::current_stage(2);
        return view('app.organize', ['current' => $current, 'year' => $year, 'years' => $years, 'flow' => $flow, 'reserved' => $reserved, 'teacher' => $teacher, 'stage1' => $stage1, 'stage2' => $stage2]);
    }

    public function vacancy()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            if (OrganizeVacancy::current()->isEmpty()) {
                $this->vacancy_init();
            }
            $admins = OrganizeVacancy::current_type('admin');
            $tutors = OrganizeVacancy::current_type('tutor');
            $domains = OrganizeVacancy::current_type('domain');
            return view('app.organize_vacancy', ['admins' => $admins, 'tutors' => $tutors, 'domains' => $domains]);
        } else {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function reset()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            DB::table('organize_original')->where('syear', current_year())->delete();
            DB::table('organize_reserved')->where('syear', current_year())->delete();
            DB::table('organize_swap')->where('syear', current_year())->delete();
            DB::table('organize_assign')->where('syear', current_year())->delete();
            OrganizeVacancy::where('syear', current_year())->delete();
            $this->vacancy_init();
            return redirect()->route('organize.vacancy');
        } else {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    private function vacancy_init() {
        $managers = Role::director();
        foreach ($managers as $a) {
            $teachers = Teacher::where('role_id', $a->id)->get();
            if ($teachers->count() > 0) {
                $v = OrganizeVacancy::create([
                    'type' => 'admin',
                    'role_id' => $a->id,
                    'name' => $a->name,
                    'stage' => 1,
                    'shortfall' => $teachers->count(),
                    'filled' => $teachers->count(),
                    'assigned' => 0,
                ]);
                foreach ($teachers as $t) {
                    DB::table('organize_original')->insert([
                        'syear' => current_year(),
                        'uuid' => $t->uuid,
                        'vacancy_id' => $v->id,
                    ]);
                    DB::table('organize_reserved')->insert([
                        'syear' => current_year(),
                        'uuid' => $t->uuid,
                        'vacancy_id' => $v->id,
                    ]);
                }
            }
        }
        $admins = Role::organize();
        foreach ($admins as $a) {
            $teachers = Teacher::where('role_id', $a->id)->get();
            if ($teachers->count() > 0) {
                $v = OrganizeVacancy::create([
                    'type' => 'admin',
                    'unit_id' => $a->unit->uplevel()->id,
                    'role_id' => $a->id,
                    'name' => $a->name,
                    'stage' => 1,
                    'shortfall' => $teachers->count(),
                    'filled' => $teachers->count(),
                    'assigned' => 0,
                ]);
                foreach ($teachers as $t) {
                    DB::table('organize_original')->insert([
                        'syear' => current_year(),
                        'uuid' => $t->uuid,
                        'vacancy_id' => $v->id,
                    ]);
                    DB::table('organize_reserved')->insert([
                        'syear' => current_year(),
                        'uuid' => $t->uuid,
                        'vacancy_id' => $v->id,
                    ]);
                }
            }
        }
        $grades = Grade::all();
        foreach ($grades as $a) {
            $v = OrganizeVacancy::create([
                'type' => 'tutor',
                'grade_id' => $a->id,
                'name' => $a->name,
                'stage' => 2,
                'shortfall' => $a->classrooms->count(),
                'filled' => $a->classrooms->count(),
                'assigned' => 0,
            ]);
            foreach ($a->classrooms as $c) {
                $t = $c->tutors->first();
                DB::table('organize_original')->insert([
                    'syear' => current_year(),
                    'uuid' => $t->uuid,
                    'vacancy_id' => $v->id,
                ]);
                DB::table('organize_reserved')->insert([
                    'syear' => current_year(),
                    'uuid' => $t->uuid,
                    'vacancy_id' => $v->id,
                ]);
            }
        }
        $domains = Domain::all();
        foreach ($domains as $a) {
            if ($a->teachers->count()) {
                $v = OrganizeVacancy::create([
                    'type' => 'domain',
                    'domain_id' => $a->id,
                    'name' => $a->name,
                    'stage' => 2,
                    'shortfall' => $a->teachers->count(),
                    'filled' => $a->teachers->count(),
                    'assigned' => 0,
                ]);
                foreach ($a->teachers as $t) {
                    DB::table('organize_original')->insert([
                        'syear' => current_year(),
                        'uuid' => $t->uuid,
                        'vacancy_id' => $v->id,
                    ]);
                    DB::table('organize_reserved')->insert([
                        'syear' => current_year(),
                        'uuid' => $t->uuid,
                        'vacancy_id' => $v->id,
                    ]);
                }
            }
        }
    }

    public function stage(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $stage = $request->input('stage');
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->stage = $stage;
        $vacancy->save();
        return response()->json($vacancy);
    }

    public function special(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $special = ($request->input('special') == 'yes') ? true : false;
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->special = $special;
        $vacancy->save();
        return response()->json($vacancy);
    }

    public function shortfall(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $shortfall = (integer) $request->input('shortfall');
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->shortfall = $shortfall;
        $vacancy->save();
        return response()->json($vacancy);
    }

    public function release(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $uuid = $request->input('uuid');
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->filled -= 1;
        $vacancy->save();
        DB::table('organize_reserved')
            ->where('syear', current_year())
            ->where('vacancy_id', $vacancy_id)
            ->where('uuid', $uuid)
            ->delete();
        return response()->json('success');
    }

    public function reserve(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $uuid = $request->input('uuid');
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->filled += 1;
        $vacancy->save();
        DB::table('organize_reserved')->Insert([
            'syear' => current_year(),
            'vacancy_id' => $vacancy_id,
            'uuid' => $uuid,
        ]);
        return response()->json('success');
    }

}
