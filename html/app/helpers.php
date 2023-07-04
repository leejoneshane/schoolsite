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

function current_seme() {
    if (date('m') > 1 || date('m') < 8) {
        $seme = 2;
    } else {
        $seme = 1;
    }
    return $seme;
}

function current_section() {
    return current_year() . current_seme();
}

function prev_section($section = null) {
    if (!$section) $section = current_section();
    $year = (integer) substr($section, 0, -1);
    $seme = (integer) substr($section, -1);
    if ($seme == 1) {
        return ($year - 1).'2';
    } else {
        return $year.'1';
    }
}

function next_section($section = null) {
    if (!$section) $section = current_section();
    $year = (integer) substr($section, 0, -1);
    $seme = (integer) substr($section, -1);
    if ($seme == 1) {
        return $year.'2';
    } else {
        return ($year + 1).'1';
    }
}

function current_between_date() {
    if (date('m') > 7) {
        $syear = date('Y');
        $eyear = $syear + 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $mindate = "$syear-08-01";
        $max = "$eyear-01-31T23:59:59+08:00";
        $maxdate = "$eyear-01-31";
    } elseif (date('m') < 2) {
        $eyear = date('Y');
        $syear = $eyear - 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $mindate = "$syear-08-01";
        $max = "$eyear-01-31T23:59:59+08:00";
        $maxdate = "$eyear-01-31";
    } else {
        $syear = date('Y');
        $min = "$syear-02-01T00:00:00+08:00";
        $mindate = "$syear-02-01";
        $max = "$syear-07-31T23:59:59+08:00";
        $maxdate = "$syear-07-31";
    }

    return (object) array('min' => $min, 'max' => $max, 'mindate' => $mindate, 'maxdate' => $maxdate);
}

function between_date($syear, $seme = null) {
    if (!$seme) {
        $syear = $syear + 1911;
        $eyear = $syear + 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $mindate = "$syear-08-01";
        $max = "$eyear-01-31T23:59:59+08:00";
        $maxdate = "$eyear-07-31";
    } elseif ($seme == 1) {
        $syear = $syear + 1911;
        $eyear = $syear + 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $mindate = "$syear-08-01";
        $max = "$eyear-01-31T23:59:59+08:00";
        $maxdate = "$eyear-01-31";
    } else {
        $syear = $syear + 1912;
        $min = "$syear-02-01T00:00:00+08:00";
        $mindate = "$syear-02-01";
        $max = "$syear-07-31T23:59:59+08:00";
        $maxdate = "$syear-07-31";
    }

    return (object) array('min' => $min, 'max' => $max, 'mindate' => $mindate, 'maxdate' => $maxdate);
}

function section_between_date($section = null) {
    if (!$section) {
        return current_between_date();
    } else {
        $year = (integer) substr($section, 0, -1);
        $seme = (integer) substr($section, -1);
        return between_date($year, $seme);
    }
}

function find_solutions(Array $assoc, int $mean) {
    $solution = [];
    $k = array_key_first($assoc);
    $v = $assoc[$k];
    $mod = $mean - $v;
    unset($assoc[$k]);
    if (count($assoc) < 1) {
        $solution[] = [ 'classes' => [ $k ], 'sum' => $v ];
    } else {
        list($p, $q, $keys) = near_search($assoc, $mod);
        if ($p != 'none') {
            $solution[] = [ 'classes' => [ $k, $p ], 'sum' => $v + $assoc[$p] ];
        }
        if ($q != 'none') {
            if ($assoc[$q] == $mod) {
                $solution[] = [ 'classes' => [ $k, $q ], 'sum' => $v + $assoc[$q] ];
            } else {
                if (empty($keys)) {
                    $sub_solution = find_solutions($assoc, $mod);
                    foreach ($sub_solution as $sub) {
                        $solution[] = [ 'classes' => array_merge([$k], $sub['classes']), 'sum' => $v + $sub['sum'] ];
                    }
                } else {
                    $assoc = array_slice_assoc_inverse($assoc, $keys);
                    if (!empty($assoc)) {
                        $sub_solution = find_solutions($assoc, $mod);
                        foreach ($sub_solution as $sub) {
                            $solution[] = [ 'classes' => array_merge([$k], $sub['classes']), 'sum' => $v + $sub['sum'] ];
                        }
                    }
                }
            }
        }
    }
    return $solution;
}

function near_search(Array $assoc, int $x) {
    $p = $q = 'none';
    $keys = [];
    foreach ($assoc as $k => $v) {
        if ($v > $x) {
            $p = $k;
            $keys[] = $p;
        } else {
            $q = $k;
            break;
        }
    }
    return [ $p, $q, $keys ];
}

function array_slice_assoc($array, $keys) {
    return array_intersect_key($array, array_flip($keys));
}

function array_slice_assoc_inverse($array, $keys) {
    return array_diff_key($array, array_flip($keys));
}