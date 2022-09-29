<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClubKind extends Model
{

	protected $table = 'club_kinds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'single',
        'stop_enroll',
        'manual_auditing',
        'enrollDate',
        'expireDate',
        'workTime',
        'restTime',
        'style',
        'weight',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'single' => 'boolean',
        'stop_enroll' => 'boolean',
        'manual_auditing' => 'boolean',
        'enrollDate' => 'datetime:Y-m-d',
        'expireDate' => 'datetime:Y-m-d',
    ];

    public function clubs()
    {
        return $this->hasMany('App\Models\Club', 'kind_id');
    }

    public function enroll_clubs()
    {
        return $this->clubs()->where('stop_enroll', false)->get();
    }

    public static function can_enroll()
    {
        $today = Carbon::now();
        return ClubKind::where('stop_enroll', false)
            ->where('enrollDate', '<=', $today)
            ->where('expireDate', '>=', $today)
            ->get();
    }

}
