<?php

namespace App\Exports;

use App\Models\Club;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

class ClubTimeExport
{
    public $club_id;

    public function __construct($club_id)
    {
        $this->club_id = $club_id;
    }

    public function export($title, $filename, $type)
    {
        $club = Club::find($this->club_id);
        $enrolls = $club->section_accepted()->sortBy(function ($en) {
            return $en->student->stdno;
        });
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(11);
        $section = $phpWord->addSection(['orientation' => 'landscape', 'pageNumberingStart' => 1]);
        $section->addHeader()->addText(config('app.name') . ' - ' . $title, null, ['alignment' => 'center']);
        $section->addFooter()->addPreserveText('第 {PAGE} 頁/共 {NUMPAGES} 頁', null, ['alignment' => 'center']);
        $section->addText('課後照顧班與社團時間序列表', ['bold' => true, 'color' => '3333FF', 'size' => 18], ['alignment' => 'center', 'lineHeight' => 1.5]);
        $section->addTextBreak(1);
        $section->addText('社團全名：'.$club->name.'　　指導老師簽名：　　　　　　', ['bold' => true, 'size' => 14], ['alignment' => 'left', 'lineHeight' => 1]);
        $table = $section->addTable(['borderSize' => 2, 'borderColor' => '999999', 'cellMargin' => 50]);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(1), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('編號', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(1), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('年班', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(1), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('座號', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('姓名', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(null, ['gridSpan' => 3,'bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('課後與社團活動一覽表', ['bold' => true], ['alignment' => 'center']);
        foreach ($enrolls as $key => $enroll) {
            $table->addRow();
            $table->addCell()->addText($key + 1);
            $table->addCell()->addText($enroll->student->class_id);
            $table->addCell()->addText($enroll->student->seat);
            $table->addCell()->addText($enroll->student->realname);
            $next = $enroll->student->section_enrolls()->reject($enroll);
            $cells = array([],[],[]);
            foreach ($next as $en) {
                $sec = $en->club_section();
                if ($sec->startTime < '16:00:00') {
                    $cells[0][] = $en;
                } elseif ($sec->startTime < '17:00:00') {
                    $cells[1][] = $en;
                } else {
                    $cells[2][] = $en;
                }
            }
            foreach ($cells as $cell) {
                $col = $table->addCell();
                foreach ($cell as $en) {
                    $short = $en->club->short_name;
                    $weekday = $en->weekday;
                    $sec = $en->club_section();
                    $time = str_replace(':', '', $sec->startTime);
                    $time = substr($time, 0, 4);
                    $col->addText("$short($weekday$time)");
                }
            }
        }
        $objWriter = IOFactory::createWriter($phpWord, $type);
        $objWriter->save($filename);
        return public_path($filename);
    }

    public function download($title, $type = null, $headers = [])
    {
        if (!$type) {
            $type = 'Word2007';
        }
        switch ($type) {
            case 'Word2007':
                $filename = "$title.docx";
                break;
            case 'MsDoc':
                $filename = "$title.doc";
                break;
            case 'ODText':
                $filename = "$title.odt";
                break;
            case 'HTML':
                $filename = "$title.html";
                break;
        }
        return response()->download(
            $this->export($title, $filename, $type),
            $filename,
            $headers
        )->deleteFileAfterSend(true);
    }

}