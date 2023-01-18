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
use App\Models\Watchdog;

class OrganizeController extends Controller
{

    public function index($year = null)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $teacher = $user->profile;
        $survey = $teacher->survey($year);
        $reserve = DB::table('organize_reserved')->where('syear', current_year())->where('uuid', $user->uuid)->first();
        $reserved = ($reserve) ? true : false;
        $current = current_year();
        if (!$year) $year = $current;
        $years = OrganizeSettings::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $flow = OrganizeSettings::where('syear', $year)->first();
        $stage1 = OrganizeVacancy::year_stage(1);
        $stage2 = OrganizeVacancy::year_stage(2);
        return view('app.organize', ['current' => $current, 'year' => $year, 'years' => $years, 'flow' => $flow, 'reserved' => $reserved, 'teacher' => $teacher, 'survey' => $survey, 'stage1' => $stage1, 'stage2' => $stage2]);
    }

    public function survey(Request $request, $uuid)
    {
        $teacher = Teacher::find($uuid);
        $flow = OrganizeSettings::current();
        if ($flow->onSurvey()) {
            $age = ($teacher->birthdate->format('md') > date("md")) ? date("Y") - $teacher->birthdate->format('Y') - 1 : date("Y") - $teacher->birthdate->format('Y');
            $survey = OrganizeSurvey::updateOrCreate([
                'syear' => current_year(),
                'uuid' => $teacher->uuid,
            ],[
                'age' => $age,
                'exprience' => $request->input('exp'),
                'edu_level' => $request->input('edu_level'),
                'edu_school' => $request->input('edu_school'),
                'edu_division' => $request->input('edu_division'),
                'score' => $request->input('total'),
            ]);
        }
        if ($flow->onFirstStage()) {
            $specials = null;
            if ($request->has('specials')) $specials = array_map('intval', $request->input('specials'));
            $survey = OrganizeSurvey::where('syear', current_year())
            ->where('uuid', $teacher->uuid)
            ->update([
                'admin1' => $request->input('admin1'),
                'admin2' => $request->input('admin2'),
                'admin3' => $request->input('admin3'),
                'special' => $specials,
            ]);
        }
        if ($flow->onSecondStage()) {
            if ($request->has('specials')) {
                $specials = array_map('intval', $request->input('specials'));
                $survey = OrganizeSurvey::where('syear', current_year())
                ->where('uuid', $teacher->uuid)
                ->update([
                    'special' => $specials,
                    'teach1' => $request->input('teach1'),
                    'teach2' => $request->input('teach2'),
                    'teach3' => $request->input('teach3'),
                    'teach4' => $request->input('teach4'),
                    'teach5' => $request->input('teach5'),
                    'teach6' => $request->input('teach6'),
                    'grade' => $request->input('grade'),
                    'overcome' => $request->input('overcome'),
                ]);
            } else {
                $survey = OrganizeSurvey::where('syear', current_year())
                ->where('uuid', $teacher->uuid)
                ->update([
                    'teach1' => $request->input('teach1'),
                    'teach2' => $request->input('teach2'),
                    'teach3' => $request->input('teach3'),
                    'teach4' => $request->input('teach4'),
                    'teach5' => $request->input('teach5'),
                    'teach6' => $request->input('teach6'),
                    'grade' => $request->input('grade'),
                    'overcome' => $request->input('overcome'),
                ]);
            }
        }
        Watchdog::watch($request, '填寫職務編排意願調查：' . $survey->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('organize')->with('success', '已為您儲存職務意願表，截止日前您仍然可以修改！');
    }

    public function setting()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('organize.manager');
        if ($user->is_admin || $manager) {
            $seme = current_between_date();
            $settings = OrganizeSettings::current();
            return view('app.organize_setting', ['seme' => $seme, 'settings' => $settings]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function saveSettings(Request $request)
    {
        $survey_at = $request->input('survey_at');
        $first_stage = $request->input('first_stage');
        $pause_at = $request->input('pause_at');
        $second_stage = $request->input('second_stage');
        $close_at = $request->input('close_at');
        $setting = OrganizeSettings::updateOrCreate([
            'syear' => current_year(),
        ],[
            'survey_at' => $survey_at,
            'first_stage' => $first_stage,
            'pause_at' => $pause_at,
            'second_stage' => $second_stage,
            'close_at' => $close_at,
        ]);
        Watchdog::watch($request, '設定職務編排時程：' . $setting->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('organize.setting')->with('success', '職務編排流程設定完成！');
    }

    public function vacancy()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('organize.manager');
        if ($user->is_admin || $manager) {
            if (OrganizeVacancy::year()->isEmpty()) {
                $this->vacancy_init();
            }
            $admins = OrganizeVacancy::year_type('admin');
            $tutors = OrganizeVacancy::year_type('tutor');
            $domains = OrganizeVacancy::year_type('domain');
            return view('app.organize_vacancy', ['admins' => $admins, 'tutors' => $tutors, 'domains' => $domains]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function reset(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('organize.manager');
        if ($user->is_admin || $manager) {
            DB::table('organize_original')->where('syear', current_year())->delete();
            DB::table('organize_reserved')->where('syear', current_year())->delete();
            DB::table('organize_swap')->where('syear', current_year())->delete();
            DB::table('organize_assign')->where('syear', current_year())->delete();
            OrganizeVacancy::where('syear', current_year())->delete();
            $this->vacancy_init();
            Watchdog::watch($request, '重新計算職缺');
            return redirect()->route('organize.vacancy');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
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
        $admins = Role::manager();
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
        $grades = Grade::whereIn('id', [1, 3, 5])->get();
        foreach ($grades as $a) {
            $b = Grade::find($a->id + 1);
            $v1 = OrganizeVacancy::create([
                'type' => 'tutor',
                'grade_id' => $a->id,
                'name' => $a->name,
                'stage' => 2,
                'shortfall' => $a->classrooms->count(),
                'filled' => 0,
                'assigned' => 0,
            ]);
            $v2 = OrganizeVacancy::create([
                'type' => 'tutor',
                'grade_id' => $b->id,
                'name' => $b->name,
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
                    'vacancy_id' => $v2->id,
                ]);
                DB::table('organize_reserved')->insert([
                    'syear' => current_year(),
                    'uuid' => $t->uuid,
                    'vacancy_id' => $v2->id,
                ]);
            }
            foreach ($b->classrooms as $c) {
                $t = $c->tutors->first();
                DB::table('organize_original')->insert([
                    'syear' => current_year(),
                    'uuid' => $t->uuid,
                    'vacancy_id' => $v1->id,
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
        Watchdog::watch($request, '修改職缺：' . $vacancy->name . '，意願調查階段：' . $stage);
        return response()->json($vacancy);
    }

    public function special(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $special = $request->boolean('special');
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->special = $special;
        $vacancy->save();
        Watchdog::watch($request, '修改職缺：' . $vacancy->name . '，特殊任務：' . $special);
        return response()->json($vacancy);
    }

    public function shortfall(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $shortfall = (integer) $request->input('shortfall');
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->shortfall = $shortfall;
        $vacancy->save();
        Watchdog::watch($request, '修改職缺：' . $vacancy->name . '，員額數：' . $shortfall);
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
        $t = Teacher::find($uuid);
        Watchdog::watch($request, '將' . $t->realname . '的職務：' . $vacancy->name . '開缺');
        return response()->json('success');
    }

    public function releaseAll(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $records = DB::table('organize_reserved')
            ->where('syear', current_year())
            ->where('vacancy_id', $vacancy_id)
            ->count();
        if ($records > 0) {
            $vacancy = OrganizeVacancy::find($vacancy_id);
            $vacancy->filled -= $records;
            $vacancy->save();
            DB::table('organize_reserved')
                ->where('syear', current_year())
                ->where('vacancy_id', $vacancy_id)
                ->delete();    
        }
        Watchdog::watch($request, '將職務：' . $vacancy->name . '所有人員開缺');
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
        $t = Teacher::find($uuid);
        Watchdog::watch($request, '保留' . $t->realname . '的職缺：' . $vacancy->name);
        return response()->json('success');
    }

    public function arrange($search = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('organize.manager');
        if ($user->is_admin || $manager) {
            $flow = OrganizeSettings::current();
            if (($flow->onPause() || $flow->onFinish()) && !$search) $search = 'first';
            if (!$search) $search = 'seven';
            $seniority = Seniority::current()->isEmpty() ? false : true;
            $completeness = OrganizeVacancy::completeness();
            $stage1 = OrganizeVacancy::year_stage(1);
            $stage2 = OrganizeVacancy::year_stage(2);
            $rest_teachers = OrganizeSurvey::where('syear', current_year())->whereNull('assign')->get();
            $teachers = [];
            if ($flow->onPause() || $search == 'seven') {
                foreach ($stage1->general as $v) {
                    $query = OrganizeSurvey::where('syear', current_year());
                    switch ($search) {
                        case 'first':
                            $query->where(function ($query) use ($v) {
                                $query->where('assign', $v->id)->orWhereNull('assign');
                            })->Where('admin1', $v->id);
                            break;
                        case 'second':
                            $query->where(function ($query) use ($v) {
                                $query->where(function ($query) use ($v) {
                                    $query->where('assign', $v->id)->where('admin1', $v->id);
                                });
                                $query->orWhere(function ($query) use ($v) {
                                    $query->whereNull('assign')->where('admin2', $v->id);
                                });
                            });
                            break;
                        case 'third':
                            $query->where(function ($query) use ($v) {
                                $query->where(function ($query) use ($v) {
                                    $query->where('assign', $v->id)->where(function ($query) use ($v) {
                                        $query->where('admin1', $v->id)->orWhere('admin2', $v->id);
                                    });
                                });
                                $query->orWhere(function ($query) use ($v) {
                                    $query->whereNull('assign')->where('admin3', $v->id);
                                });
                            });
                            break;
                        default:
                            $query->where('assign', $v->id);
                    }
                    $teachers[$v->id] = $query->orderBy('score', 'desc')->orderBy('age', 'desc')->get();
                }
                foreach ($stage1->special as $v) {
                    $query = OrganizeSurvey::where('syear', current_year());
                    if ($search == 'seven') {
                        $query->where('assign', $v->id);
                    } else {
                        $query->where(function ($query) use ($v) {
                            $query->where('assign', $v->id)->orWhereNull('assign');
                        })->WhereJsonContains('special', $v->id);    
                    }
                    $teachers[$v->id] = $query->orderBy('score', 'desc')->orderBy('age', 'desc')->get();
                }
            }
            if ($flow->onFinish() || $search == 'seven') {
                foreach ($stage2->special as $v) {
                    $query = OrganizeSurvey::where('syear', current_year());
                    if ($search == 'seven') {
                        $query->where('assign', $v->id);
                    } else {
                        $query->where(function ($query) use ($v) {
                            $query->where('assign', $v->id)->orWhereNull('assign');
                        })->WhereJsonContains('special', $v->id);    
                    }
                    $teachers[$v->id] = $query->orderBy('score', 'desc')->ordderBy('age', 'desc')->get();
                }
                foreach ($stage2->general as $v) {
                    $query = OrganizeSurvey::where('syear', current_year());
                    switch ($search) {
                        case 'first':
                            $query->where(function ($query) use ($v) {
                                $query->where('assign', $v->id)->orWhereNull('assign');
                            })->Where('teach1', $v->id);
                            break;
                        case 'second':
                            $query->where(function ($query) use ($v) {
                                $query->where(function ($query) use ($v) {
                                    $query->where('assign', $v->id)->where('teach1', $v->id);
                                });
                                $query->orWhere(function ($query) use ($v) {
                                    $query->whereNull('assign')->where('teach2', $v->id);
                                });
                            });
                            break;
                        case 'third':
                            $query->where(function ($query) use ($v) {
                                $query->where(function ($query) use ($v) {
                                    $query->where('assign', $v->id)->where(function ($query) use ($v) {
                                        $query->where('teach1', $v->id)->orWhere('teach2', $v->id);
                                    });
                                });
                                $query->orWhere(function ($query) use ($v) {
                                    $query->whereNull('assign')->where('teach3', $v->id);
                                });
                            });
                            break;
                        case 'four':
                            $query->where(function ($query) use ($v) {
                                $query->where(function ($query) use ($v) {
                                    $query->where('assign', $v->id)->where(function ($query) use ($v) {
                                        $query->where('teach1', $v->id)->orWhere('teach2', $v->id)->orWhere('teach3', $v->id);
                                    });
                                });
                                $query->orWhere(function ($query) use ($v) {
                                    $query->whereNull('assign')->where('teach4', $v->id);
                                });
                            });
                            break;
                        case 'five':
                            $query->where(function ($query) use ($v) {
                                $query->where(function ($query) use ($v) {
                                    $query->where('assign', $v->id)->where(function ($query) use ($v) {
                                        $query->where('teach1', $v->id)->orWhere('teach2', $v->id)->orWhere('teach3', $v->id)->orWhere('teach4', $v->id);
                                    });
                                });
                                $query->orWhere(function ($query) use ($v) {
                                    $query->whereNull('assign')->where('teach5', $v->id);
                                });
                            });
                            break;
                        case 'six':
                            $query->where(function ($query) use ($v) {
                                $query->where(function ($query) use ($v) {
                                    $query->where('assign', $v->id)->where(function ($query) use ($v) {
                                        $query->where('teach1', $v->id)->orWhere('teach2', $v->id)->orWhere('teach3', $v->id)->orWhere('teach4', $v->id)->orWhere('teach5', $v->id);
                                    });
                                });
                                $query->orWhere(function ($query) use ($v) {
                                    $query->whereNull('assign')->where('teach6', $v->id);
                                });
                            });
                            break;
                        default:
                            $query->where('assign', $v->id);
                    }
                    $teachers[$v->id] = $query->orderBy('score', 'desc')->orderBy('age', 'desc')->get();
                }
            }
            return view('app.organize_arrangement', ['display' => $search, 'flow' => $flow, 'seniority' => $seniority, 'completeness' => $completeness, 'stage1' => $stage1, 'stage2' => $stage2, 'teachers' => $teachers, 'rest_teachers' => $rest_teachers]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function assign(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $uuid = $request->input('uuid');
        if (empty($uuid)) return response()->json('not found');
        $survey = OrganizeSurvey::findByUUID($uuid);
        $survey->assign = $vacancy_id;
        $survey->save();
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->assigned += 1;
        $vacancy->save();
        DB::table('organize_assign')->Insert([
            'syear' => current_year(),
            'vacancy_id' => $vacancy_id,
            'uuid' => $uuid,
        ]);
        $t = Teacher::find($uuid);
        Watchdog::watch($request, '安排' . $t->realname . '擔任職缺：' . $vacancy->name);
        return response()->json('success');
    }

    public function unassign(Request $request)
    {
        $vacancy_id = $request->input('vid');
        $uuid = $request->input('uuid');
        if (empty($uuid)) return response()->json('not found');
        $survey = OrganizeSurvey::findByUUID($uuid);
        $survey->assign = null;
        $survey->save();
        $vacancy = OrganizeVacancy::find($vacancy_id);
        $vacancy->assigned -= 1;
        $vacancy->save();
        DB::table('organize_assign')
            ->where('syear', current_year())
            ->where('vacancy_id', $vacancy_id)
            ->where('uuid', $uuid)
            ->delete();
        $t = Teacher::find($uuid);
        Watchdog::watch($request, '取消' . $t->realname . '擔任職缺：' . $vacancy->name . '的安排');
        return response()->json('success');
    }

    public function listVacancy($year = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $current = current_year();
        if (!$year) $year = $current;
        $years = OrganizeSettings::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $vacancys = OrganizeVacancy::year($year);
        return view('app.organize_listvacancy', ['current' => $current, 'year' => $year, 'years' => $years, 'vacancys' => $vacancys]);
    }

    public function listSurvey(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $uuid = $request->input('uuid');
        if (!$uuid) {
            $teacher = $user->profile;
        } else {
            $teacher = Teacher::find($uuid);
        }
        if ($request->has('year')) {
            $year = $request->input('year');
        } else {
            $year = current_year();
        }
        $survey = $teacher->survey($year);
        $stage1 = OrganizeVacancy::year_stage(1, $year);
        $stage2 = OrganizeVacancy::year_stage(2, $year);
        $header = $teacher->realname . $year .'學年度意願調查表';
        if (!$survey) {
            $body = '找不到意願調查表！';
        } else {
            $body = view('app.organize_listsurvey', ['year' => $year, 'teacher' => $teacher, 'survey' => $survey, 'stage1' => $stage1, 'stage2' => $stage2])->render();
        }
        return response()->json((object) [ 'header' => $header, 'body' => $body]);
    }

    public function listResult($year = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $current = current_year();
        if (!$year) $year = $current;
        $years = OrganizeSettings::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $vacancys = OrganizeVacancy::year($year);
        return view('app.organize_listresult', ['current' => $current, 'year' => $year, 'years' => $years, 'vacancys' => $vacancys]);
    }

}
