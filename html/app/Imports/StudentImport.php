<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentImport implements ToCollection
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

        // 預設對應校務行政系統匯出學生名冊之標準欄位索引（72 欄格式）
        $colMap = [
            'class_name' => 0,      // 班級
            'class_id' => 1,        // 班級代碼
            'seat' => 2,            // 座號
            'id' => 3,              // 學號
            'idno' => 4,            // 身分證
            'realname' => 5,        // 姓名
            'gender' => 7,          // 性別
            'birthdate' => 9,       // 出生年月日
            'mobile' => 11,         // 學生行動電話
            'email' => 12,          // 電子信箱
            'status' => 13,         // 學籍狀態
            'reg_address' => 22,    // 戶籍地址
            'mail_address' => 26,   // 通訊地址
            'reg_tel_area' => 27,   // 學生戶籍電話區碼
            'reg_tel' => 28,        // 學生戶籍電話
            'mail_tel_area' => 29,  // 學生通訊電話區碼
            'mail_tel' => 30,       // 學生通訊電話
            'parent1_mobile' => 44, // 家長1行動電話
            'parent1_tel_area' => 45, // 家長1住家區碼
            'parent1_tel' => 46,    // 家長1住家電話
        ];

        $startIndex = 0;
        $firstRow = $rows->first();
        if ($firstRow) {
            $firstCells = array_map(fn($v) => trim((string) $v), $firstRow->toArray());
            if (in_array('學號', $firstCells) || in_array('身分證', $firstCells) || in_array('身分證字號', $firstCells) || in_array('姓名', $firstCells)) {
                $startIndex = 1;
                foreach ($firstCells as $idx => $header) {
                    switch ($header) {
                        case '班級':
                        case '班級名稱':
                            $colMap['class_name'] = $idx;
                            break;
                        case '班級代碼':
                            $colMap['class_id'] = $idx;
                            break;
                        case '座號':
                            $colMap['seat'] = $idx;
                            break;
                        case '學號':
                            $colMap['id'] = $idx;
                            break;
                        case '身分證':
                        case '身分證字號':
                            $colMap['idno'] = $idx;
                            break;
                        case '姓名':
                            $colMap['realname'] = $idx;
                            break;
                        case '性別':
                            $colMap['gender'] = $idx;
                            break;
                        case '出生年月日':
                        case '出生日期':
                        case '生日':
                            $colMap['birthdate'] = $idx;
                            break;
                        case '學生行動電話':
                        case '行動電話':
                        case '手機':
                            $colMap['mobile'] = $idx;
                            break;
                        case '電子信箱':
                        case '電子郵件':
                        case '信箱':
                        case 'Email':
                            $colMap['email'] = $idx;
                            break;
                        case '學籍狀態':
                            $colMap['status'] = $idx;
                            break;
                        case '戶籍地址':
                            $colMap['reg_address'] = $idx;
                            break;
                        case '通訊地址':
                            $colMap['mail_address'] = $idx;
                            break;
                        case '學生戶籍電話區碼':
                            $colMap['reg_tel_area'] = $idx;
                            break;
                        case '學生戶籍電話':
                            $colMap['reg_tel'] = $idx;
                            break;
                        case '學生通訊電話區碼':
                            $colMap['mail_tel_area'] = $idx;
                            break;
                        case '學生通訊電話':
                            $colMap['mail_tel'] = $idx;
                            break;
                        case '家長1行動電話':
                            $colMap['parent1_mobile'] = $idx;
                            break;
                        case '家長1住家區碼':
                            $colMap['parent1_tel_area'] = $idx;
                            break;
                        case '家長1住家電話':
                            $colMap['parent1_tel'] = $idx;
                            break;
                    }
                }
            }
        }

        $activeUuids = [];
        $importedClassIds = [];
        $importedCount = 0;

        foreach ($rows as $index => $row) {
            if ($index < $startIndex) {
                continue;
            }

            $className = trim((string) ($row[$colMap['class_name']] ?? ''));
            $classId = trim((string) ($row[$colMap['class_id']] ?? ''));
            $seatRaw = trim((string) ($row[$colMap['seat']] ?? ''));
            $stuId = trim((string) ($row[$colMap['id']] ?? ''));
            $idno = strtoupper(trim((string) ($row[$colMap['idno']] ?? '')));
            $realname = trim((string) ($row[$colMap['realname']] ?? ''));

            if (empty($idno) && empty($realname) && empty($stuId)) {
                continue;
            }

            if (empty($idno)) {
                $this->logs[] = '略過第 ' . ($index + 1) . " 列（{$realname}）：缺少身分證字號。";
                continue;
            }

            if (empty($stuId)) {
                $this->logs[] = '略過第 ' . ($index + 1) . " 列（{$realname} / {$idno}）：缺少學號。";
                continue;
            }

            if (empty($classId)) {
                $this->logs[] = '略過第 ' . ($index + 1) . " 列（{$realname} / {$idno}）：缺少班級代碼。";
                continue;
            }

            // 確保班級資料存在，若尚無此班級則自動建立
            if (!isset($importedClassIds[$classId])) {
                $classroom = Classroom::firstOrCreate(
                    ['id' => $classId],
                    [
                        'grade_id' => substr($classId, 0, 1),
                        'name' => $className !== '' ? $className : $classId,
                    ]
                );
                if ($classroom->wasRecentlyCreated) {
                    $this->logs[] = "自動建立班級：{$classroom->id} {$classroom->name}";
                }
                $importedClassIds[$classId] = true;
            }

            $seat = intval($seatRaw);

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

            // 手機：優先取學生行動電話，若空則備援填入家長1行動電話
            $mobile = $this->cleanPhone($row[$colMap['mobile']] ?? '');
            if ($mobile === null) {
                $mobile = $this->cleanPhone($row[$colMap['parent1_mobile']] ?? '');
            }

            // 住家電話：依序取學生通訊電話、學生戶籍電話，若皆空則備援填入家長1住家電話
            $telephone = $this->combinePhone($row[$colMap['mail_tel_area']] ?? '', $row[$colMap['mail_tel']] ?? '')
                ?? $this->combinePhone($row[$colMap['reg_tel_area']] ?? '', $row[$colMap['reg_tel']] ?? '')
                ?? $this->combinePhone($row[$colMap['parent1_tel_area']] ?? '', $row[$colMap['parent1_tel']] ?? '');

            // 地址：優先取通訊地址，若空則取戶籍地址，不串接郵遞區號並將全形數字轉為半形
            $mailAddress = trim((string) ($row[$colMap['mail_address']] ?? ''));
            $regAddress = trim((string) ($row[$colMap['reg_address']] ?? ''));
            $address = $mailAddress !== '' ? $mailAddress : $regAddress;
            $address = $address !== '' ? $this->toHalfWidthDigits($address) : null;

            $email = preg_replace('/\s+/', '', trim((string) ($row[$colMap['email']] ?? '')));
            if ($email === '') {
                $email = null;
            }

            $status = trim((string) ($row[$colMap['status']] ?? '在學'));

            // 以身分證字號尋找現有學生（含軟刪除）
            $stu = Student::withTrashed()->where('idno', $idno)->first();
            $isNew = false;
            if (!$stu) {
                $conflictById = Student::withTrashed()->where('id', $stuId)->first();
                if ($conflictById) {
                    $this->logs[] = '略過第 ' . ($index + 1) . " 列（{$realname}）：學號 {$stuId} 已被其他學生（{$conflictById->idno} {$conflictById->realname}）使用。";
                    continue;
                }
                $isNew = true;
                $stu = new Student();
                $stu->uuid = (string) Str::orderedUuid();
                $stu->idno = $idno;
            } else {
                $conflictById = Student::withTrashed()
                    ->where('id', $stuId)
                    ->where('uuid', '!=', $stu->uuid)
                    ->first();
                if ($conflictById) {
                    $this->logs[] = '略過第 ' . ($index + 1) . " 列（{$realname}）：學號 {$stuId} 已被其他學生（{$conflictById->idno} {$conflictById->realname}）使用。";
                    continue;
                }
                $stu->idno = $idno;
            }

            $stu->id = $stuId;
            $stu->account = $this->resolveAccount($stu, $stuId, $idno);

            if (!empty($stu->sn) && !empty($stu->gn) && ($stu->sn . $stu->gn === $realname)) {
                // 保留原有姓氏與名字切分
            } else {
                [$sn, $gn] = $this->splitName($realname);
                $stu->sn = $sn;
                $stu->gn = $gn;
            }
            $stu->realname = $realname;
            $stu->class_id = $classId;
            $stu->seat = $seat;
            $stu->gender = $gender;
            if ($birthdate) {
                $stu->birthdate = $birthdate;
            }
            if (!is_null($mobile)) {
                $stu->mobile = $mobile;
            }
            if (!is_null($telephone)) {
                $stu->telephone = $telephone;
            }
            if (!is_null($address)) {
                $stu->address = $address;
            }
            if (!is_null($email)) {
                $stu->email = $email;
            }

            $stu->save();

            if ($status === '在學' || $status === '') {
                if ($stu->trashed()) {
                    $stu->restore();
                }
                $activeUuids[] = $stu->uuid;
                $this->syncUserAccount($stu);
                $importedCount++;
                $actionLabel = $isNew ? '新增' : '更新';
                $this->logs[] = "{$actionLabel}學生：{$stu->class_id} {$stu->seat}號 {$stu->realname} ({$stu->id})";
            } else {
                User::where('uuid', $stu->uuid)->delete();
                if (!$stu->trashed()) {
                    $stu->delete();
                }
                $this->logs[] = "標註非在學學生：{$stu->class_id} {$stu->realname} ({$stu->id}) [{$status}]";
            }
        }

        // 若勾選移除不在名冊內的轉出帳號，僅限定於本次匯入資料所包含的班級範圍內
        if ($this->remove_leave && count($activeUuids) > 0 && count($importedClassIds) > 0) {
            $leaves = Student::whereIn('class_id', array_keys($importedClassIds))
                ->whereNotIn('uuid', $activeUuids)
                ->get();
            foreach ($leaves as $l) {
                User::where('uuid', $l->uuid)->delete();
                $l->delete();
                $this->logs[] = "移除班級 {$l->class_id} 未在名冊內之不在籍學生：{$l->id} {$l->realname} ({$l->idno})";
            }
        }

        $classScope = count($importedClassIds) === 1
            ? '班級 ' . array_key_first($importedClassIds)
            : count($importedClassIds) . ' 個班級';
        array_unshift($this->logs, "班級學生資料表匯入完成（範圍：{$classScope}），共處理 {$importedCount} 筆在學學生資料。");
    }

    protected function resolveAccount(Student $stu, string $stuId, string $idno): string
    {
        if (!empty($stu->account)) {
            $exists = Student::withTrashed()
                ->where('account', $stu->account)
                ->where('uuid', '!=', $stu->uuid)
                ->exists();
            if (!$exists) {
                return $stu->account;
            }
        }

        $candidates = [];
        if (!empty($stuId)) {
            $candidates[] = 'meps' . strtolower($stuId);
        }
        $candidates[] = strtolower($idno);

        foreach ($candidates as $candidate) {
            $exists = Student::withTrashed()
                ->where('account', $candidate)
                ->where('uuid', '!=', $stu->uuid)
                ->exists();
            if (!$exists) {
                return $candidate;
            }
        }

        return strtolower($idno) . '_' . Str::lower(Str::random(4));
    }

    protected function syncUserAccount(Student $stu): void
    {
        $pwd = substr($stu->idno, -6);
        $userEmail = $stu->email ?: ($stu->id ? ('meps' . strtolower($stu->id) . '@tc.meps.tp.edu.tw') : ($stu->account . '@tc.meps.tp.edu.tw'));

        $sysUser = User::where('uuid', $stu->uuid)->first();
        if (!$sysUser) {
            $byEmail = User::where('email', $userEmail)->where('user_type', 'Student')->first();
            if ($byEmail && !Student::where('uuid', $byEmail->uuid)->exists()) {
                $sysUser = $byEmail;
                $sysUser->uuid = $stu->uuid;
            }
        }

        if ($sysUser) {
            $sysUser->name = $stu->account;
            if (!User::where('email', $userEmail)->where('id', '!=', $sysUser->id)->exists()) {
                $sysUser->email = $userEmail;
            }
            if ($this->reset_password) {
                $sysUser->password = Hash::make($pwd);
                $sysUser->setRememberToken(Str::random(60));
            }
            $sysUser->save();
        } else {
            if (User::where('email', $userEmail)->exists()) {
                $userEmail = 'meps' . strtolower($stu->id ?: $stu->account) . '@tc.meps.tp.edu.tw';
                if (User::where('email', $userEmail)->exists()) {
                    $userEmail = strtolower($stu->idno) . '@tc.meps.tp.edu.tw';
                }
            }
            $sysUser = new User([
                'uuid' => $stu->uuid,
                'user_type' => 'Student',
                'name' => $stu->account,
                'email' => $userEmail,
                'is_admin' => false,
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

    protected function cleanPhone($value): ?string
    {
        $str = trim((string) $value);
        if ($str === '' || $str === '0') {
            return null;
        }
        return $this->toHalfWidthDigits($str);
    }

    protected function combinePhone($areaValue, $telValue): ?string
    {
        $tel = $this->cleanPhone($telValue);
        if ($tel === null) {
            return null;
        }
        $area = $this->cleanPhone($areaValue);
        if ($area !== null && !str_starts_with($tel, $area) && !str_starts_with($tel, '0')) {
            return $area . '-' . $tel;
        }
        return $tel;
    }

    protected function toHalfWidthDigits(string $text): string
    {
        return strtr($text, [
            '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
        ]);
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $str = trim((string) $value);

        // 優先解析 8 碼西元純數字（如 20191004），避免被 is_numeric 誤判為 Excel 日期序號
        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $str, $m)) {
            $y = intval($m[1]);
            $mo = intval($m[2]);
            $d = intval($m[3]);
            if (checkdate($mo, $d, $y)) {
                return sprintf('%04d-%02d-%02d', $y, $mo, $d);
            }
        }

        // 解析 6~7 碼民國年純數字（如 1081004）
        if (preg_match('/^(\d{2,3})(\d{2})(\d{2})$/', $str, $m)) {
            $y = intval($m[1]) + 1911;
            $mo = intval($m[2]);
            $d = intval($m[3]);
            if (checkdate($mo, $d, $y)) {
                return sprintf('%04d-%02d-%02d', $y, $mo, $d);
            }
        }

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

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $ts = strtotime($str);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        return null;
    }
}
