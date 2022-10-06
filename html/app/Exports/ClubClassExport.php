<?php

namespace App\Exports;

use App\Models\ClubEnroll;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ClubClassExport
{
    public $class_id;

    public function __construct($class_id)
    {
        $this->class_id = $class_id;
    }

    public function toFile($filename)
    {
//        unlink(public_path("$filename.docx"));
        $enrolls = ClubEnroll::currentByClass($this->class_id)->groupBy('uuid');
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(11);
        $section = $phpWord->addSection(['pageNumberingStart' => 1]);
        $section->addHeader()->addText(config('app.name') . ' - ' . $filename);
        $section->addFooter()->addPreserveText('第 {PAGE} 頁/共 {NUMPAGES} 頁', ['alignment' => 'center']);
        $section->addText($filename, ['bold' => true, 'color' => '3333FF', 'size' => 18], ['alignment' => 'center', 'lineHeight' => 1.5]);
        $table = $section->addTable(['borderSize' => 2, 'borderColor' => '999999', 'cellMargin' => 2]);
        $table->addRow('auto', ['bgColor' => 'cccccc', 'tblHeader' => true]);
        $table->addCell()->addText('座號', ['bold' => true]);
        $table->addCell()->addText('姓名', ['bold' => true]);
        $table->addCell()->addText('社團全名', ['bold' => true]);
        $table->addCell()->addText('上課時間', ['bold' => true]);
        $table->addCell()->addText('授課地點', ['bold' => true]);
        foreach ($enrolls as $student) {
            $span = $student->count();
            foreach ($student as $key => $enroll) {
                $table->addRow();
                if ($key == 0) {
                    $table->addCell('auto', ['vMerge' => 'restart', 'valign' => 'center'])->addText($enroll->student->seat);
                    $table->addCell('auto', ['vMerge' => 'restart', 'valign' => 'center'])->addText($enroll->student->realname);
                } else {
                    $table->addCell('auto', ['vMerge' => 'continue']);
                    $table->addCell('auto', ['vMerge' => 'continue']);
                }
                $table->addCell()->addText($enroll->club->name);
                $table->addCell()->addText($enroll->studytime);
                $table->addCell()->addText($enroll->club->location);
            }
        }
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save("$filename.docx");
    }

}