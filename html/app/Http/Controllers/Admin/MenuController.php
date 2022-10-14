<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index($menu = '', $message = null)
    {
        $routename = [];
        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $value) {
            $name = $value->getName();
            if (!empty($name) && strpos($name, '.') === false) $routename[] = $name;
        }
        $menus = Menu::submenus();
        $instance = Menu::find($menu);
        if (!empty($menu)) {
            $items = $instance->childs;
        } else {
            $items = Menu::topmenus();
        }
        if ($message) {
            $key = array_key_first($message);
            $val = $message[$key];
            return view('admin.menus', ['current' => $menu, 'menu' => $instance, 'menus' => $menus, 'items' => $items, 'routes' => $routename])
                ->with($key, $val);
        } else {
            return view('admin.menus', ['current' => $menu, 'menu' => $instance, 'menus' => $menus, 'items' => $items, 'routes' => $routename]);
        }
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
        }
        foreach ($ids as $old => $new) {
            if ($old == $new) continue;
            $m = Menu::find($old);
            $m->id = $new;
            $m->save();
        }
        return $this->index($menu, ['success' => '選單項目已經更新！']);
    }

    public function add($menu = '')
    {
        $routename = [];
        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $value) {
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
            Menu::create([
                'id' => $mid,
                'parent_id' => $menu,
                'caption' => $caption,
                'url' => $url,
                'weight' => $weight,
            ]);
        } else {
            Menu::create([
                'id' => $mid,
                'caption' => $caption,
                'url' => $url,
                'weight' => $weight,
            ]);
        }
        return $this->index($menu, ['success' => '選單項目新增完成！']);
    }

    public function remove($menu)
    {
        $item = Menu::find($menu);
        $parent = $item->parent_id;
        if ($item->url == '#') {
            Menu::where('parent_id', $menu)->update([
                'parent_id' => $parent,
            ]);
        }
        $item->delete();
        return $this->index($parent, ['success' => '選單項目已經刪除！']);
    }

}
