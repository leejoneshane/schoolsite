<?php

namespace App\Exports;

use App\Models\PublicClass;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PublicExcelExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping
{
    use Exportable;

    public $section;
    public $fields;

    public function __construct($section)
    {
        $this->section = $section;
        $this->fields = [
            [ 'id' => 'id', 'name' => '編號' ],
            [ 'id' => 'domain', 'name' => '教學領域' ],
            [ 'id' => 'teach_unit', 'name' => '單元名稱' ],
            [ 'id' => 'teach_grade', 'name' => '教學對象' ],
            [ 'id' => 'teach_class', 'name' => '授課班級' ],
            [ 'id' => 'reserved_at', 'name' => '上課時間' ],
            [ 'id' => 'week_session', 'name' => '週節次' ],
            [ 'id' => 'location', 'name' => '上課地點' ],
            [ 'id' => 'teacher', 'name' => '授課教師' ],
            [ 'id' => 'eduplan', 'name' => '教案已上傳' ],
            [ 'id' => 'discuss', 'name' => '會談記錄已上傳' ],
            [ 'id' => 'teachers', 'name' => '觀課夥伴' ],
        ];
    }

    public function collection()
    {
        return PublicClass::bySection($this->section);
    }

    public function headings(): array
    {
        $fields = [];
        foreach ($this->fields as $f) {
            $fields[] = $f['name'];
        }
        return $fields;
    }

    public function map($row): array
    {
        $rowdata = [];
        foreach ($this->fields as $f) {
            if ($f['id'] == 'domain') {
                $rowdata[] = $row->domain->name;
            } elseif ($f['id'] == 'teach_grade') {
                $rowdata[] = $row->classroom->grade->name;
            } elseif ($f['id'] == 'teach_class') {
                $rowdata[] = $row->classroom->name;
            } elseif ($f['id'] == 'reserved_at') {
                $rowdata[] = $row->reserved_at->format('Y-m-d');
            } elseif ($f['id'] == 'teacher') {
                $rowdata[] = $row->teacher->realname;
            } elseif ($f['id'] == 'eduplan') {
                $rowdata[] = is_null($row->eduplan) ? 0 : 1;
            } elseif ($f['id'] == 'discuss') {
                $rowdata[] = is_null($row->discuss) ? 0 : 1;
            } elseif ($f['id'] == 'teachers') {
                $data = '';
                if ($row->teachers()) {
                    foreach ($row->teachers() as $t) {
                        $data .= $t->realname . '、';
                    }
                    $rowdata[] = substr($data, 0, -1);
                } else {
                    $rowdata[] = '';
                }
            } else {
                $rowdata[] = $row->{$f['id']};
            }
        };
        return $rowdata;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_NUMBER,
            'L' => NumberFormat::FORMAT_TEXT,
        ];
    }

}