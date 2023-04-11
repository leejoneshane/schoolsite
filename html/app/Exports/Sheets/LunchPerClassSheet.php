<?php

namespace App\Exports\Sheets;

use App\Models\Classroom;
use App\Models\LunchSurvey;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LunchPerClassSheet implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping, WithTitle
{

    public $section;
    public $class_id;

    public function __construct($section, $class_id)
    {
        $this->section = $section;
        $this->class_id = $class_id;
    }

    public function collection()
    {
        return LunchSurvey::class_survey($this->class_id, $this->section);
    }

    public function headings(): array
    {
        return [
            [
                '臺北市國語實驗國民小學'.substr($this->section, 0, -1).'學年度'.(substr($this->section, -1) == '1') ? '上' : '下'.'學期午餐調查結果彙整  '.date('Y-m-d').'匯出',
            ],[
                '班級', '座號', '姓名', '參加午餐', '', '乳品', '', '不參加午餐', '',
            ],[
                '', '', '', '葷食', '素食', '要飲用', '改成水果', '家長親送', '蒸飯設備',
            ],
        ];
    }

    public function map($row): array
    {
        $class_name = Classroom::find($row->class_id)->name;
        return [
            $class_name,
            $row->seat,
            $row->teacher->realname,
            ($row->by_school && !($row->vegen)) ? 1 : 0,
            ($row->by_school && $row->vegen) ? 1 : 0,
            ($row->by_school && $row->milk) ? 1 : 0,
            ($row->by_school && !($row->milk)) ? 1 : 0,
            ($row->by_parent) ? 1 : 0,
            ($row->boxed_meal) ? 1 : 0,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function title(): string
    {
        return $this->class_id;
    }

}