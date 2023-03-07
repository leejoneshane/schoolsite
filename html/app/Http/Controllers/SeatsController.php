<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Seats;
use App\Models\SeatsTheme;
use App\Models\User;
use App\Models\Student;
use App\Models\Watchdog;

class SeatsController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type != 'Teacher') {
            return redirect()->route('home')->with('error', '只有教職員才能管理分組座位表！');
        }
        $seats = Seats::findByUUID($user->uuid);
        return view('app.seats', ['seats' => $seats]);
    }

    public function theme()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type != 'Teacher') {
            return redirect()->route('home')->with('error', '只有教職員才能管理分組座位表！');
        }
        $manager = $user->is_admin || $user->hasPermission('club.manager');
        $templates = SeatsTheme::all();
        return view('app.seats_theme', ['manager' => $manager, 'templates' => $templates]);
    }

    public function addTheme()
    {
        $styles = SeatsTheme::$styles;
        return view('app.seats_addtheme', ['styles' => $styles]);
    }

    public function insertTheme(Request $request)
    {
        $matrix = json_decode($request->input('matrix'));
        $theme = SeatsTheme::create([
            'name' => $request->input('title'),
            'matrix' => $matrix,
            'uuid' => $request->user()->uuid,
        ]);
        Watchdog::watch($request, '新增座位表版型：' . $theme->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('seats.theme')->with('success', '座位表版型新增完成！');
    }

    public function showTheme(Request $request)
    {
        $template = SeatsTheme::find($request->input('id'));
        $styles = SeatsTheme::$styles;
        $view = view('app.seats_viewtheme', ['template' => $template, 'styles' => $styles])->render();
        return response()->json((object) ['html' => $view]);
    }

    public function editTheme($id)
    {
        $template = SeatsTheme::find($id);
        $styles = SeatsTheme::$styles;
        return view('app.seats_edittheme', ['template' => $template, 'styles' => $styles]);
    }

    public function updateTheme(Request $request, $id)
    {
        $matrix = json_decode($request->input('matrix'));
        $theme = SeatsTheme::find($id);
        $theme->update([
            'name' => $request->input('title'),
            'matrix' => $matrix,
        ]);
        Watchdog::watch($request, '修改座位表版型：' . $theme->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('seats.theme')->with('success', '座位表版型修改完成！');
    }

    public function removeTheme(Request $request, $id)
    {
        $theme = SeatsTheme::find($id);
        if ($theme->seats->count() > 0) {
            return redirect()->route('seats.theme')->with('error', '這個版型正在使用中，因此無法刪除！');
        }
        Watchdog::watch($request, '移除座位表版型：' . $theme->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $theme->delete();
        return redirect()->route('seats.theme')->with('success', '座位表版型新增完成！');
    }

    public function add()
    {
    }

    public function insert(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function auto($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function change($id)
    {
    }

    public function updateChange(Request $request, $id)
    {
    }

    public function remove(Request $request, $id)
    {
    }

}
