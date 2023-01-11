<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Watchdog extends Model
{

    protected $table = 'watchdog';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'ip',
        'device',
        'platform',
        'browser',
        'robot',
        'url',
        'action',
    ];

    //紀錄瀏覽歷程
    public static function watch(Request $request, $action)
    {
        $device = Agent::device();
        $platform = Agent::platform();
        $platform .= Agent::version($platform);
        $browser = Agent::browser();
        $browser .= Agent::version($browser);
        $robot = Agent::robot();
        $dog = Watchdog::create([
            'uuid' => $request->user()->uuid,
            'ip' => $request->ip(),
            'device' => $device,
            'platform' => $platform,
            'browser' => $browser,
            'robot' => $robot,
            'url' => $request->fullUrl(),
            'action' => $action,
        ]);
        return $dog;
    }

    //取得指定日期的瀏覽紀錄，靜態函式
    public static function findByDate($date = null)
    {
        if (is_null($date)) {
            $date = Carbon::today();
        } elseif (is_string($date)) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }
        return Watchdog::whereRaw('DATE(created_at) = ?', $date->format('Y-m-d'))->latest()->get();
    }

    //搜尋指定IP的瀏覽紀錄，靜態函式
    public static function findByIp($ip)
    {
        return Watchdog::where('ip', $ip)->latest()->get();
    }

    //搜尋指定帳號的瀏覽紀錄，靜態函式
    public static function findByUuid($uuid = null)
    {
        if (!$uuid) {
            $uuid = auth()->user()->uuid;
        }
        return Watchdog::where('uuid', $uuid)->latest()->get();
    }

    //取得產生此紀錄的帳號
    public function who()
    {
        return $this->belongsTo('App\Models\User', 'uuid', 'uuid');
    }

}
