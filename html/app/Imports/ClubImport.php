<?php

namespace App\Imports;

use App\Models\Club;
use App\Models\ClubSection;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ClubImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public $kind;

    public function __construct($kind)
    {
        $this->kind = $kind;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['name']) || !isset($row['short'])) {
                return null;
            }
            $unit = Unit::findByName($row['dep']);
            $grades = [];
            for ($i=0; $i<6; $i++) {
                if (substr($row['grade'], $i, 1) == '1') {
                    $grades[] = $i + 1;
                }
            }
            if ($row['week'] == '00000') {
                $self_define = true;
                $weekdays = null;
            } else {
                $self_define = false;
                $weekdays = [];
                for ($i=0; $i<5; $i++) {
                    if (substr($row['week'], $i, 1) == '1') {
                        $weekdays[] = $i + 1;
                    }
                }
            }
            $self_remove = false;
            if (isset($row['remove']) && $row['remove'] == '1') {
                $self_remove = true;
            }
            $has_lunch = false;
            if (isset($row['lunch']) && $row['lunch'] == '1') {
                $has_lunch = true;
            }
            if (is_string($row['sdate'])) {
                $sdate = $row['sdate'];
            } else {
                $sdate = ExcelDate::excelToDateTimeObject($row['sdate'])->format('Y-m-d');
            }
            if (is_string($row['edate'])) {
                $edate = $row['edate'];
            } else {
                $edate = ExcelDate::excelToDateTimeObject($row['edate'])->format('Y-m-d');
            }
            if ($row['stime'] > 1) {
                $stime = substr($row['stime'], 0, 2) . ':' . substr($row['stime'], -2);
            } else {
                $stime = ExcelDate::excelToDateTimeObject($row['stime'])->format('H:i');
            }
            if ($row['etime'] > 1) {
                $etime = substr($row['etime'], 0, 2) . ':' . substr($row['etime'], -2);
            } else {
                $etime = ExcelDate::excelToDateTimeObject($row['etime'])->format('H:i');
            }
            $club = Club::updateOrCreate([
                'name' => $row['name'],
            ],[
                'short_name' => $row['short'],
                'kind_id' => $this->kind,
                'unit_id' => $unit->id,
                'for_grade' => $grades,
                'self_remove' => $self_remove,
                'has_lunch' => $has_lunch,
                'stop_enroll' => false,
            ]);
            ClubSection::updateOrCreate([
                'section' => current_section(),
                'club_id' => $club->id,
            ],[
                'weekdays' => $weekdays,
                'self_defined' => $self_define,
                'startDate' => $sdate,
                'endDate' => $edate,
                'startTime' => $stime,
                'endTime' => $etime,
                'teacher' => $row['teacher'],
                'location' => $row['place'],
                'memo' => $row['memo'],
                'cash' => $row['cash'],
                'total' => $row['total'],
                'maximum' => $row['maxnum'],
            ]);
        }
    }
}