<?php

namespace App\Exports;

use App\Models\PublicClass;
use App\Models\Domain;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

class PublicExport
{
    public $section;
    public $domain_id;

    public function __construct($section, $domain_id)
    {
        $this->section = $section;
        $this->domain_id = $domain_id;
    }

    public function export()
    {
        $domain = Domain::find($this->domain_id);
        $pdfpath = public_path('public_class/' . $domain->id . '.pdf');
        $filesName = [];
        $publics = PublicClass::byDomain($this->domain_id, $this->section);
        foreach ($publics as $p) {
            $filesName[] = public_path('public_class/' . $p->eduplan);
            $filesName[] = public_path('public_class/' . $p->discuss);
        }

        /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        include_once(base_path('vendor/seblucas/tbszip/tbszip.php'));
        $zip = new clsTbsZip();
        $content = [] ;
        $r = '';
        for ($i = 1;$i <  count($filesName);$i++){
        // Open the all document - 1
            $zip->Open($filesName[$i]);
            $content[$i] = $zip->FileRead('word/document.xml');
            $zip->Close();
            // Extract the content of  document
            $p = strpos($content[$i], '<w:body');
            $p = strpos($content[$i], '>', $p);
            $content[$i] = substr($content[$i], $p+1);
            $p = strpos($content[$i], '</w:body>');
            $content[$i] = substr($content[$i], 0, $p);
            $r .= $content[$i]  ;
        }
        // Insert after first document
        $zip->Open($filesName[0]);
        $content2 = $zip->FileRead('word/document.xml');
        $p = strpos($content2, '</w:body>');
        $content2 = substr_replace($content2, $r, $p, 0);
        $zip->FileReplace('word/document.xml', $content2, TBSZIP_STRING);
        $zip->Flush(TBSZIP_FILE, 'merge.docx');

        // Load temporarily create word file
        $sourceDocx = public_path('public_class/' . 'merge.docx');
        $Content = IOFactory::load($sourceDocx); 

        //Save it into PDF
        $savePdfPath = public_path('public_class/' . $this->domain_id . 'merge.pdf');
        if (file_exists($savePdfPath)) unlink($savePdfPath);
        $PDFWriter = IOFactory::createWriter($Content,'PDF');
        $PDFWriter->save($savePdfPath);

        unlink($sourceDocx);
        return public_path($savePdfPath);
    }

    public function download($filename,$headers = [])
    {
        return response()->download(
            $this->export(),
            $filename,
            $headers
        )->deleteFileAfterSend(true);
    }

}