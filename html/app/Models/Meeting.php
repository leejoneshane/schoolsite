<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\Subscribeable;

class Meeting extends Model implements Subscribeable
{

    protected $table = 'meeting';
    const template = 'emails.meeting';

    protected $fillable = [
        'unit_id',
        'role',
        'reporter',
        'words',
        'inside',
        'expired_at',
    ];

    protected $casts = [
        'inside' => 'boolean',
        'expired_at' => 'datetime:Y-m-d',
    ];

    public function newsletter()
    {
        $meets = Meeting::inTimeOpen(date('Y-m-d'));
        return ['meets' => $meets];
    }

    public function unit()
    {
        return $this->hasOne('App\Models\Unit', 'id', 'unit_id');
    }

    public static function inTime($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return Meeting::whereDate('created_at', '<=', $dt)
            ->whereDate('expired_at', '>=', $dt)
            ->orderBy('unit_id')
            ->get();
    }

    public static function inTimeInside($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return Meeting::where('inside', true)
            ->whereDate('created_at', '<=', $dt)
            ->whereDate('expired_at', '>=', $dt)
            ->orderBy('unit_id')
            ->get();
    }

    public static function inTimeOpen($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return IcsEvent::where('inside', false)
            ->whereDate('created_at', '<=', $dt)
            ->whereDate('expired_at', '>=', $dt)
            ->orderBy('unit_id')
            ->get();
    }

}