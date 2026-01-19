<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\LunchCafeteria;
use App\Exports\Sheets\LunchLocationSheet;

class LunchLocationExport implements WithMultipleSheets
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
        // Get all cafeterias
        $cafeterias = LunchCafeteria::all();

        foreach ($cafeterias as $cafeteria) {
            // Exclude "隨班用餐" (In-class dining)
            if (strpos($cafeteria->description, '隨班用餐') !== false) {
                continue;
            }
            $sheets[] = new LunchLocationSheet($this->section, $cafeteria);
        }

        return $sheets;
    }
}
