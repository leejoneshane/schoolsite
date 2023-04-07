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

    public $year;
    public $no;

    public function __construct($year)
    {
        $this->year = $year;
        $this->no = 0;
    }

    public function collection()
    {
        return Seniority::year_teachers($this->year)->orderBy('tutor_class')->orderBy('unit_id')->get();
    }

    public function headings(): array
    {
        return [
            [
                '臺北市國語實驗國民小學'.$this->year.'學年度教師教學年資統計  '.date('Y-m-d').'匯出',
            ],[
                '編號', '職別', '姓名', '在校年', '在校月', '在校積分', '校外年', '校外月', '校外積分', '教學年資', '總積分', '備註',
            ]
        ];
    }

    public function map($row): array
    {
        $this->no ++;
        $seniority = $row->seniority($this->year);
        return [
            $this->no,
            ($row->tutor) ?: $row->role_name,
            $row->realname,
            ($seniority->new_school_year) ?: $seniority->school_year,
            ($seniority->new_school_month) ?: $seniority->school_month,
            ($seniority->new_school_score) ?: $seniority->school_score,
            ($seniority->new_teach_year) ?: $seniority->teach_year,
            ($seniority->new_teach_month) ?: $seniority->teach_month,
            ($seniority->new_teach_score) ?: $seniority->teach_score,
            ($seniority->newyears > 0) ? $seniority->newyears : $seniority->years,
            ($seniority->newscore > 0) ? $seniority->newscore : $seniority->score,
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