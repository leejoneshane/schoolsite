<?php

namespace App\Exports;

use App\Models\PublicClass;
use App\Models\Domain;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use ZipArchive;

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
        $filesName = [];
        $publics = PublicClass::byDomain($this->domain_id, $this->section);
        foreach ($publics as $p) {
            if ($p->eduplan) $filesName[] = public_path('public_class/' . $p->eduplan);
            if ($p->discuss) $filesName[] = public_path('public_class/' . $p->discuss);
        }

        /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        $zip = new ZipArchive();
        $content = [] ;
        $r = '';
        for ($i = 1;$i <  count($filesName);$i++){
        // Open the all document - 1
            $result = $zip->open($filesName[$i], ZipArchive::RDONLY);
            if ($result) {
                $content[$i] = $zip->getFromName('word/document.xml');
                $zip->close();
                // Extract the content of  document
                $p = strpos($content[$i], '<w:body');
                $p = strpos($content[$i], '>', $p);
                $content[$i] = substr($content[$i], $p+1);
                $p = strpos($content[$i], '</w:body>');
                $content[$i] = substr($content[$i], 0, $p);
                $r .= $content[$i];
            }
        }
        // Insert after first document
        $merge_file = public_path('public_class/' . $this->section . $domain->name . 'merge.docx');
        if (file_exists($merge_file)) unlink($merge_file);
        copy($filesName[0], $merge_file);

        $result = $zip->Open($merge_file);
        if ($result) {
            $content2 = $zip->getFromName('word/document.xml');
            $p = strpos($content2, '</w:body>');
            $content2 = substr_replace($content2, $r, $p, 0);
            $zip->addFromString('word/document.xml', $content2, ZipArchive::FL_OVERWRITE);
            $zip->close();
        }

        // Load temporarily create word file then Save it into PDF
        $pdfpath = public_path('public_class/' . $this->section . $domain->name . '.pdf');
        $Content = IOFactory::load($merge_file); 
        if (file_exists($pdfpath)) unlink($pdfpath);
        $PDFWriter = IOFactory::createWriter($Content,'PDF');
        $PDFWriter->save($pdfpath);
        unlink($merge_file);

        return $pdfpath;
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