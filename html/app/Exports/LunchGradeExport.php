<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\LunchGradeSheet;

class LunchGradeExport implements WithMultipleSheets
{
    use Exportable;

    public $section;

    public function __construct($section)
    {
        $this->section = $section;
    }

    public function sheets(): array
    {
        $sheets = [];
        for ($grade = 1; $grade <= 6; $grade++) {
            $sheets[] = new LunchGradeSheet($this->section, $grade);
        }
        return $sheets;
    }
}
