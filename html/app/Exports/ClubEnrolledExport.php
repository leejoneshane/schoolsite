<?php

namespace App\Exports;

use App\Models\Club;
use App\Models\ClubEnroll;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

class ClubEnrolledExport
{
    public $club_id;

    public function __construct($club_id)
    {
        $this->club_id = $club_id;
    }

    public function export($title, $filename, $type)
    {
        $club = Club::find($this->club_id);
        $enrolls = $club->year_accepted()->sortBy(function ($en) {
            return $en->student->stdno;
        });
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(11);
        $section = $phpWord->addSection(['pageNumberingStart' => 1]);
        $section->addHeader()->addText(config('app.name') . ' - ' . $title, null, ['alignment' => 'center']);
        $section->addFooter()->addPreserveText('第 {PAGE} 頁/共 {NUMPAGES} 頁', null, ['alignment' => 'center']);
        $section->addText($title, ['bold' => true, 'color' => '3333FF', 'size' => 18], ['alignment' => 'center', 'lineHeight' => 1.5]);
        $table = $section->addTable(['borderSize' => 2, 'borderColor' => '999999', 'cellMargin' => 50]);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.1), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('社團全名', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(1.15), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('分類', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(1.75), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('負責單位', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('招生年級', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(1.75), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('指導老師', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(1.75), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('授課地點', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(5.4), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('上課時間', ['bold' => true], ['alignment' => 'center']);
        $table->addRow();
        $table->addCell()->addText($club->name);
        $table->addCell()->addText($club->kind->name);
        $table->addCell()->addText($club->unit->name);
        $table->addCell()->addText($club->grade);
        $table->addCell()->addText($club->teacher);
        $table->addCell()->addText($club->location);
        $table->addCell()->addText($club->studytime);
        $section->addTextBreak(1);
        $table = $section->addTable(['borderSize' => 2, 'borderColor' => '999999', 'cellMargin' => 50]);
        $table->addRow(null, ['tblHeader' => true]);
        $table->addCell(Converter::cmToTwip(1.25), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('編號', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('年班座號', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('學生姓名', ['bold' => true], ['alignment' => 'center']);
        if ($club->has_lunch) {
            $table->addCell(Converter::cmToTwip(1.25), ['bgColor' => 'cccccc', 'valign' => 'center'])
                ->addText('營養午餐', ['bold' => true], ['alignment' => 'center']);
        }
        if ($club->self_defined) {
            $table->addCell(Converter::cmToTwip(3), ['bgColor' => 'cccccc', 'valign' => 'center'])
                ->addText('自選上課日', ['bold' => true], ['alignment' => 'center']);
        }
        $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('聯絡人', ['bold' => true, 'alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(3), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('聯絡信箱', ['bold' => true, 'alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(3), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('聯絡電話', ['bold' => true, 'alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('備註', ['bold' => true, 'alignment' => 'center']);
        foreach ($enrolls as $key => $enroll) {
            $table->addRow();
            $table->addCell()->addText($key + 1);
            $table->addCell()->addText($enroll->student->stdno);
            $table->addCell()->addText($enroll->student->realname);
            if ($club->has_lunch) {
                $table->addCell()->addText($enroll->lunch);
            }
            if ($club->self_defined) {
                $table->addCell()->addText($enroll->weekday);
            }
            $table->addCell()->addText($enroll->parent);
            $table->addCell()->addText($enroll->email);
            $table->addCell()->addText($enroll->mobile);
            $table->addCell()->addText(($enroll->identity > 0) ? $enroll->mark : '');
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