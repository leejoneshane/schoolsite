<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SeniorityImport implements ToCollection, WithStartRow
{
    use Importable;


    public function startRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows)
    {
        if (date('m') > 7) {
        	$year = date('Y') - 1911;
    	} else {
        	$year = date('Y') - 1912;
    	}
        foreach ($rows as $row) {
            $job = $row[1];
            $name = $row[2];
            $teachers = Teacher::where('realname', 'like', '%'.$name.'%')->get();
            if ($teachers->count() == 1) {
                $uuid = $teachers->first()->uuid;
            } else {
                $uuid = false;
                foreach ($teachers as $t) {
                    if (Classroom::find($t->turtor_class)->name == $job || $t->role_name == $job) {
                        $uuid = $t->uuid;
                        break;
                    } else {
                        $another = $t->uuid;
                    }
                }
            }
            if (!$uuid) $uuid = $another;
            DB::table('seniority')->updateOrInsert([
                'uuid' => $uuid,
            ],[
                'year' => $year,
                'school_year' => $row[3],
                'school_month' => $row[4] ?: 0,
                'school_score' => $row[5],
                'teach_year' => $row[6],
                'teach_month' => $row[7] ?: 0,
                'teach_score' => $row[8],
            ]);
        }
    }
}