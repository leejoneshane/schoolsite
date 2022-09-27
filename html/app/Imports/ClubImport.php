<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Club;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ClubImport implements ToModel, WithHeadingRow, WithUpserts
{
    use Importable;

    public $kind;

    public function __construct($kind)
    {
        $this->kind = $kind;
    }

    public function uniqueBy()
    {
        return 'name';
    }

    public function model(array $rows)
    {
        if (!isset($row['name']) || !isset($row['short'])) {
            return null;
        }
        $unit = Unit::findByName($rows['dep']);
        $grades = [];
        for ($i=0; $i<6; $i++) {
            if (substr($rows['grade'], $i, 1) == '1') {
                $grades[] = $i + 1;
            }
        }
        if ($rows['week'] == '00000') {
            $self_define = true;
            $weekdays = null;
        } else {
            $self_define = false;
            $weekdays = [];
            for ($i=0; $i<5; $i++) {
                if (substr($rows['week'], $i, 1) == '1') {
                    $weekdays[] = $i + 1;
                }
            }    
        }
        if ($rows['remove'] == '1') {
            $self_remove = true;
        } else {
            $self_remove = false;
        }
        if ($rows['lunch'] == '1') {
            $has_lunch = true;
        } else {
            $has_lunch = false;
        }
        $sdate = Carbon::parse($rows['sdate'])->format('Y-m-d');
        $edate = Carbon::parse($rows['edate'])->format('Y-m-d');
        return new Club([
            'name' => $rows['name'],
            'short_name' => $rows['short'],
            'kind_id' => $this->kind,
            'unit_id' => $unit->id,
            'for_grade' => $grades,
            'weekdays' => $weekdays,
            'self_defined' => $self_define,
            'self_remove' => $self_remove,
            'has_lunch' => $has_lunch,
            'stop_enroll' => false,
            'startDate' => $sdate,
            'endDate' => $edate,
            'startTime' => $rows['stime'],
            'endTime' => $rows['etime'],
            'teacher' => $rows['teacher'],
            'location' => $rows['location'],
            'memo' => $rows['memo'],
            'cash' => $rows['cash'],
            'total' => $rows['total'],
            'maximum' => $rows['maxnum'],
        ]);
    }
}