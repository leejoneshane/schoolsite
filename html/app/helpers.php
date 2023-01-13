<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;

function watch(Request $request, $action) {
    $device = Agent::device();
    $platform = Agent::platform();
    $platform .= Agent::version($platform);
    $browser = Agent::browser();
    $browser .= Agent::version($browser);
    $robot = Agent::robot();
    DB::table('watchdog')->insert([
        'uuid' => $request->user()->uuid,
        'ip' => $request->header('x-real-ip'),
        'device' => $device,
        'platform' => $platform,
        'browser' => $browser,
        'robot' => $robot,
        'url' => $request->fullUrl(),
        'action' => $action,
    ]);
}

function current_year() {
    if (date('m') > 7) {
        $year = date('Y') - 1911;
    } else {
        $year = date('Y') - 1912;
    }
    return $year;
}

function current_years() {
    if (date('m') > 7) {
        $years = [ date('Y'), date('Y') + 1 ];
    } elseif (date('m') < 2) {
        $years = [ date('Y') - 1, date('Y') ];
    } else {
        $years = [ date('Y') ];
    }
    return $years;
}

function current_seme() {
    if (date('m') > 1 || date('m') < 8) {
        $seme = 2;
    } else {
        $seme = 1;
    }
    return $seme;
}

function current_between_date() {
    if (date('m') > 7) {
        $syear = date('Y');
        $eyear = $syear + 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $mindate = "$syear-08-01";
        $max = "$eyear-01-31T00:00:00+08:00";
        $maxdate = "$eyear-01-31";
    } elseif (date('m') < 2) {
        $eyear = date('Y');
        $syear = $eyear - 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $mindate = "$syear-08-01";
        $max = "$eyear-01-31T00:00:00+08:00";
        $maxdate = "$eyear-01-31";
    } else {
        $syear = date('Y');
        $min = "$syear-02-01T00:00:00+08:00";
        $mindate = "$syear-02-01";
        $max = "$syear-07-31T00:00:00+08:00";
        $maxdate = "$syear-07-31";
    }

    return (object) array('min' => $min, 'max' => $max, 'mindate' => $mindate, 'maxdate' => $maxdate);
}

function between_date($syear, $seme) {
    if ($seme == 1) {
        $syear = $syear + 1911;
        $eyear = $syear + 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $mindate = "$syear-08-01";
        $max = "$eyear-01-31T00:00:00+08:00";
        $maxdate = "$eyear-01-31";
    } else {
        $syear = $syear + 1912;
        $min = "$syear-02-01T00:00:00+08:00";
        $mindate = "$syear-02-01";
        $max = "$syear-07-31T00:00:00+08:00";
        $maxdate = "$syear-07-31";
    }

    return (object) array('min' => $min, 'max' => $max, 'mindate' => $mindate, 'maxdate' => $maxdate);
}