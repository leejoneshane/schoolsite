<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Seniority;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SeniorityImport implements ToCollection, WithStartRow
{
    use Importable;


    public function startRow(): int
    {
        return 3;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row[0])) continue;
            $uuid = $row[0];
            $job = $row[1];
            $name = $row[2];
            $row[3] = intval($row[3]);
            $row[4] = intval($row[4]);
            $row[6] = intval($row[6]);
            $row[7] = intval($row[7]);
            if ($uuid) {
                $teacher = Teacher::where('uuid', $uuid)->first();
            } elseif ($name && $job) {
                $teachers = Teacher::where('realname', 'like', '%'.$name.'%')->get();
                if ($teachers->isNotEmpty()) {
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
                }
                if (!$uuid) $uuid = $another;
            }
            Seniority::updateOrCreate([
                'uuid' => $uuid,
                'syear' => current_year(),
            ],[
                'school_year' => $row[3],
                'school_month' => $row[4] ?: 0,
                'school_score' => round(($row[3] * 12 + $row[4]) / 12 * 0.7, 2),
                'teach_year' => $row[6],
                'teach_month' => $row[7] ?: 0,
                'teach_score' => round(($row[6] * 12 + $row[7]) / 12 * 0.3, 2),
            ]);
        }
    }
}