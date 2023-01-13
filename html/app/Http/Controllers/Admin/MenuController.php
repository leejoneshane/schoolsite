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
        foreach ($captions as $id => $title) {
            $m = Menu::find($id);
            $m->parent_id = $parents[$id];
            $m->caption = $title;
            $m->url = $urls[$id];
            $m->weight = $weights[$id];
            $m->save();
            Watchdog::watch($request, '更新選單項目：' . $m->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        foreach ($ids as $old => $new) {
            if ($old == $new) continue;
            $m = Menu::find($old);
            $m->id = $new;
            $m->save();
            Watchdog::watch($request, '變更選單項目代號：' . $old . '->' . $new);
        }
        return redirect()->route('menus')->with('success', '選單項目已經更新！');
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
        $url = $request->input('url');
        $weight = $request->input('weight');
        if (!empty($menu)) {
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
        return redirect()->route('menus')->with('success', '選單項目新增完成！');
    }

    public function remove(Request $request, $menu)
    {
        $item = Menu::find($menu);
        $parent = $item->parent_id;
        if ($item->url == '#') {
            Menu::where('parent_id', $menu)->update([
                'parent_id' => $parent,
            ]);
        }
        Watchdog::watch($request, '移除選單項目：' . $item->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $item->delete();
        return redirect()->route('menus')->with('success', '選單項目已經刪除！');
    }

}
