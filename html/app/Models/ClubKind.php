<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}
