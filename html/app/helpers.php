<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;

const GAME_CHARACTER = 'images/game/characters/'; //遊戲職業圖片路徑
const GAME_FACE = 'images/game/faces/'; //遊戲臉孔圖片路徑
const GAME_SKILL = 'images/game/skills/'; //遊戲技能圖片路徑
const GAME_BASE = 'images/game/bases/'; //遊戲據點圖片路徑
const GAME_FURNITURE = 'images/game/furnitures/'; //遊戲家具圖片路徑
const GAME_ITEM = 'images/game/items/'; //遊戲道具圖片路徑
const NOT_ENOUGH_GP = 1; //無法購買
const ALREADY_EXISTS = 2; //不可重複購買
const NOT_EXISTS = 3; //物品已用完
const LOCK_ALREADY = 4; //班級已被鎖定
const MISS = 5; //攻擊失敗或物品使用失敗
const PEACE = 6; //非戰鬥時刻
const LESS_MP = 7; //行動力不足
const DEAD = 8; //角色已死亡
const COMA = 9; //角色已昏迷
const NORMAL = 10; //角色正常


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

function section_name($section = null) {
    if (!$section) $section = current_section();
    $seme = substr($section, -1);
    if ($seme == 1) {
        $strseme = '上學期';
    } else {
        $strseme = '下學期';
    }
    return '第'.substr($section, 0, -1).'學年'.$strseme;
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
    if (date('m') > 1 && date('m') < 8) {
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

function which_section($date) {
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    $m = $date->format('n');
    if ($m > 7) {
        $syear = $date->format('Y') - 1911;
    } else {
        $syear = $date->format('Y') - 1912;
    }
    if ($m > 1 && $m < 8) {
        $seme = 2;
    } else {
        $seme = 1;
    }
    return $syear . $seme;
}

//學生社團於暑假期間開課，屬於上一個學期
function club_section($date) {
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    $m = $date->format('n');
    if ($m > 6) {
        $syear = $date->format('Y') - 1911;
    } else {
        $syear = $date->format('Y') - 1912;
    }
    if ($m > 1 && $m < 9) {
        $seme = 2;
    } else {
        $seme = 1;
    }
    return $syear . $seme;
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
    if (empty($assoc)) return false;
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
                    if (!empty($assoc)) {
                        $sub_solution = find_solutions($assoc, $mod);
                        foreach ($sub_solution as $sub) {
                            $solution[] = [ 'classes' => array_merge([$k], $sub['classes']), 'sum' => $v + $sub['sum'] ];
                        }
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