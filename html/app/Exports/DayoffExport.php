<?php

namespace App\Exports;

use App\Models\Dayoff;
use App\Models\Classroom;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

class DayoffExport
{
    public $dayoff_id;

    public function __construct($id)
    {
        $this->dayoff_id = $id;
    }

    public function export($filename, $type)
    {
        $dayoff = Dayoff::find($this->dayoff_id);
        $students = $dayoff->students()->orderBy('class_id')->orderBy('seat')->get();
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(14);
        $section = $phpWord->addSection(['orientation' => 'portrait', 'pageNumberingStart' => 1]);
        $header = $section->addHeader();
        $header->addText(config('app.name') . ' - 公假單', null, ['alignment' => 'center']);
        $unit = $dayoff->creater->mainunit;
        if ($unit) {
            $watermark = 'images/'.$unit->name.'圓戳章.jpg';
            $file_path = public_path($watermark);
            if (file_exists($file_path)) {
                $header->addWatermark($file_path, ['posHorizontal' => 'absolute', 'posVertical' => 'absolute', 'marginTop' => 300, 'marginLeft' => 150]);
            }
        }
        $section->addFooter()->addPreserveText('第 {PAGE} 頁，共 {NUMPAGES} 頁', null, ['alignment' => 'center']);
        $members = [];
        $old = $students->first()->class_id;
        $old_name = $students->first()->classroom->name;
        $first = true;
        foreach ($students as $stu) {
            if ($stu->class_id != $old) { //new page
                if ($first) {
                    $first = false;
                } else {
                    $section->addPageBreak();
                }
                $stu_list = implode('、', $members);
                $this->make_page($section, $dayoff, $old_name, $stu_list);
                $members = [];
                $members[] =  $stu->seat . $stu->realname;
                $old = $stu->class_id;
                $old_name = $stu->classroom->name;
            } else { //same page
                $members[] =  $stu->seat . $stu->realname;
            }
        }
        if (!empty($members)) {
            $section->addPageBreak();
            $stu_list = implode('、', $members);
            $this->make_page($section, $dayoff, $old_name, $stu_list);    
        }
        $objWriter = IOFactory::createWriter($phpWord, $type);
        $objWriter->save($filename);
        return public_path($filename);
    }

    public function make_page($section, $dayoff, $class_name, $stu_list) {
//        $textbox = $section->addTextBox(['align' => 'center', 'width' => 72, 'height' => 32, 'borderColor' => '999999', 'borderSize' => 1]); 
        $section->addText('公假單', ['underline' => 'single', 'bold' => true, 'size' => 18], ['alignment' => 'center', 'lineHeight' => 1.5]);
        $section->addTextBreak(1);
        $table = $section->addTable(['unit' => 'pct', 'width' => 100 * 50, 'borderSize' => 2, 'borderColor' => '999999', 'cellMargin' => 50]);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('班級', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($class_name, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('事由', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($dayoff->reason, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('時間', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($dayoff->datetime, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('地點', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($dayoff->location, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(Converter::cmToTwip(1.2), ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('學生名單', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($stu_list, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(Converter::cmToTwip(1.2), ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('學生簽名', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText(null, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('業務單位', ['bold' => true], ['alignment' => 'right']);
        if ($dayoff->creater) {
            $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
                ->addText($dayoff->creater->unit_name . $dayoff->creater->realname, ['bold' => false], ['alignment' => 'left']);
        } else {
            $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
                ->addText('管理員', ['bold' => false], ['alignment' => 'left']);
        }
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('備註', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($dayoff->memo, ['bold' => true], ['alignment' => 'left']);
        $section->addTextBreak(3);
        $section->addLine(['weight' => 1, 'width' => 460, 'height' => 0, 'dash' => 'dash', 'color' => '999999']);
        $section->addTextBreak(3);
//        $textbox = $section->addTextBox(['align' => 'center', 'width' => 72, 'height' => 32, 'borderColor' => '999999', 'borderSize' => 1]); 
        $section->addText('回　條', ['underline' => 'single', 'bold' => true, 'size' => 18], ['alignment' => 'center', 'lineHeight' => 1.5]);
        $section->addTextBreak(1);
        $table = $section->addTable(['unit' => 'pct', 'width' => 100 * 50, 'borderSize' => 2, 'borderColor' => '999999', 'cellMargin' => 50]);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('班級', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($class_name, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('事由', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($dayoff->reason, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('時間', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($dayoff->datetime, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(null, ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('地點', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($dayoff->location, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(Converter::cmToTwip(1.2), ['tblHeader' => false]);
        $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
            ->addText('學生名單', ['bold' => true], ['alignment' => 'right']);
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText($stu_list, ['bold' => false], ['alignment' => 'left']);
        $table->addRow(null, ['tblHeader' => false]);
        if ($dayoff->who) {
            $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
                ->addText('導師及科任老師簽名', ['bold' => true], ['alignment' => 'right']);
        } else {
            $table->addCell(Converter::cmToTwip(2.75), ['bgColor' => 'd0d0d0', 'valign' => 'center'])
                ->addText('導師簽名', ['bold' => true], ['alignment' => 'right']);
        }
        $table->addCell(Converter::cmToTwip(13.34), ['valign' => 'center'])
            ->addText(null, ['bold' => false], ['alignment' => 'left']);
        $section->addTextBreak(1);
        $section->addText('回條請於　　月　　日星期　　繳回業務單位', ['bold' => false, 'size' => 11], ['alignment' => 'right', 'lineHeight' => 1.5]);
        $section->addText('　　年　　月　　日發布', ['bold' => false, 'size' => 11], ['alignment' => 'right', 'lineHeight' => 1.5]);
    }

    public function download($filename, $type = null, $headers = [])
    {
        $dayoff = Dayoff::find($this->dayoff_id);
        if (!$type) {
            $type = 'Word2007';
        }
        switch ($type) {
            case 'Word2007':
                $filename = "$dayoff->reason.docx";
                break;
            case 'MsDoc':
                $filename = "$dayoff->reason.doc";
                break;
            case 'ODText':
                $filename = "$dayoff->reason.odt";
                break;
            case 'HTML':
                $filename = "$dayoff->reason.html";
                break;
        }
        return response()->download(
            $this->export($filename, $type),
            $filename,
            $headers
        )->deleteFileAfterSend(true);
    }

    public function view()
    {
        $dayoff = Dayoff::find($this->dayoff_id);
        $this->export("$dayoff->reason.docx", 'Word2007');
        $url = asset($dayoff->reason.'.docx');
        return redirect('https://view.officeapps.live.com/op/view.aspx?src='.$url);
    }

}