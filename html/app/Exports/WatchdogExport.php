<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Watchdog;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class WatchdogExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping, WithEvents
{
    use Exportable;

    public $period;

    public function __construct($last_date)
    {
        $this->period = $last_date;
    }

    public function collection()
    {
        return Watchdog::whereRaw('DATE(created_at) <= ?', $this->period)->get();
    }

    public function headings(): array
    {
        return [
            '時間',
            '人員',
            '身份',
            '姓名',
            'IP',
            '裝置',
            '平台',
            '瀏覽器',
            '機器人',
            '網址',
            '動作',
        ];
    }

    public function map($row): array
    {
        $user = $row->who;
        if ($user->user_type == 'Teacher') {
            $role = employee($user->uuid)->role_name;
        } elseif ($user->user_type == 'Student') {
            $role = employee($user->uuid)->classroom->name;
        } else {
            $role = '本地帳號';
        }
        $time = ExcelDate::dateTimeToExcel($row->created_at);
        return [
            $time,
            $row->uuid,
            $role,
            $row->who->realname,
            $row->ip,
            $row->device,
            $row->platform,
            $row->browser,
            $row->robot,
            $row->url,
            $row->action,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DATETIME,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterSheet::class => function(AfterSheet $event) {
                $last = $event->getConcernable()->period;
                Watchdog::whereRaw('DATE(created_at) <= ?', $last)->delete();
            },
        ];
    }

}