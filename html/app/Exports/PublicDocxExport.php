<?php

namespace App\Exports;

use App\Models\PublicClass;
use App\Models\Domain;
use DocxMerge\DocxMerge;

class PublicDocxExport
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
            if ($p->eduplan) {
                $extension = pathinfo($p->eduplan, PATHINFO_EXTENSION);
                if ($extension == 'docx') {
                    $filesName[] = public_path('public_class/' . $p->eduplan);
                }
            }
            if ($p->discuss) {
                $extension = pathinfo($p->discuss, PATHINFO_EXTENSION);
                if ($extension == 'docx') {
                    $filesName[] = public_path('public_class/' . $p->discuss);
                }
            }
        }

        $dm = new DocxMerge();
        $merge_file = public_path('public_class/' . $this->section . $domain->name . 'merge.docx');
        $dm->merge($filesName, $merge_file);
        return $merge_file;
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