<?php

namespace App\Exports;

use App\Models\Club;
use App\Models\ClubEnroll;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClubCashExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping
{
    use Exportable;

    public $section;
    public $clubs;

    public function __construct($section = null)
    {
        if (!$section) $section = next_section();
        $this->section = $section;
        $this->clubs = Club::section_clubs();
    }

    public function collection()
    {
        $enrolls = collect();
        foreach ($this->clubs as $club) {
            $enrolls = $enrolls->merge($club->section_accepted());
        }
        $enrolls = $enrolls->sortBy('uuid');
        $collection = [];
        $old = '';
        $record = new \stdClass;
        $record->clubs = [];
        foreach ($enrolls as $enroll) {
            $sec = $enroll->club_section();
            if (!$old) { //first
                $old = $enroll->uuid;
                $record->grade = substr($enroll->student->class_id, 0, 1);
                $record->myclass = substr($enroll->student->class_id, -2);
                $record->seat = $enroll->student->seat;
                $record->student = $enroll->student;
                $record->clubs[$enroll->club_id] = $sec->cash;
            } elseif ($old != $enroll->uuid) { //prev
                $total = 0;
                foreach ($record->clubs as $cash) {
                    $total += $cash;
                }
                $record->total = $total;
                $collection[] = $record;
                $old = $enroll->uuid;
                $record = new \stdClass;
                $record->clubs = [];
                $record->grade = substr($enroll->student->class_id, 0, 1);
                $record->myclass = substr($enroll->student->class_id, -2);
                $record->seat = $enroll->student->seat;
                $record->student = $enroll->student;
                $record->clubs[$enroll->club_id] = $sec->cash;
            } else {
                $record->clubs[$enroll->club_id] = $sec->cash;
            }
        }
        //last one
        $total = 0;
        foreach ($record->clubs as $cash) {
            $total += $cash;
        }
        $record->total = $total;
        $collection[] = $record;

        $total = 0;
        $append = new \stdClass;
        $append->student = null;
        $append->grade = 99;
        $append->myclass = null;
        $append->seat = null;
        $append->clubs = [];
        foreach ($collection as $c) {
            foreach ($c->clubs as $id => $cash) {
                $total += $cash;
                if (!isset($append->clubs[$id])) {
                    $append->clubs[$id] = $cash;
                } else {
                    $append->clubs[$id] += $cash;
                }
            }
        }
        $append->total = $total;
        $collection[] = $append;
        $rows = collect($collection)->sortBy([
            ['grade', 'asc'],
            ['myclass', 'asc'],
            ['seat', 'asc']
        ]);
        return $rows;
    }

    public function headings(): array
    {
        $headings = [
            '年',
            '班',
            '座號',
            '學號',
            '生日',
            '姓名',
        ];
        foreach ($this->clubs as $club) {
            $headings[] = $club->short_name;
        }
        $headings[] = '小計';
        return $headings;
    }

    public function map($row): array
    {
        if (is_null($row->student)) {
            $map = [ '總計', '', '', '', '', '' ];
        } else {
            $m = $row->student->birthdate->format('m');
            $d = $row->student->birthdate->format('d');
            $map = [
                $row->grade,
                $row->myclass,
                $row->seat,
                $row->student->id,
                $m.'月'.$d.'日',
                $row->student->realname,
            ];
        }
        foreach ($this->clubs as $club) {
            if (isset($row->clubs[$club->id])) {
                $map[] = $row->clubs[$club->id];
            } else {
                $map[] = 0;
            }
        }
        $map[] = $row->total;
        return $map;
    }

    public function columnFormats(): array
    {
        $formats = [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
        ];
        $asc1 = 64;
        $asc2 = ord('G');
        foreach ($this->clubs as $club) {
            if ($asc1 < 65) {
                $key = chr($asc2);
            } else {
                $key = chr($asc1).chr($asc2);
            }
            $formats[$key] = NumberFormat::FORMAT_NUMBER;
            $asc2 += 1;
            if ($asc2 > 90) {
                $asc1 += 1;
                $asc2 = 65;
            }
        }
        if ($asc1 < 65) {
            $key = chr($asc2);
        } else {
            $key = chr($asc1).chr($asc2);
        }
        $formats[$key] = NumberFormat::FORMAT_NUMBER;
        return $formats;
    }

}