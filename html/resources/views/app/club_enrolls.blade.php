@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    報名資訊管理
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $club->kind_id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    @if ($current == $section || $current = prev_section($section))
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.appendenroll', ['club_id' => $club->id, 'section' => $section]) }}">
        <i class="fa-solid fa-circle-plus"></i>新增報名資訊
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.fastappend', ['club_id' => $club->id, 'section' => $section]) }}">
        <i class="fa-solid fa-truck-fast"></i>快速輸入
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.importold', ['club_id' => $club->id, 'section' => $section]) }}">
        <i class="fa-solid fa-file-import"></i>匯入舊生
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.notify', ['club_id' => $club->id, 'section' => $section]) }}">
        <i class="fa-regular fa-envelope"></i>寄送錄取通知
    </a>
    <div class="inline text-sm py-2 pl-6 rounded"><i class="fa-solid fa-download"></i>下載
        <a class="text-blue-300 hover:text-blue-600" href="{{ route('clubs.exportenrolled', ['club_id' => $club->id, 'section' => $section]) }}">
            錄取名冊
        </a>、
        <a class="text-blue-300 hover:text-blue-600" href="{{ route('clubs.exporttimeseq', ['club_id' => $club->id, 'section' => $section]) }}">
            時間序列表
        </a>、
        <a class="text-blue-300 hover:text-blue-600" href="{{ route('clubs.exportroll', ['club_id' => $club->id, 'section' => $section]) }}">
            點名表
        </a>
    </div>
    <button class="text-sm py-2 pl-6 rounded text-red-300 hover:text-red-600" onclick="
        var result = confirm('確定要清空所有報名記錄嗎？');
        if (result) {
            const myform = document.getElementById('remove');
            myform.action = '{{ route('clubs.prune', [ 'club_id' => $club->id, 'section' => $section ]) }}';
            myform.submit();
        }
    ">
        <i class="fa-solid fa-file-import"></i>清空報名紀錄
    </button>
    @endif
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            學生社團全名
        </th>
        <th scope="col" class="p-2">
            招生年級
        </th>
        <th scope="col" class="p-2">
            指導教師
        </th>
        <th scope="col" class="p-2">
            上課時間
        </th>
        <th scope="col" class="p-2">
            授課地點
        </th>
        <th scope="col" class="p-2">
            費用
        </th>
        <th scope="col" class="p-2">
            招生人數
        </th>
        <th scope="col" class="p-2">
            已報名
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 {{ $club->style }}">
        <td class="p-2">{{ $club->name }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        <td class="p-2">{{ $club->section($section)->teacher }}</td>
        <td class="p-2">{{ $club->section($section)->studytime }}</td>
        <td class="p-2">{{ $club->section($section)->location }}</td>
        <td class="p-2">{{ $club->section($section)->cash }}</td>
        <td class="p-2">{{ $club->section($section)->total }}</td>
        <td class="p-2">{{ $club->count_enrolls($section) }}</td>
    </tr>
</table>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mt-5" role="alert">
    <p>
        注意事項：<br>
        　　1. 本社團{{ ($club->kind->manual_auditing) ? '採用人工審核，請透過管理介面進行錄取作業！' : '將會由系統依照報名順序自動錄取！' }}<br>
        　　2. 錄取作業不會自動郵寄通知，待錄取作業完成後，請透過上方連結統一寄發錄取通知。<br>
        @if ($club->devide)
        　　3. 學生分組後，在下載各式表單時會自動依照組別分頁！<br>
        @endif
    </p>
</div>
<div class="p-3">
    <label for="sections">請選擇學期：</label>
    <select id="sections" class="inline w-48 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
        onchange="
        var section = this.value;
        window.location.replace('{{ route('clubs.enrolls', ['club_id' => $club->id]) }}' + '/' + section);
        ">
        @foreach ($club->sections as $s)
        <option value="{{ $s->section }}"{{ ($s->section == $section) ? ' selected' : '' }}>{{ substr($s->section, 0, -1) }}學年{{ (substr($s->section, -1) == 1) ? '上學期' : '下學期' }}</option>
        @endforeach
    </select>
@if ($club->devide)
    @if (count($groups) > 1)
    <label for="groups">請選擇組別：</label>
    <select id="groups" class="inline w-48 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
        onchange="
        var mygroup = this.value;
        window.location.replace('{{ route('clubs.enrolls', ['club_id' => $club->id, 'section' => $section]) }}?order={{ $order }}&group=' + mygroup);
        ">
        <option value="all"{{ ($group == 'all') ? ' selected' : '' }}>全部</option>
        @foreach ($groups as $g)
        <option value="{{ $g }}"{{ ($g == $group) ? ' selected' : '' }}>{{ $g }}</option>
        @endforeach
    </select>
    <a class="text-red-300 hover:text-red-600" href="{{ route('clubs.devide', [ 'club_id' => $club->id, 'section' => $section ]) }}">重新分組</a>
    @else
    <a class="text-red-300 hover:text-red-600" href="{{ route('clubs.devide', [ 'club_id' => $club->id, 'section' => $section ]) }}">請點擊這裡，進行學生分組！</a>
    @endif
@endif
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            排序
        </th>
        <th scope="col" class="p-2">
            年班座號
            <a class="text-sm py-2 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.enrolls', ['club_id' => $club->id, 'section' => $section]) }}?order=stdno{{ ($club->devide) ? '&group='.$group : '' }}">
                <i class="fa-solid fa-arrow-down-short-wide"></i>
            </a>
        </th>
        <th scope="col" class="p-2">
            學生姓名
        </th>
        @if ($club->section($section)->self_defined)
        <th scope="col" class="p-2">
            自選上課日
        </th>
        @endif
        <th scope="col" class="p-2">
            聯絡人
        </th>
        <th scope="col" class="p-2">
            聯絡信箱
        </th>
        <th scope="col" class="p-2">
            聯絡電話
        </th>
        <th scope="col" class="p-2">
            報名時間
            <a class="text-sm py-2 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.enrolls', ['club_id' => $club->id, 'section' => $section]) }}?order=created_at{{ ($club->devide) ? '&group='.$group : '' }}">
                <i class="fa-solid fa-arrow-down-short-wide"></i>
            </a>
        </th>
        <th scope="col" class="p-2">
            身份註記
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @php
        $no = 1;
    @endphp
    @forelse ($enrolls as $enroll)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            <span>{{ $no++ }}</span>
        </td>
        <td class="p-2">
            <span>{{ $enroll->student->stdno }}</span>
        </td>
        <td class="p-2">
            <span>{{ $enroll->student->realname }}{{ $enroll->student->trashed() ? '（已轉學）' : '' }}</span>
        </td>
        @if ($club->section($section)->self_defined)
        <td class="p-2">
            <span>{{ $enroll->weekday }}</span>
        </td>
        @endif
        <td class="p-2">
            <span>{{ $enroll->parent }}</span>
        </td>
        <td class="p-2">
            <span>{{ $enroll->email }}</span>
        </td>
        <td class="p-2">
            <span>{{ $enroll->mobile }}</span>
        </td>
        <td class="p-2">
            <span>{{ $enroll->created_at }}</span>
        </td>
        <td class="p-2">
            <span>{{ $enroll->mark }}</span>
        </td>
        <td class="p-2 text-lg">
        @if ($current == $section || $current = prev_section($section))
        @if ($enroll->accepted)
            <button class="py-2 pr-6 text-fuchsia-300 hover:text-fuchsia-600" title="除名"
                onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('clubs.deny', ['enroll_id' => $enroll->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-person-circle-xmark"></i>
            </button>
        @else
            <button class="py-2 pr-6 text-blue-300 hover:text-blue-600" title="錄取"
                onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('clubs.valid', ['enroll_id' => $enroll->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-person-circle-check"></i>
            </button>
        @endif
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
            onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('clubs.delenroll', ['enroll_id' => $enroll->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
        @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-xl font-bold">目前還沒有人報名！</td>
    </tr>
    @endforelse
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
