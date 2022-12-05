<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SyncFromTpedu;
use App\Jobs\SyncToAD;
use App\Jobs\SyncToGoogle;
use App\Models\Classroom;

class SyncController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function syncFromTpedu()
    {
        $classes = Classroom::all();
        return view('admin.tpedu', ['classes' => $classes]);
    }

    public function startSyncFromTpedu(Request $request)
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
        return view('admin')->with('success', '同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
    }

    public function syncToAD()
    {
        return view('admin.ad');
    }

    public function startSyncToAD(Request $request)
    {
        $password = ($request->input('password') == 'sync') ? true : false;
        $leave = $request->input('leave');
        SyncToAD::dispatch($password, $leave);
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
        return view('admin')->with('success', 'Google 同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
    }

}
