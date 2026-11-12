<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\Teacher;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class TeacherImport implements ToCollection
{
    use Importable;

    public $reset_password = false;
    public $remove_leave = true;
    public $logs = [];

    public function __construct($reset_password = false, $remove_leave = true)
    {
        $this->reset_password = $reset_password;
        $this->remove_leave = $remove_leave;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            $this->logs[] = '錯誤：上傳的檔案沒有任何資料列！';
            return;
        }

        // 建立欄位名稱對應索引（預設依標準欄位順序）
        $colMap = [
            'id' => 0,          // 員工代碼
            'realname' => 1,    // 姓名
            'gender' => 2,      // 性別
            'idno' => 3,        // 身分證
            'birthdate' => 4,   // 出生日期
            'in_quota' => 5,    // 編制內否
            'edu_level' => 6,   // 教育程度
            'telephone' => 7,   // 辦公室電話
            'mobile' => 8,      // 行動電話
            'email' => 9,       // 電子郵件
            'arrive_date' => 10,// 到職日期
            'status' => 11,     // 離職否
            'leave_date' => 12, // 離職日期
            'hire_type' => 13,  // 聘任方式
            'acc_type' => 14,   // 帳號類型
        ];

        $startIndex = 0;
        $firstRow = $rows->first();
        if ($firstRow) {
            $firstCells = array_map(fn($v) => trim((string) $v), $firstRow->toArray());
            if (in_array('員工代碼', $firstCells) || in_array('身分證', $firstCells) || in_array('姓名', $firstCells)) {
                $startIndex = 1;
                foreach ($firstCells as $idx => $header) {
                    switch ($header) {
                        case '員工代碼':
                            $colMap['id'] = $idx;
                            break;
                        case '姓名':
                            $colMap['realname'] = $idx;
                            break;
                        case '性別':
                            $colMap['gender'] = $idx;
                            break;
                        case '身分證':
                        case '身分證字號':
                            $colMap['idno'] = $idx;
                            break;
                        case '出生日期':
                        case '生日':
                            $colMap['birthdate'] = $idx;
                            break;
                        case '編制內否':
                            $colMap['in_quota'] = $idx;
                            break;
                        case '教育程度':
                            $colMap['edu_level'] = $idx;
                            break;
                        case '辦公室電話':
                        case '聯絡電話':
                        case '電話':
                            $colMap['telephone'] = $idx;
                            break;
                        case '行動電話':
                        case '手機':
                            $colMap['mobile'] = $idx;
                            break;
                        case '電子郵件':
                        case '信箱':
                        case 'Email':
                            $colMap['email'] = $idx;
                            break;
                        case '到職日期':
                            $colMap['arrive_date'] = $idx;
                            break;
                        case '離職否':
                        case '在職狀態':
                            $colMap['status'] = $idx;
                            break;
                        case '離職日期':
                            $colMap['leave_date'] = $idx;
                            break;
                        case '聘任方式':
                            $colMap['hire_type'] = $idx;
                            break;
                        case '帳號類型':
                            $colMap['acc_type'] = $idx;
                            break;
                    }
                }
            }
        }

        // 預先查找預設的級任、科任職務與單位，供新進無職務教師預設掛載
        $tutorRole = Role::where('role_no', 'C05')->orWhere('name', 'like', '%級任%')->first();
        $subjectRole = Role::where('role_no', 'C06')->orWhere('name', 'like', '%科任%')->first();

        $activeUuids = [];
        $importedCount = 0;
        $year = current_year();

        foreach ($rows as $index => $row) {
            if ($index < $startIndex) {
                continue;
            }

            $empId = trim((string) ($row[$colMap['id']] ?? ''));
            $realname = trim((string) ($row[$colMap['realname']] ?? ''));
            $idno = strtoupper(trim((string) ($row[$colMap['idno']] ?? '')));

            if (empty($idno) && empty($realname)) {
                continue;
            }

            if (empty($idno)) {
                $this->logs[] = "略過第 " . ($index + 1) . " 列（{$realname}）：缺少身分證字號。";
                continue;
            }

            $genderStr = trim((string) ($row[$colMap['gender']] ?? ''));
            $gender = 0;
            if ($genderStr === '男' || $genderStr === '1') {
                $gender = 1;
            } elseif ($genderStr === '女' || $genderStr === '2') {
                $gender = 2;
            } elseif (!empty($genderStr)) {
                $gender = 9;
            }

            $birthdate = $this->parseDate($row[$colMap['birthdate']] ?? null);
            $telephone = trim((string) ($row[$colMap['telephone']] ?? ''));
            if ($telephone === '0' || $telephone === '') {
                $telephone = null;
            }
            $mobile = trim((string) ($row[$colMap['mobile']] ?? ''));
            if ($mobile === '0' || $mobile === '') {
                $mobile = null;
            }
            $email = preg_replace('/\s+/', '', trim((string) ($row[$colMap['email']] ?? '')));
            if ($email === '') {
                $email = null;
            }

            $status = trim((string) ($row[$colMap['status']] ?? '在職'));
            $hireType = trim((string) ($row[$colMap['hire_type']] ?? ''));
            $accType = trim((string) ($row[$colMap['acc_type']] ?? ''));

            // 以身分證字號或員工代碼尋找現有教師（含軟刪除）
            $emp = Teacher::withTrashed()->where('idno', $idno)->first();

            $isNew = false;
            if (!$emp) {
                $isNew = true;
                $emp = new Teacher();
                $emp->uuid = (string) Str::orderedUuid();
                $emp->idno = $idno;
            } else {
                $emp->idno = $idno;
            }

            if (!empty($empId)) {
                $emp->id = $empId;
            }

            // 處理帳號 (account 欄位為 unique not null)
            $emp->account = $this->resolveAccount($emp, $email, $empId, $idno);

            // 處理姓氏與名字
            if (!empty($emp->sn) && !empty($emp->gn) && ($emp->sn . $emp->gn === $realname)) {
                // 保留原有姓氏與名字切分
            } else {
                [$sn, $gn] = $this->splitName($realname);
                $emp->sn = $sn;
                $emp->gn = $gn;
            }
            $emp->realname = $realname;
            $emp->gender = $gender;
            if ($birthdate) {
                $emp->birthdate = $birthdate;
            }
            if (!is_null($telephone)) {
                $emp->telephone = $telephone;
            }
            if (!is_null($mobile)) {
                $emp->mobile = $mobile;
            }
            if (!is_null($email)) {
                $emp->email = $email;
            }

            // 若該教職員尚未設定單位與職務，依導師班級或科任預設掛載，確保教職員列表可正常查詢
            if (empty($emp->unit_id) || empty($emp->role_id)) {
                $defaultRole = !empty($emp->tutor_class) ? ($tutorRole ?? $subjectRole) : ($subjectRole ?? $tutorRole);
                if ($defaultRole) {
                    $defaultUnit = Unit::find($defaultRole->unit_id);
                    $emp->unit_id = $defaultRole->unit_id;
                    $emp->unit_name = $defaultUnit ? $defaultUnit->name : '';
                    $emp->role_id = $defaultRole->id;
                    $emp->role_name = $defaultRole->name;
                }
            }

            $emp->save();

            // 同步更新 job_title 關聯表
            if (!empty($emp->unit_id) && !empty($emp->role_id)) {
                DB::table('job_title')->insertOrIgnore([
                    'year' => $year,
                    'uuid' => $emp->uuid,
                    'unit_id' => $emp->unit_id,
                    'role_id' => $emp->role_id,
                ]);
            }

            // 在職與離職處理及 User 登入帳號同步
            if ($status === '在職' || $status === '') {
                if ($emp->trashed()) {
                    $emp->restore();
                }
                $activeUuids[] = $emp->uuid;
                $this->syncUserAccount($emp);
                $importedCount++;
                $actionLabel = $isNew ? '新增' : '更新';
                $this->logs[] = "{$actionLabel}教職員：{$emp->id} {$emp->realname} ({$emp->idno})";
            } else {
                User::where('uuid', $emp->uuid)->delete();
                if (!$emp->trashed()) {
                    $emp->delete();
                }
                $this->logs[] = "標註離職教職員：{$emp->id} {$emp->realname} ({$emp->idno})";
            }
        }

        // 若勾選移除不在名冊內的已離職帳號
        if ($this->remove_leave && count($activeUuids) > 0) {
            $leaves = Teacher::whereNotIn('uuid', $activeUuids)->get();
            foreach ($leaves as $l) {
                User::where('uuid', $l->uuid)->delete();
                $l->delete();
                $this->logs[] = "移除未在名冊內之離職教職員：{$l->idno} {$l->realname}";
            }
        }

        array_unshift($this->logs, "全校教師資料表匯入完成，共處理 {$importedCount} 筆在職教職員資料。");
    }

    protected function resolveAccount(Teacher $emp, ?string $email, string $empId, string $idno): string
    {
        $emailPrefix = null;
        if (!empty($email) && strpos($email, '@') !== false) {
            [$prefix, $domain] = explode('@', $email, 2);
            $prefix = strtolower(trim($prefix));
            if (!empty($prefix)) {
                $emailPrefix = $prefix;
            }
        }

        // 若已有帳號且非系統預設代號，優先保留；若原為 meps 代號且現在有學校信箱則更新
        if (!empty($emp->account)) {
            if ($emailPrefix && str_starts_with($emp->account, 'meps') && !Teacher::withTrashed()->where('account', $emailPrefix)->where('uuid', '!=', $emp->uuid)->exists()) {
                return $emailPrefix;
            }
            return $emp->account;
        }

        $candidates = [];
        if ($emailPrefix) {
            $candidates[] = $emailPrefix;
        }
        if (!empty($empId)) {
            $candidates[] = 'meps' . strtolower($empId);
        }
        $candidates[] = strtolower($idno);

        foreach ($candidates as $candidate) {
            $exists = Teacher::withTrashed()
                ->where('account', $candidate)
                ->where('uuid', '!=', $emp->uuid)
                ->exists();
            if (!$exists) {
                return $candidate;
            }
        }

        return strtolower($idno) . '_' . Str::lower(Str::random(4));
    }

    protected function syncUserAccount(Teacher $emp): void
    {
        $pwd = substr($emp->idno, -6);
        $userEmail = $emp->email ?: ($emp->id ? ('meps' . strtolower($emp->id) . '@tc.meps.tp.edu.tw') : ($emp->account . '@tc.meps.tp.edu.tw'));

        $isAdmin = false;
        if (!empty($emp->character)) {
            $characters = explode(',', $emp->character);
            if (in_array('TPECadmin1', $characters)) {
                $isAdmin = true;
            }
        }

        $sysUser = User::where('uuid', $emp->uuid)->first();
        if (!$sysUser) {
            $byEmail = User::where('email', $userEmail)->where('user_type', 'Teacher')->first();
            if ($byEmail && !Teacher::where('uuid', $byEmail->uuid)->exists()) {
                $sysUser = $byEmail;
                $sysUser->uuid = $emp->uuid;
            }
        }

        if ($sysUser) {
            $sysUser->name = $emp->account;
            if (!User::where('email', $userEmail)->where('id', '!=', $sysUser->id)->exists()) {
                $sysUser->email = $userEmail;
            }
            if ($isAdmin) {
                $sysUser->is_admin = true;
            }
            if ($this->reset_password) {
                $sysUser->password = Hash::make($pwd);
                $sysUser->setRememberToken(Str::random(60));
            }
            $sysUser->save();
        } else {
            if (User::where('email', $userEmail)->exists()) {
                $userEmail = 'meps' . strtolower($emp->id ?: $emp->account) . '@tc.meps.tp.edu.tw';
                if (User::where('email', $userEmail)->exists()) {
                    $userEmail = strtolower($emp->idno) . '@tc.meps.tp.edu.tw';
                }
            }
            $sysUser = new User([
                'uuid' => $emp->uuid,
                'user_type' => 'Teacher',
                'name' => $emp->account,
                'email' => $userEmail,
                'is_admin' => $isAdmin,
            ]);
            $sysUser->password = Hash::make($pwd);
            $sysUser->save();
        }
    }

    protected function splitName(string $name): array
    {
        $len = mb_strlen($name, 'UTF-8');
        if ($len > 3) {
            return [mb_substr($name, 0, 2, 'UTF-8'), mb_substr($name, 2, null, 'UTF-8')];
        }
        return [mb_substr($name, 0, 1, 'UTF-8'), mb_substr($name, 1, null, 'UTF-8')];
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        $str = trim((string) $value);
        $parts = preg_split('/[\/\-\.]/', $str);
        if (count($parts) === 3) {
            $y = intval($parts[0]);
            $m = intval($parts[1]);
            $d = intval($parts[2]);
            if ($y < 1911) {
                $y += 1911;
            }
            if (checkdate($m, $d, $y)) {
                return sprintf('%04d-%02d-%02d', $y, $m, $d);
            }
        }
        $ts = strtotime($str);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }
        return null;
    }
}
