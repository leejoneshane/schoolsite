<?php

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
        $max = "$eyear-01-31T00:00:00+08:00";
    } elseif (date('m') < 2) {
        $eyear = date('Y');
        $syear = $eyear - 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $max = "$eyear-01-31T00:00:00+08:00";
    } else {
        $syear = date('Y');
        $min = "$syear-02-01T00:00:00+08:00";
        $max = "$syear-07-31T00:00:00+08:00";
    }

    return (object) array('min' => $min, 'max' => $max);
}

function between_date($syear, $seme) {
    if ($seme == 1) {
        $syear = $syear + 1911;
        $eyear = $syear + 1;
        $min = "$syear-08-01T00:00:00+08:00";
        $max = "$eyear-01-31T00:00:00+08:00";
    } else {
        $syear = $syear + 1912;
        $min = "$syear-02-01T00:00:00+08:00";
        $max = "$syear-07-31T00:00:00+08:00";
    }

    return (object) array('min' => $min, 'max' => $max);
}