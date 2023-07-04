<?php

namespace App\Exports\Sheets;

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
use Maatwebsite\Excel\Concerns\WithTitle;

class ClubRollPerGroupSheet implements FromCollection, WithHeadings, WithStyles, WithMapping, WithTitle
{
    use Exportable;

    public $club;
    public $devide;
    public $rows;

    public function __construct($club, $group)
    {
        $this->club = $club;
        $this->devide = $group;
    }

    public function collection()
    {
        if ($this->devide == 'all') {
            $enrolls = $this->club->section_accepted()->sortBy(function ($en) {
                return $en->student->stdno;
            });
        } else {
            $enrolls = $this->club->section_devide($this->devide)->sortBy(function ($en) {
                return $en->student->stdno;
            });
        }
        return $enrolls;
    }

    public function headings(): array
    {
        $header = [];
        $header[0] = [ '臺北市國語實驗國民小學學生社團【'.$this->club->name.'】點名表' ];
        $header[1] = [ '月份：　　　　指導老師簽名：　　　　　　　　' ];
        $header[2] = [ '學生\日期' ];
        $header[3] = [ '編號', '年班', '座號', '姓名' ];
        if ($this->club->has_lunch) {
            for ($i=0; $i<30; $i++) {
                $header[3][] = '出席';
                $header[3][] = '午餐';
            }
        } else {
            for ($i=0; $i<30; $i++) {
                $header[3][] = '出席';
            }
        }
        return $header;
    }

    public function prepareRows($rows)
    {
        $i = 1;
        foreach ($rows as $k => $r) {
            $rows[$k]->no = $i;
            $i++;
        }
        $this->rows = $i + 3;
        return $rows;
    }

    public function map($row): array
    {
        return [
            $row->no,
            $row->student->class_id,
            $row->student->seat,
            $row->student->realname,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        if ($this->club->has_lunch) {
            $cols = 60;
            $last = 'BL';
        } else {
            $cols = 30;
            $last = 'AH';
        }
        $sheet->getColumnDimension('A')->setWidth(3.17);
        $sheet->getColumnDimension('B')->setWidth(4.17);
        $sheet->getColumnDimension('C')->setWidth(3.17);
        $sheet->getColumnDimension('D')->setWidth(5.67)->setAutoSize(true);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->mergeCells('A1:'.$last.'1')->getStyle('A1:'.$last.'1')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_CENTER,
        ]);
        $sheet->mergeCells('A2:'.$last.'2')->getStyle('A2:'.$last.'2')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_LEFT,
        ]);
        $sheet->mergeCells('A3:D3')->getStyle('A3:D3')->getAlignment()->applyFromArray([
            'horizontal'   => Alignment::HORIZONTAL_RIGHT,
        ]);
        $sheet->getStyle('A3:'.$last.$this->rows)->getBorders()->applyFromArray([
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => [
                    'rgb' => '808080',
                ],
            ],
        ]);
        $c = $odd = ord('A');
        for ($i=0;$i<$cols;$i++) {
            if ($i < 22) {
                $char = chr($c + 4 + $i);
            } elseif ($i > 21 && $i < 48) {
                $char = 'A' . chr($c + $i - 22);
            } elseif ($i > 47) {
                $char = 'B' . chr($c + $i - 48);
            }
            if ($cols == 60) {
                if ($i % 2 == 1) {
                    $sheet->mergeCells($odd.'3:'.$char.'3');
                } else {
                    $odd = $char;
                }
            }
            $sheet->getColumnDimension($char)->setWidth(2.67);
            $sheet->getStyle($char . '4')->getNumberFormat()->applyFromArray([
                'formatCode' => NumberFormat::FORMAT_TEXT,
            ]);
            $sheet->getStyle($char . '4')->getAlignment()->applyFromArray([
                'horizontal'   => Alignment::HORIZONTAL_CENTER,
            ])->setWrapText(true);
        }
    }

    public function title(): string
    {
        if ($this->devide == 'all') {
            return '全部';
        } else {
            return '第' . $this->devide . '組';
        }
    }

}