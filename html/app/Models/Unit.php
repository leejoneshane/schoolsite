<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{

	protected $table = 'units';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unit_no',
        'name',
    ];

    public static function main()
    {
        return Unit::whereRaw('CHAR_LENGTH(unit_no) = 3 or LEFT(unit_no, 1) = ?', ['Z'])->get();
    }

    public static function sub($main)
    {
        if (strlen($main) > 3) return false;
        return Unit::whereRaw('LEFT(unit_no, 3) = ?', [$main])->get();
    }

    public static function subkeys($main = '')
    {
        if (strlen($main) > 3) {
            $u = Unit::where('unit_no', $main)->first();
            return array($u->id);
        }
        $keys = [];
        if (empty($main)) {
            $all_subs = Unit::whereRaw('CHAR_LENGTH(unit_no) > 3 and LEFT(unit_no, 1) <> ?', ['Z'])->get();
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

    public function is_main()
    {
        if (strlen($this->unit_no) == 3 || substr($this->unit_no, 0, 1) == 'Z') return true;
        return false;
    }

    public function uplevel()
    {
        return Unit::where('unit_no', substr($this->unit_no, 0, 3))->first();
    }

    public function roles()
    {
        return $this->hasMany('App\Models\Role');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'unit_id', 'uuid');
    }

}
