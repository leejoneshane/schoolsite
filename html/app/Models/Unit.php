<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{

    protected $table = 'units';

    //以下屬性可以批次寫入
    protected $fillable = [
        'unit_no',
        'name',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'roles',
        'teachers',
    ];

    //取得指定代碼的單位物件，靜態函式
    public static function findByNo($unit_no)
    {
        return Unit::where('unit_no', $unit_no)->first();
    }

    //搜尋指定名稱的單位，靜態函式
    public static function findByName($name)
    {
        return Unit::where('name', 'like', '%'.$name.'%')->first();
    }

    //取得所有的主要單位，靜態函式
    public static function main()
    {
        return Unit::whereRaw('CHAR_LENGTH(unit_no) = 3 or LEFT(unit_no, 1) = ?', ['Z'])->get();
    }

    //取得指定代碼單位的所有下屬單位，靜態函式
    public static function sub($main)
    {
        if (strlen($main) > 3) return false;
        return Unit::whereRaw('LEFT(unit_no, 3) = ?', [$main])->get();
    }

    //取得下屬單位的所有編號並傳回陣列，靜態函式
    public static function subkeys($main = '')
    {
        if (strlen($main) > 3) {
            $u = Unit::where('unit_no', $main)->first();
            return array($u->id);
        }
        $keys = [];
        if (empty($main)) {
            $all_subs = Unit::whereRaw('CHAR_LENGTH(unit_no) > 3 and LEFT(unit_no, 1) <> ?', 'Z')->get();
            if ($all_subs) {
                foreach ($all_subs as $sub) {
                    $keys[] = $sub->id;
                }
            }
        } else {
            $subs = Unit::sub($main);
            if ($subs) {
                foreach ($subs as $sub) {
                    $keys[] = $sub->id;
                }
            }
        }
        return $keys;
    }

    //檢查目前單位是否為主要單位
    public function is_main()
    {
        if (strlen($this->unit_no) == 3 || substr($this->unit_no, 0, 1) == 'Z') return true;
        return false;
    }

    //取得目前單位的上層單位，若已經是上層單位則取得自己
    public function uplevel()
    {
        return Unit::where('unit_no', substr($this->unit_no, 0, 3))->first();
    }

    //取得目前單位的所有職務
    public function roles()
    {
        return $this->hasMany('App\Models\Role');
    }

    //檢查目前單位中所有在職教師
    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'unit_id', 'uuid')->where('year', current_year());
    }

}
