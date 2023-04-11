<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\LunchPerClassSheet;
use App\Models\Classroom;
use App\Models\LunchSurvey;

class LunchExport implements WithMultipleSheets
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
        $classes = Classroom::all();
        foreach ($classes as $cls) {
            if (LunchSurvey::class_survey($cls->id, $this->section)->isEmpty()) continue;
            $sheets[] = new LunchPerClassSheet($this->section, $cls->id);
        }

        return $sheets;
    }

}