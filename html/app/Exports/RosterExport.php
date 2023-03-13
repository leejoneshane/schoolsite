<?php

namespace App\Exports;

use App\Models\Roster;
use App\Models\Classroom;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RosterExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping
{
    use Exportable;

    public Roster $roster;
    public $section;
    public $fields;

    public function __construct($id, $section)
    {
        $this->roster = Roster::find($id);
        $this->section = $section;
        $fields = [
            [ 'id' => 'class_id', 'name' => '班級' ],
            [ 'id' => 'seat', 'name' => '座號' ],
            [ 'id' => 'realname', 'name' => '姓名' ],
        ];
        if ($this->roster->fields) {
            foreach ($this->roster->fields as $f1) {
                foreach (Roster::FIELDS as $f2) {
                    if ($f1 == $f2['id']) {
                        $fields[] = $f2;
                    }
                }
            }
        }
        $this->fields = $fields;
    }

    public function collection()
    {
        return $this->roster->section_students($this->section);
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
        $class_name = Classroom::find($row->class_id)->name;
        foreach ($this->fields as $f) {
            if ($f['id'] == 'class_id') {
                $rowdata[] = $class_name;
            } else {
                $rowdata[] = $row->{$f['id']};
            }
        };
        return $rowdata;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
        ];
    }

}