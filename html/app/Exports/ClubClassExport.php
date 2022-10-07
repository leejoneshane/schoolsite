<?php

namespace App\Exports;

use App\Models\ClubEnroll;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

class ClubClassExport
{
    public $class_id;

    public function __construct($class_id)
    {
        $this->class_id = $class_id;
    }

    public function export($title, $filename, $type)
    {
        $enrolls = ClubEnroll::currentByClass($this->class_id)->groupBy('uuid');
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(11);
        $section = $phpWord->addSection(['pageNumberingStart' => 1]);
        $section->addHeader()->addText(config('app.name') . ' - ' . $title, null, ['alignment' => 'center']);
        $section->addFooter()->addPreserveText('第 {PAGE} 頁/共 {NUMPAGES} 頁', null, ['alignment' => 'center']);
        $section->addText($title, ['bold' => true, 'color' => '3333FF', 'size' => 18], ['alignment' => 'center', 'lineHeight' => 1.5]);
        $table = $section->addTable(['borderSize' => 2, 'borderColor' => '999999', 'cellMargin' => 50]);
        $table->addRow(null, ['tblHeader' => true]);
        $table->addCell(Converter::cmToTwip(1.25), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('座號', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('姓名', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(4), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('社團全名', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(null, ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('上課時間', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(3), ['bgColor' => 'cccccc', 'valign' => 'center'])
            ->addText('授課地點', ['bold' => true, 'alignment' => 'center']);
        foreach ($enrolls as $student) {
            $span = $student->count();
            foreach ($student as $key => $enroll) {
                $table->addRow();
                if ($key == 0) {
                    $table->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])
                        ->addText($enroll->student->seat, null, ['alignment' => 'center']);
                    $table->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])
                        ->addText($enroll->student->realname, null, ['alignment' => 'center']);
                } else {
                    $table->addCell(null, ['vMerge' => 'continue']);
                    $table->addCell(null, ['vMerge' => 'continue']);
                }
                $table->addCell()->addText($enroll->club->name);
                $table->addCell()->addText($enroll->studytime);
                $table->addCell()->addText($enroll->club->location);
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