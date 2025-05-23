@extends('layouts.admin')

@section('content')
<div class="flex text-2xl font-bold leading-normal pb-5">
    瀏覽歷程
    <label for="period" class="py-2 pl-6 text-sm">
        備份
    </label>
    <select id="period" class="text-sm text-gray-500 border border-gray-200 dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700">
        <option value="0">上學期</option>
        <option value="1">去年</option>
    </select>
    <label for="period" class="py-2 pr-6 text-sm">
        以前的紀錄並從資料庫移除
    </label>
    <button type="submit" class="text-sm rounded text-blue-300 hover:text-blue-600" onclick="
        const myform = document.getElementById('remove');
        var period = document.getElementById('period').value;
        myform.action = '{{ route('watchdog.export') }}?period=' + period;
        myform.submit();
    ">
        <i class="fa-solid fa-file-export"></i>開始備份
    </button>
</div>
<label for="date" class="inline p-2">日期：</label>
<input type="date" id="date" class="rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    value="{{ $date->format('Y-m-d') }}" onchange="
        var mydate = this.value;
        window.location.replace('{{ route('watchdog') }}?date=' + mydate);
">
<label for="ip" class="inline p-2">用戶IP：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="ip" value="{{ $ip }}">
<label for="user" class="inline p-2">教師帳號：</label>
<select id="user" name="user" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
    <option></option>
    @foreach ($teachers as $t)
    @php
        $gap = '';
        $rname = '';
        if ($t->role_name) $rname = $t->role_name;
        for ($i=0;$i<6-mb_strlen($rname);$i++) {
            $gap .= '　';
        }
        $display = $t->role_name . $gap . $t->realname;
    @endphp
    <option value="{{ $t->uuid }}"{{ ($t->uuid == $uuid) ? ' selected' : '' }}>{{ $display }}</option>
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
        if (!$user) {
            $role = '尚未登入';
        } elseif ($user->user_type == 'Teacher') {
            $role = employee($user->uuid)->role_name;
        } elseif ($user->user_type == 'Student') {
            if (employee($user->uuid)) {
                $role = employee($user->uuid)->classroom->name;
            } else {
                $role = '不在籍學生';
            }
        } else {
            $role = '本地帳號';
        }
        @endphp
        <td class="p-2">{{ $role }}</td>
        <td class="p-2">{{ ($user && employee($user->uuid)) ? employee($user->uuid)->realname : ''}}</td>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td colspan="8" class="border-b">{!! nl2br($log->action) !!}</td>
    </tr>
    @endforeach
</table>
{{ $logs->links('pagination::tailwind') }}
<form class="hidden" id="remove" method="POST" action="{{ route('watchdog.export') }}">
    @csrf
</form>
@endsection
