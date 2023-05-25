<?php

namespace App\Exports;

use App\Models\Club;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class ClubTimeExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    use Exportable;

    public $club;
    public $rows = [];
    public $total = 4;

    public function __construct($club_id)
    {
        $this->club = Club::find($club_id);
    }

    public function collection()
    {
        $enrolls = $this->club->section_accepted()->sortBy(function ($en) {
            return $en->student->stdno;
        });
        return $enrolls;
    }

    public function headings(): array
    {
        return [
            [ '臺北市國語實驗國民小學學生社團【'.$this->club->name.'】課後照顧班與社團時間序列表' ],
            [ '月份：', '', '指導老師簽名：', '' ],
            [ '編號', '年班', '座號', '姓名', '課後與社團活動一覽表', '', '' ],
            [ '', '', '', '', '中午', '下午', '晚上']
        ];
    }

    public function prepareRows($rows)
    {
        $i = 1;
        foreach ($rows as $k => $row) {
            $rows[$k]->no = $i;
            $clubs = [];
            $next = $row->student->section_enrolls()->reject($row);
            foreach ($next as $en) {
                $sec = $en->club_section();
                if ($sec->startTime < '16:00:00') {
                    $rows[$k]->period0[] = $en;
                } elseif ($sec->startTime < '17:00:00') {
                    $rows[$k]->period1[] = $en;
                } else {
                    $rows[$k]->period2[] = $en;
                }
            }
            $num = max(count($clubs[0]), count($clubs[1]), count($clubs[2]));
            $rows[$k]->count = $num;
            $this->rows[$i] = $num;
            $this->total += $num;
            $i++;
        }
        return $rows;
    }

    public function map($row): array
    {
        $num = $row->count;
        $map[0] = [
            $row->no,
            $row->student->class_id,
            $row->student->seat,
            $row->student->realname,
            '',
            '',
            '',
        ];
        for ($i=1;$i<$num;$i++) {
            $map[$i] = [ '', '', '', '', '', '', '' ];
        }
        for ($i=0;$i<$num;$i++) {
            if (isset($clubs[0][$i])) {
                $en = $clubs[0][$i];
                $short = $en->club->short_name;
                $weekday = $en->weekday;
                $sec = $en->club_section();
                $time = str_replace(':', '', $sec->startTime);
                $time = substr($time, 0, 4);
                $map[$i][4] = "$short($weekday$time)";
            }
            if (isset($clubs[1][$i])) {
                $en = $clubs[1][$i];
                $short = $en->club->short_name;
                $weekday = $en->weekday;
                $sec = $en->club_section();
                $time = str_replace(':', '', $sec->startTime);
                $time = substr($time, 0, 4);
                $map[$i][5] = "$short($weekday$time)";
            }
            if (isset($clubs[2][$i])) {
                $en = $clubs[2][$i];
                $short = $en->club->short_name;
                $weekday = $en->weekday;
                $sec = $en->club_section();
                $time = str_replace(':', '', $sec->startTime);
                $time = substr($time, 0, 4);
                $map[$i][6] = "$short($weekday$time)";
            }
        }
        return $map;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1')->getStyle('A1:G1')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
        ]);
        $sheet->mergeCells('A3:A4')->getStyle('A3:A4')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('B3:B4')->getStyle('B3:B4')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('C3:C4')->getStyle('C3:C4')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('D3:D4')->getStyle('D3:D4')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
            'vertical'     => Alignment::VERTICAL_CENTER,
        ]);
        $sheet->mergeCells('E3:G3')->getStyle('E3:G3')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
        ]);
        $start = 5;
        for ($i=0;$i<count($this->rows);$i++) {
            $num = $this->rows[$i + 1];
            $sheet->mergeCells('A'.$start.':A'.$start + $num - 1)->getStyle('A'.$start.':A'.$start + $num - 1)->getAlignment()->applyFromArray([
                'horizontal'   => Alignment::HORIZONTAL_CENTER,
                'vertical'     => Alignment::VERTICAL_CENTER,
            ]);
            $sheet->mergeCells('B'.$start.':B'.$start + $num - 1)->getStyle('B'.$start.':B'.$start + $num - 1)->getAlignment()->applyFromArray([
                'horizontal'   => Alignment::HORIZONTAL_CENTER,
                'vertical'     => Alignment::VERTICAL_CENTER,
            ]);
            $sheet->mergeCells('C'.$start.':C'.$start + $num - 1)->getStyle('C'.$start.':C'.$start + $num - 1)->getAlignment()->applyFromArray([
                'horizontal'   => Alignment::HORIZONTAL_CENTER,
                'vertical'     => Alignment::VERTICAL_CENTER,
            ]);
            $sheet->mergeCells('D'.$start.':D'.$start + $num - 1)->getStyle('D'.$start.':D'.$start + $num - 1)->getAlignment()->applyFromArray([
                'horizontal'   => Alignment::HORIZONTAL_CENTER,
                'vertical'     => Alignment::VERTICAL_CENTER,
            ]);
            $start += $num;
        }
        $sheet->getStyle('A3:G'.$this->total)->getBorders()->applyFromArray([
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => [
                    'rgb' => '808080',
                ],
            ],
        ]);
    }

}