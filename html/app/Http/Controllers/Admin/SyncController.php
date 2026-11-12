<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SyncFromTpedu;
use App\Jobs\SyncToAD;
use App\Jobs\SyncToGoogle;
use App\Imports\ExcelDecryptor;
use App\Imports\StudentImport;
use App\Imports\TeacherImport;
use App\Models\Classroom;
use App\Models\Watchdog;
use Maatwebsite\Excel\Excel as ExcelFormat;

class SyncController extends Controller
{

    public function syncFromLDAP()
    {
        $classes = Classroom::all();
        return view('admin.tpedu', ['classes' => $classes]);
    }

    public function startSyncFromLDAP(Request $request)
    {
        $expire = $request->boolean('expire');
        $password = ($request->input('password') == 'sync') ? true : false;
        $unit = $request->boolean('sync_units');
        $classroom = $request->boolean('sync_classes');
        $subject = $request->boolean('sync_subjects');
        $teacher = $request->boolean('sync_teachers');
        $student = $request->boolean('sync_students');
        $target = !empty($request->input('target')) ? $request->input('target') : false;
        $remove = ($request->input('leave') == 'remove') ? true : false;
        SyncFromTpedu::dispatch($expire, $password, $unit, $classroom, $subject, $teacher, $student, $target, $remove);
        Watchdog::watch($request, '開始進行單一身份驗證同步！' . json_encode([
            'expire' => $expire,
            'password' => $password,
            'unit' => $unit,
            'classroom' => $classroom,
            'subject' => $subject,
            'teacher' => $teacher,
            'student' => $student,
            'target' => $target,
            'remove' => $remove,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return view('admin')->with('success', '同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
    }

    public function importFromSMS()
    {
        return view('admin.importer');
    }

    public function startImportFromSMS(Request $request)
    {
        if (!$request->hasFile('file')) {
            return redirect()->route('import')->with('error', '請選擇要上傳的 Excel 檔案！');
        }

        $type = $request->input('type', 'teachers');
        $password = ($request->input('password') == 'sync');
        $remove = ($request->input('leave') == 'remove');
        $excelPassword = $request->input('excel_password');
        $file = $request->file('file');
        $tempDecryptedPath = null;

        try {
            $tempDecryptedPath = ExcelDecryptor::decryptXlsxIfNeeded(
                $file->getRealPath(),
                $excelPassword,
                (string) $file->getClientOriginalExtension()
            );

            $importSource = $tempDecryptedPath ?? $file;
            $readerType = $tempDecryptedPath ? ExcelFormat::XLSX : null;

            if ($type === 'teachers') {
                $importer = new TeacherImport($password, $remove);
                $importer->import($importSource, null, $readerType);
                Watchdog::watch($request, '匯入校務行政全校教師資料表：' . $file->getClientOriginalName());
                return redirect()->route('import')
                    ->with('success', '全校教師資料表已經匯入完成！')
                    ->with('import_logs', $importer->logs);
            }

            if ($type === 'students') {
                $importer = new StudentImport($password, $remove);
                $importer->import($importSource, null, $readerType);
                Watchdog::watch($request, '匯入校務行政班級學生資料表：' . $file->getClientOriginalName());
                return redirect()->route('import')
                    ->with('success', '班級學生資料表已經匯入完成！')
                    ->with('import_logs', $importer->logs);
            }

            return redirect()->route('import')->with('error', '不支援的資料表匯入類型！');
        } catch (\RuntimeException $e) {
            return redirect()->route('import')->with('error', $e->getMessage());
        } finally {
            if ($tempDecryptedPath && is_file($tempDecryptedPath)) {
                @unlink($tempDecryptedPath);
            }
        }
    }

    public function syncToMSAD()
    {
        return view('admin.ad');
    }

    public function startSyncToMSAD(Request $request)
    {
        $password = ($request->input('password') == 'sync') ? true : false;
        $leave = $request->input('leave');
        SyncToAD::dispatch($password, $leave);
        Watchdog::watch($request, '開始進行 AD 同步！' . json_encode([
            'password' => $password,
            'leave' => $leave,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return view('admin')->with('success', 'AD 同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
    }

    public function syncToGsuite()
    {
        $classes = Classroom::all();
        return view('admin.gsuite', ['classes' => $classes]);
    }

    public function startSyncToGsuite(Request $request)
    {
        $password = ($request->input('password') == 'sync') ? true : false;
        $leave = $request->input('leave');
        $target = false;
        if ($leave == 'onduty') $target = $request->input('target1');
        if ($leave != 'onduty') $target = $request->input('target2');
        SyncToGoogle::dispatch($password, $leave, $target);
        Watchdog::watch($request, '開始進行 Google 同步！' . json_encode([
            'password' => $password,
            'leave' => $leave,
            'target' => $target,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return view('admin')->with('success', 'Google 同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
    }

}
