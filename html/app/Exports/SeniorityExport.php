<?php

namespace App\Exports;

use App\Models\Seniority;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class SeniorityExport implements FromCollection, WithHeadings, WithColumnFormatting, WithStyles, WithMapping
{
    use Exportable;

    public $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function collection()
    {
        return Seniority::year_teachers($this->year)->orderByRaw('unit_id = 25')->orderBy('tutor_class')->get();
    }

    public function headings(): array
    {
        return [
            [
                '臺北市國語實驗國民小學'.$this->year.'學年度教師教學年資統計  '.date('Y-m-d').'匯出',
            ],[
                '唯一編號', '職別', '姓名', '在校年資', '', '', '校外年資', '', '', '教學年資', '總積分', '備註',
            ],[
                '', '', '', '在校年', '在校月', '在校積分', '校外年', '校外月', '校外積分', '', '', '',
            ],
        ];
    }

    public function map($row): array
    {
        $seniority = $row->seniority($this->year);
        return [
            $row->uuid,
            ($row->tutor) ?: (($row->unit_id == 25 && $row->domain) ? $row->domain->name : $row->role_name),
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
            'A' => NumberFormat::FORMAT_TEXT,
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

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:L1')->getStyle('A1:L1')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
        ]);
        $sheet->mergeCells('D2:F2')->getStyle('D2:F2')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
        ]);
        $sheet->mergeCells('G2:I2')->getStyle('G2:I2')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
        ]);
        $sheet->mergeCells('A2:A3')->getStyle('A2:A3')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('B2:B3')->getStyle('B2:B3')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('C2:C3')->getStyle('C2:C3')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('J2:J3')->getStyle('J2:J3')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('K2:K3')->getStyle('K2:K3')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('L2:L3')->getStyle('L2:L3')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
    }

}