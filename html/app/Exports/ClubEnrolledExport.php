<?php

namespace App\Exports;

use App\Models\Club;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ClubEnrolledExport implements FromView, WithStyles, ShouldAutoSize
{
    use Exportable;

    public $club_id;

    public function __construct($club_id)
    {
        $this->club_id = $club_id;
    }

    public function view(): View
    {
        $club = Club::find($this->club_id);
        $enrolls = $club->year_accepted()->sortBy(function ($enroll) {
            return $enroll->student->seat;
        });
        return view('exports.clubenrolls', ['club' => $club, 'enrolls' => $enrolls]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'name' => '標楷體',
                    'size' => 24,
                    'bold' => true,
                ],
            ],
            3 => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'size' => 14,
                    'bold' => true,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => [ 'rgb' => '000000' ],
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => [ 'rgb' => '000000' ],
                    ],
                    'left' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => [ 'rgb' => '000000' ],
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => [ 'rgb' => '000000' ],
                    ],
                ],
            ],
        ];
    }

}