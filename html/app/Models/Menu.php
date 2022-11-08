<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'parent_id',
        'caption',
        'url',
        'weight',
    ];

    protected $appends = [
        'top',
        'link',
    ];

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

    public function getLinkAttribute()
    {
        return (substr($this->url, 0, 6) == 'route.') ? route(substr($this->url, 6)) : $this->url;
    }

    public function parents()
    {
        return $this->hasOne('App\Models\Menu', 'id', 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany('App\Models\Menu', 'parent_id', 'id')->orderBy('weight');
    }

    public static function topmenus()
    {
        return Menu::whereNull('parent_id')->orderBy('weight')->get();
    }

    public static function submenus()
    {
        return Menu::where('url', '#')->orderBy('weight')->get();
    }

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
