@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">瀏覽歷程</div>
<label for="date" class="inline p-2">日期：</label>
<input type="date" id="date" class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    value="{{ $date->format('Y-m-d') }}" onchange="
        var mydate = this.value;
        window.location.replace('{{ route('watchdog') }}' + '?date=' + mydate);
">
<label for="idno" class="inline p-2">用戶IP：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="ip" value="{{ $ip }}">
<label for="user" class="inline p-2">教師帳號：</label>
<select id="user" name="user" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
    <option></option>
    @foreach ($teachers as $teacher)
    @php
        $gap = '';
        for ($i=0;$i<6-mb_strlen($teacher->role_name);$i++) {
                $gap .= '　';
            }
        $display = $teacher->role_name . $gap . $teacher->realname;
    @endphp
    <option value="{{ $teacher->uuid }}"{{ ($teacher->uuid == $uuid) ? ' selected' : '' }}>{{ $display }}</option>
    @endforeach
</select>
<label for="stdid" class="inline p-2">學生帳號，學號：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="stdid" value="{{ $stdid }}">
<label for="stdno" class="inline p-2">班級座號：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="stdno" value="{{ $stdno }}">
<i class="fa-solid fa-magnifying-glass" onclick="
    var search = '';
    var ip = document.getElementById('ip').value;
    if (ip) {
        search = search + 'ip=' + ip + '&';
    }
    var uuid = document.getElementById('user').value;
    if (uuid) {
        search = search + 'uuid=' + uuid + '&';
    }
    var stdid = document.getElementById('stdid').value;
    if (stdid) {
        search = search + 'stdid=' + stdid + '&';
    }
    var stdno = document.getElementById('stdno').value;
    if (stdno) {
        search = search + 'stdno=' + stdno + '&';
    }
    search = search.slice(0, -1);
    if (search) {
        window.location.replace('{{ route('watchdog') }}' + '?' + search);
    } else {
        var mydate = document.getElementById('date').value;
        window.location.replace('{{ route('watchdog') }}' + '?date=' + mydate);
    }
"></i>
<table class="w-full table-fixed py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            時間
        </th>
        <th scope="col" class="p-2">
            IP
        </th>
        <th scope="col" class="p-2">
            裝置
        </th>
        <th scope="col" class="p-2">
            平台
        </th>
        <th scope="col" class="p-2">
            瀏覽器
        </th>
        <th scope="col" class="p-2">
            機器人
        </th>
        <th scope="col" class="p-2">
            使用者身份
        </th>
        <th scope="col" class="p-2">
            使用者姓名
        </th>
    </tr>
    @foreach ($logs as $log)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $log->created_at }}</td>
        <td class="p-2">{{ $log->ip }}</td>
        <td class="p-2">{{ $log->device }}</td>
        <td class="p-2">{{ $log->platform }}</td>
        <td class="p-2">{{ $log->browser }}</td>
        <td class="p-2">{{ $log->robot }}</td>
        @php
        $user = $log->who;
        if ($user->user_type == 'Teacher') {
            $role = $user->profile->role_name;
        } elseif ($user->user_type == 'Student') {
            $role = $user->profile->classroom->name;
        } else {
            $role = '本地帳號';
        }
        @endphp
        <td class="p-2">{{ $role }}</td>
        <td class="p-2">{{ $user->profile->realname }}</td>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td colspan="8" class="border-b">{!! nl2br($log->action) !!}</td>
    </tr>
    @endforeach
</table>
{{ $logs->links('pagination::tailwind') }}
@endsection
