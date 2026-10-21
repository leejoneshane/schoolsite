<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Watchdog;

class MenuController extends Controller
{

    public function index($menu = '')
    {
        $routename = [];
        $routeCollection = Route::getRoutes()->getRoutesByMethod();
        foreach ($routeCollection['GET'] as $value) {
            $name = $value->getName();
            if (!empty($name) && strpos($name, '.') === false) $routename[] = $name;
        }
        $menus = Menu::submenus();
        $instance = Menu::find($menu);
        if (!empty($instance)) {
            $items = $instance->childs;
        } else {
            $items = Menu::topmenus();
        }
        return view('admin.menus', ['current' => $menu, 'menu' => $instance, 'menus' => $menus, 'items' => $items, 'routes' => $routename]);
    }

    public function update(Request $request, $menu = '')
    {
        $ids = $request->input('ids');
        $captions = $request->input('captions');
        $parents = $request->input('parents');
        $urls = $request->input('urls');
        $weights = $request->input('weights');
        if (!empty($captions) && is_array($captions)) {
            foreach ($captions as $id => $title) {
                $m = Menu::find($id);
                if ($m) {
                    if (is_array($parents) && isset($parents[$id])) {
                        $m->parent_id = $parents[$id];
                    }
                    $m->caption = $title;
                    if (is_array($urls) && isset($urls[$id])) {
                        $m->url = $urls[$id];
                    }
                    if (is_array($weights) && isset($weights[$id])) {
                        $m->weight = $weights[$id];
                    }
                    $m->save();
                    Watchdog::watch($request, '更新選單項目：' . $m->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                }
            }
        }
        if (!empty($ids) && is_array($ids)) {
            foreach ($ids as $old => $new) {
                if ($old == $new) continue;
                $m = Menu::find($old);
                if ($m) {
                    $m->id = $new;
                    $m->save();
                    Watchdog::watch($request, '變更選單項目代號：' . $old . '->' . $new);
                }
            }
        }
        return redirect()->route('menus', ['menu' => $menu])->with('success', '選單項目已經更新！');
    }

    public function add($menu = '')
    {
        $routename = [];
        $routeCollection = Route::getRoutes()->getRoutesByMethod();
        foreach ($routeCollection['GET'] as $value) {
            $name = $value->getName();
            if (!empty($name) && strpos($name, '.') === false) $routename[] = $name;
        }
        return view('admin.menuadd', ['current' => $menu, 'routes' => $routename]);
    }

    public function insert(Request $request, $menu = '')
    {
        $mid = $request->input('mid');
        $caption = $request->input('caption');
        $kind = $request->input('kind');
        $url = ($kind == 'sub') ? '#' : ($request->input('url') ?: '#');
        $weight = intval($request->input('weight'));
        if ($menu == 'add' || (!empty($menu) && !Menu::find($menu))) {
            $menu = '';
        }
        $existing = Menu::find($mid);
        if ($existing) {
            if ($existing->parent_id == 'add') {
                $existing->parent_id = !empty($menu) ? $menu : null;
                $existing->caption = $caption;
                $existing->url = $url;
                $existing->weight = $weight;
                $existing->save();
                $m = $existing;
            } else {
                return redirect()->route('menus', ['menu' => $menu])->with('error', '選單識別碼「' . $mid . '」已經存在！');
            }
        } elseif (!empty($menu)) {
            $m = Menu::create([
                'id' => $mid,
                'parent_id' => $menu,
                'caption' => $caption,
                'url' => $url,
                'weight' => $weight,
            ]);
        } else {
            $m = Menu::create([
                'id' => $mid,
                'caption' => $caption,
                'url' => $url,
                'weight' => $weight,
            ]);
        }
        Watchdog::watch($request, '新增選單項目：' . $m->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('menus', ['menu' => $menu])->with('success', '選單項目新增完成！');
    }

    public function remove(Request $request, $menu)
    {
        $item = Menu::find($menu);
        if (!$item) {
            return redirect()->route('menus')->with('error', '找不到您要刪除的選單項目！');
        }
        $parent = $item->parent_id;
        if ($item->url == '#') {
            Menu::where('parent_id', $menu)->update([
                'parent_id' => $parent,
            ]);
        }
        Watchdog::watch($request, '移除選單項目：' . $item->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $item->delete();
        return redirect()->route('menus', ['menu' => $parent])->with('success', '選單項目已經刪除！');
    }

}
