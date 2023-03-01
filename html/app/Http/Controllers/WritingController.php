<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\WritingGenre;
use App\Models\WritingContext;
use App\Models\Watchdog;

class WritingController extends Controller
{

    public function index(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('writing.manager');
        $genres = WritingGenre::all();
        if ($request->input('genre')) {
            $genre = WritingGenre::find($request->input('genre'));
        } elseif (session('genre')) {
            $genre = WritingGenre::find(session('genre'));
        } else {
            $genre = WritingGenre::latest('id')->first();
        }
        if ($request->input('order')) {
            $order = $request->input('order');
        } else {
            $order = session('order', 'updated_at');
        }
        if ($genre) {
            $contexts = $genre->contexts()->orderByDesc($order)->paginate(16);
        } else {
            $contexts = collect();
        }
        session(['genre' => $genre->id]);
        session(['order' => $order]);
        return view('app.writing', ['manager' => $manager, 'genres' => $genres, 'genre' => $genre, 'order' => $order, 'contexts' => $contexts]);
    }

    public function genres()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('writing.manager');
        if ($user->is_admin || $manager) {
            $genres = WritingGenre::all();
            return view('app.writing_genres', ['genres' => $genres]);    
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function addGenre()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('writing.manager');
        if ($user->is_admin || $manager) {
            return view('app.writing_addgenre');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insertGenre(Request $request)
    {
        $k = WritingGenre::create([
            'name' => $request->input('title'),
            'description' => $request->input('desc'),
        ]);
        Watchdog::watch($request, '新增投稿專欄：' . $k->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('writing.genres')->with('success', '投稿專欄新增完成！');
    }

    public function editGenre($genre)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('writing.manager');
        if ($user->is_admin || $manager) {
            $genre = WritingGenre::find($genre);
            return view('app.writing_editgenre', ['genre' => $genre]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function updateGenre(Request $request, $genre)
    {
        $k = WritingGenre::find($genre);
        $k->update([
            'name' => $request->input('title'),
            'description' => $request->input('desc'),
        ]);
        Watchdog::watch($request, '修改投稿專欄：' . $k->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('writing.genres')->with('success', '投稿專欄修改完成！');
    }

    public function removeGenre(Request $request, $genre)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('writing.manager');
        if ($user->is_admin || $manager) {
            $contexts = WritingContext::where('genre_id', $genre)->get();
            if ($contexts) {
                return redirect()->route('writing.genres')->with('message', '該專欄已經有投稿作品，因此無法刪除！');
            } else {
                $genre = WritingGenre::find($genre);
                Watchdog::watch($request, '刪除投稿專欄：' . $genre->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $genre->delete();
                return redirect()->route('writing.genres')->with('success', '投稿專欄已經刪除！');
            }
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function add(Request $request, $genre)
    {
        $referer = $request->headers->get('referer');
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('writing.manager');
        if (!$manager && $user->user_type != 'Student') return redirect()->route('home')->with('error', '您不是學生，因此無法投稿！');
        $genre = WritingGenre::find($genre);
        return view('app.writing_add', ['referer' => $referer, 'genre' => $genre]);
    }

    public function insert(Request $request, $genre)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('writing.manager');
        if (!$manager && $user->user_type != 'Student') return redirect()->route('home')->with('error', '您不是學生，因此無法投稿！');
        $stu = Student::find($user->uuid);
        $genre = WritingGenre::find($genre);
        $referer = $request->input('referer');
        $lines = explode('<br>', $request->input('words'));
        $first = array_shift($lines);
        if ($first) {
            $title = mb_ereg_replace('　', '', $first);
        } else {
            $title = '無題';            
        }
        $words = implode('<br>', $lines);
        $context = WritingContext::create([
            'genre_id' => $genre->id,
            'uuid' => $stu->uuid,
            'title' => $title,
            'words' => $words,
            'author' => $stu->realname,
            'classname' => $stu->classroom->name,
            'hits' => 0,
        ]);
        Watchdog::watch($request, '投稿：' . $context->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect($referer)->with('success', '文章已經完成投稿！');
    }

    public function edit(Request $request, $id)
    {
        $referer = $request->headers->get('referer');
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('writing.manager');
        if (!$manager && $user->user_type != 'Student') return redirect()->route('home')->with('error', '您不是學生，因此無法投稿！');
        $context = WritingContext::find($id);
        return view('app.writing_edit', ['referer' => $referer, 'context' => $context]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('writing.manager');
        if (!$manager && $user->user_type != 'Student') return redirect()->route('home')->with('error', '您不是學生，因此無法投稿！');
        $referer = $request->input('referer');
        $lines = explode('<br>', $request->input('words'));
        $first = array_shift($lines);
        if ($first) {
            $title = mb_ereg_replace('　', '', $first);
        } else {
            $title = '無題';
        }
        $words = implode('<br>', $lines);
        $context = WritingContext::find($id);
        $context->update([
            'title' => $title,
            'words' => $words,
        ]);
        Watchdog::watch($request, '投稿：' . $context->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect($referer)->with('success', '文章已經修改完成！');
    }

    public function remove(Request $request, $id)
    {
        $referer = $request->headers->get('referer');
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('writing.manager');
        if (!$manager && $user->user_type != 'Student') return redirect()->route('home')->with('error', '您不是學生，因此無法投稿！');
        $context = WritingContext::find($id);
        Watchdog::watch($request, '刪除投稿文章：' . $context->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $context->delete();
        return redirect($referer)->with('success', '文章已經刪除！');
    }

    public function show(Request $request, $id)
    {
        $referer = $request->headers->get('referer');
        $context = WritingContext::find($id);
        $context->hits += 1;
        $context->save();
        return view('app.writing_view', ['referer' => $referer, 'context' => $context]);
    }

}
