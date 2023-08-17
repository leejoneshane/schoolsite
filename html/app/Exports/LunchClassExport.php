<?php

namespace App\Exports;

use App\Models\LunchSurvey;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class LunchClassExport implements FromView,  WithColumnFormatting, WithTitle
{
    use Exportable;

    public $section;
    public $class_id;

    public function __construct($section, $class_id)
    {
        $this->section = $section;
        $this->class_id = $class_id;
    }

    public function view(): View
    {
        $surveys = LunchSurvey::class_survey($this->class_id, $this->section);
        return view('components.lunch_survey_sheet', [ 'surveys' => $surveys ]);
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