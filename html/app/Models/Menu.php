<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    public $incrementing = false;
    protected $keyType = 'string';

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'parent_id',
        'caption',
        'url',
        'weight',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'parents',
        'childs',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'top',
        'link',
    ];

    //提供此選單項目的頂層選單編號
    public function getTopAttribute()
    {
        $id = $this->id;
        $parents = $this->parents;
        while (!is_null($parents)) {
            $id = $parents->id;
            $parents = $parents->parents;
        }
        return $id;
    }

    //提供此選單項目的連結網址
    public function getLinkAttribute()
    {
        return (substr($this->url, 0, 6) == 'route.') ? route(substr($this->url, 6)) : $this->url;
    }

    //取得此選單項目的上層選單物件
    public function parents()
    {
        return $this->hasOne('App\Models\Menu', 'id', 'parent_id');
    }

    //取得此選單項目的所有次選單（若此選項項目是連結而非選單，則傳回 null）
    public function childs()
    {
        return $this->hasMany('App\Models\Menu', 'parent_id', 'id')->orderBy('weight');
    }

    //篩選所有頂層選單，靜態函式
    public static function topmenus()
    {
        return Menu::whereNull('parent_id')->orderBy('weight')->get();
    }

    //篩選所有選單，靜態函式
    public static function submenus()
    {
        return Menu::where('url', '#')->orderBy('weight')->get();
    }

   //產生選單網頁內容
    public function render()
    {
        $html = '<ul>';
        $items = $this->childs->sortBy('weight')->get();
        foreach ($items as $item) {
            $html .= '<li>';
            if ($item->link == '#') {
                $html .= '<span>' . $item->caption . '</span>';
            } else {
                $html .= '<a href="' . $item->link . '">' . $item->caption . '</a>';
            }
            if ($item->childs->count() > 0) {
                $html .= $item->render();
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}
