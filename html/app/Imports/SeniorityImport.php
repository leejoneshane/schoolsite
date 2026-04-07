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

    public $logs = [];

    public function startRow(): int
    {
        return 3;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if (empty($row[0]) && empty($row[2])) continue;
            $uuid = $row[0];
            $job = $row[1];
            $name = $row[2];
            $row[3] = intval($row[3]);
            $row[4] = intval($row[4]);
            $row[6] = intval($row[6]);
            $row[7] = intval($row[7]);
            $another = null;
            if ($uuid) {
                $teacher = Teacher::where('uuid', $uuid)->first();
                if ($teacher) {
                    $this->logs[] = "找到：{$name} (UUID: " . substr($uuid, 0, 8) . "...)";
                } else {
                    $this->logs[] = "警告：Excel 提供的 UUID ({$uuid}) 找不到教職員 {$name}";
                    // 嘗試用姓名找
                    $uuid = false;
                }
            }

            if (!$uuid && $name && $job) {
                $teachers = Teacher::where('realname', 'like', '%' . $name . '%')->get();
                if ($teachers->isNotEmpty()) {
                    if ($teachers->count() == 1) {
                        $uuid = $teachers->first()->uuid;
                        $this->logs[] = "對應成功：{$name} (由姓名自動對應)";
                    } else {
                        foreach ($teachers as $t) {
                            // 修正原先程式碼 typo: turtor_class -> tutor_class
                            $t_job = Classroom::find($t->tutor_class)?->name ?? $t->role_name;
                            if ($t_job == $job) {
                                $uuid = $t->uuid;
                                break;
                            } else {
                                $another = $t->uuid;
                            }
                        }
                        if ($uuid) {
                            $this->logs[] = "對應成功：{$name} (由姓名及職稱 {$job} 自動對應)";
                        } elseif ($another) {
                            $uuid = $another;
                            $this->logs[] = "模糊對應：{$name} (姓名重複，選取其中之一)";
                        }
                    }
                }
            }

            if (!$uuid) {
                $this->logs[] = "錯誤：找不到相關教職員 {$name}，跳過 row: " . ($index + 3);
                continue;
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