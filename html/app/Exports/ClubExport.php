<?php

namespace App\Exports;

use App\Models\Club;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClubExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping
{
    use Exportable;

    public $kind;

    public function __construct($kind)
    {
        $this->kind = $kind;
    }

    public function collection()
    {
        $rows = Club::where('kind_id', $this->kind)->get();
        foreach ($rows as $key => $row) {
            if ($row->section() == null) {
                $rows->forget($key);
            }
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'dep',
            'name',
            'short',
            'grade',
            'week',
            'sdate',
            'edate',
            'stime',
            'etime',
            'teacher',
            'place',
            'cash',
            'total',
            'maxnum',
            'memo',
            'lunch',
            'remove',
        ];
    }

    public function map($row): array
    {
        $section = $row->section();
        $unit = $row->unit->name;
        $grades = [0,0,0,0,0,0];
        for ($i=0; $i<6; $i++) {
            if (in_array($i + 1, $row->for_grade)) {
                $grades[$i] = 1;
            }
        }
        $grade = implode('', $grades);
        if ($section->self_defined) {
            $week = '00000';
        } else {
            $weekdays = [0,0,0,0,0];
            for ($i=0; $i<5; $i++) {
                if (in_array($i + 1, $section->weekdays)) {
                    $weekdays[$i] = 1;
                }
            }
            $week = implode('', $weekdays);
        }
        $lunch = 0;
        if ($row->has_lunch) $lunch = 1;
        $remove = 0;
        if ($row->self_remove) $remove = 1;
        return [
            $unit,
            $row->name,
            $row->short_name,
            $grade,
            $week,
            $section->startDate->format('Y-m-d'),
            $section->endDate->format('Y-m-d'),
            $section->startTime,
            $section->endTime,
            $section->teacher,
            $section->location,
            $section->cash,
            $section->total,
            $section->maximum,
            $section->memo,
            $lunch,
            $remove,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_NUMBER,
            'M' => NumberFormat::FORMAT_NUMBER,
            'N' => NumberFormat::FORMAT_NUMBER,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_NUMBER,
            'Q' => NumberFormat::FORMAT_NUMBER,
        ];
    }

}