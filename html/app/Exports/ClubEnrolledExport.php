<?php

namespace App\Exports;

use App\Models\Club;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

class ClubEnrolledExport
{
    public $club_id;
    public $section;

    public function __construct($club_id, $section)
    {
        $this->club_id = $club_id;
        $this->section = $section;
    }

    public function export($title, $filename, $type)
    {
        $club = Club::find($this->club_id);
        if ($club->devide) {
            $groups = $club->section_groups($this->section);
            if (count($groups) <= 1) $groups = ['all'];
        } else {
            $groups = ['all'];
        }
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(9);
        $section = $phpWord->addSection(['pageNumberingStart' => 1]);
        $section->addHeader()->addText(config('app.name') . ' - ' . $title, null, ['alignment' => 'center']);
        $section->addFooter()->addPreserveText('第 {PAGE} 頁/共 {NUMPAGES} 頁', null, ['alignment' => 'center']);
        $first = true;
        foreach ($groups as $n => $grp) {
            if ($grp == 'all') {
                $enrolls = $club->section_accepted($this->section)->sortBy(function ($en) {
                    return $en->student->stdno;
                });
            } else {
                $enrolls = $club->section_devide($grp, $this->section)->sortBy(function ($en) {
                    return $en->student->stdno;
                });
            }
            if ($first) {
                $first = false;
            } else {
                $section->addPageBreak();
            }
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
            $table->addCell()->addText($club->section($this->section)->teacher);
            $table->addCell()->addText($club->section($this->section)->location);
            $table->addCell()->addText($club->section($this->section)->studytime);
            $section->addTextBreak(1);
            $table = $section->addTable(['borderSize' => 2, 'borderColor' => '999999', 'cellMargin' => 50]);
            if ($club->section($this->section)->self_defined) {
                $counter = [0,0,0,0,0,0];
                foreach ($enrolls as $enroll) {
                    if (is_array($enroll->weekdays)) {
                        for ($i=1; $i<6; $i++) {
                            if (in_array($i, $enroll->weekdays)) {
                                $counter[$i]++;
                            }
                        }    
                    }
                }
                $table->addRow(null, ['tblHeader' => true]);
                $table->addCell(Converter::cmToTwip(1), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('編號', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('年班座號', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('學生姓名', ['bold' => true], ['alignment' => 'center']);
                if ($club->has_lunch) {
                    $table->addCell(Converter::cmToTwip(1), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                        ->addText('營養午餐', ['bold' => true], ['alignment' => 'center']);
                    $table->addCell(Converter::cmToTwip(1), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                        ->addText('豆奶', ['bold' => true], ['alignment' => 'center']);
                }
                $table->addCell(Converter::cmToTwip(1.25), ['gridSpan' => 5, 'bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('自選上課日', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('聯絡人', ['bold' => true, 'alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(3), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('聯絡信箱', ['bold' => true, 'alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('聯絡電話', ['bold' => true, 'alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['vMerge' => 'restart', 'bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('備註', ['bold' => true, 'alignment' => 'center']);
                $table->addRow(null, ['tblHeader' => true]);
                $table->addCell(null, ['vMerge' => 'continue']);
                $table->addCell(null, ['vMerge' => 'continue']);
                $table->addCell(null, ['vMerge' => 'continue']);
                if ($club->has_lunch) {
                    $table->addCell(null, ['vMerge' => 'continue']);
                }
                $table->addCell(Converter::cmToTwip(1.25), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('一', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(1.25), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('二', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(1.25), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('三', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(1.25), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('四', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(1.25), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('五', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(null, ['vMerge' => 'continue']);
                $table->addCell(null, ['vMerge' => 'continue']);
                $table->addCell(null, ['vMerge' => 'continue']);
                $table->addCell(null, ['vMerge' => 'continue']);
            } else {
                $table->addRow(null, ['tblHeader' => true]);
                $table->addCell(Converter::cmToTwip(1), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('編號', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('年班座號', ['bold' => true], ['alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('學生姓名', ['bold' => true], ['alignment' => 'center']);
                if ($club->has_lunch) {
                    $table->addCell(Converter::cmToTwip(1), ['bgColor' => 'cccccc', 'valign' => 'center'])
                        ->addText('營養午餐', ['bold' => true], ['alignment' => 'center']);
                    $table->addCell(Converter::cmToTwip(1), ['bgColor' => 'cccccc', 'valign' => 'center'])
                        ->addText('豆奶', ['bold' => true], ['alignment' => 'center']);
                }
                $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('聯絡人', ['bold' => true, 'alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(3), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('聯絡信箱', ['bold' => true, 'alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('聯絡電話', ['bold' => true, 'alignment' => 'center']);
                $table->addCell(Converter::cmToTwip(2), ['bgColor' => 'cccccc', 'valign' => 'center'])
                    ->addText('備註', ['bold' => true, 'alignment' => 'center']);
            }
            $no = 1;
            foreach ($enrolls as $key => $enroll) {
                $table->addRow();
                $table->addCell()->addText($no);
                $table->addCell()->addText($enroll->student->stdno);
                $table->addCell()->addText($enroll->student->realname);
                if ($club->has_lunch) {
                    $table->addCell()->addText($enroll->lunch);
                    if ($enroll->soymilk) {
                        $table->addCell()->addText('1');
                    } else {
                        $table->addCell();
                    }
                }
                if ($club->section($this->section)->self_defined) {
                    for ($i=1; $i<6; $i++) {
                        if (in_array($i, $enroll->weekdays)) {
                            $table->addCell()->addText('1');
                        } else {
                            $table->addCell();
                        }
                    }
                }
                $table->addCell()->addText($enroll->parent);
                $table->addCell()->addText($enroll->email);
                $table->addCell()->addText($enroll->mobile);
                $table->addCell()->addText(($enroll->identity > 0) ? $enroll->mark : '');
                $no++;
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