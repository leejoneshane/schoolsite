<?php

namespace App\Exports;

use App\Models\Club;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\ClubTimePerGroupSheet;

class ClubTimeExport implements WithMultipleSheets
{
    use Exportable;

    public $club;

    public function __construct($club_id)
    {
        $this->club = Club::find($club_id);
    }

    public function sheets(): array
    {
        $sheets = [];
        if ($this->club->devide) {
            $groups = $this->club->section_groups();
            if (count($groups) <= 1) $groups = ['all'];
        } else {
            $groups = ['all'];
        }
        foreach ($groups as $g) {
            $sheets[] = new ClubTimePerGroupSheet($this->club, $g);
        }
        return $sheets;
    }

}