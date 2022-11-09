<?php

namespace App\Exports;

use App\Models\Seniority;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SeniorityExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping
{
    use Exportable;

    public function collection()
    {
        return Seniority::current();
    }

    public function headings(): array
    {
        return [
            [
                '臺北市國語實驗國民小學'.Seniority::current_year().'學年度教師教學年資統計  '.date('Y-m-d').'匯出',
            ],[
                '編號', '職別', '姓名', '在校年', '在校月', '在校積分', '校外年', '校外月', '校外積分', '教學年資', '總積分', '備註',
            ]
        ];
    }

    public function map($row): array
    {
        return [
            $row->no,
            ($row->teacher->tutor) ?: $row->teacher->role_name,
            $row->teacher->realname,
            ($row->new_school_year) ?: $row->school_year,
            ($row->new_school_month) ?: $row->school_month,
            ($row->new_school_score) ?: $row->school_score,
            ($row->new_teach_year) ?: $row->teach_year,
            ($row->new_teach_month) ?: $row->teach_month,
            ($row->new_teach_score) ?: $row->teach_score,
            ($row->newyears > 0) ? $row->newyears : $row->years,
            ($row->newscore > 0) ? $row->newscore : $row->score,
            '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER_00,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER_00,
            'J' => NumberFormat::FORMAT_NUMBER_00,
            'K' => NumberFormat::FORMAT_NUMBER_00,
            'L' => NumberFormat::FORMAT_TEXT,
        ];
    }

}