@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    社團一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.add', ['kid' => $kind->id]) }}">
        <i class="fa-solid fa-circle-plus"></i>新增課外社團
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.import', ['kid' => $kind->id]) }}">
        <i class="fa-solid fa-file-import"></i>批次匯入
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.export', ['kid' => $kind->id]) }}">
        <i class="fa-solid fa-file-export"></i>匯出成Excel
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.repeat', ['kid' => $kind->id]) }}">
        <i class="fa-solid fa-check-double"></i>重複報名清冊
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.classroom', ['kid' => $kind->id]) }}">
        <i class="fa-solid fa-address-book"></i>各班錄取名冊
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        請從下方選單挑選要瀏覽的社團類別。<br>
        點擊社團名稱可以管理社團報名資訊，點擊圖示「筆」可以編輯，點擊圖示「垃圾桶」刪除社團，點擊圖示「信封」寄信給報名的家長，點擊圖示「資源回收」將刪除本學年度所有報名資訊，以便重新報名。<br>
    </p>
</div>
<label for="kinds">請選擇社團類別：</label>
<select id="kinds" class="inline w-48 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
    onchange="
    var kid = this.value;
    window.location.replace('{{ route('clubs.admin') }}' + '/' + kid);
    ">
    @foreach ($kinds as $k)
    <option value="{{ $k->id }}" {{ ($kind->id == $k->id) ? 'selected' : '' }}>{{ $k->name }}</option>
    @endforeach
</select>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            營隊全名
        </th>
        <th scope="col" class="p-2">
            招生年級
        </th>
        <th scope="col" class="p-2">
            指導老師
        </th>
        <th scope="col" class="p-2">
            上課時段
        </th>
        <th scope="col" class="p-2">
            授課地點
        </th>
        <th scope="col" class="p-2">
            招生人數
        </th>
        <th scope="col" class="p-2">
            報名限制
        </th>
        <th scope="col" class="p-2">
            已報名
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($clubs as $club)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 {{ $kind->style }}">
        <td class="p-2">{{ $club->name }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        @if ($club->section())
        <td class="p-2">{{ $club->section()->teacher }}</td>
        <td class="p-2">{{ $club->section()->studytime }}</td>
        <td class="p-2">{{ $club->section()->location }}</td>
        <td class="p-2">{{ $club->section()->total }}</td>
        <td class="p-2">{{ $club->section()->maximum }}</td>
        <td class="p-2">{{ $club->count_enrolls() }}</td>
        @else
        <td colspan="6" class="p-2">本學期未開班</td>
        @endif
        <td class="p-2">
            @if (Auth::user()->is_admin ||  Auth::user()->profile->mainunit->id == $club->unit_id)
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('clubs.sections', ['club_id' => $club->id]) }}" title="時程管理">
                <i class="fa-regular fa-clock"></i>
            </a>
            <a class="py-2 pr-6 text-green-300 hover:text-green-600"
                href="{{ route('clubs.enrolls', ['club_id' => $club->id]) }}" title="學生管理">
                <i class="fa-solid fa-user-group"></i>
            </a>
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('clubs.edit', ['club_id' => $club->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('clubs.remove', ['club_id' => $club->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            <a class="py-2 pr-6 text-gray-500 hover:text-black"
                href="{{ route('clubs.mail', ['club_id' => $club->id]) }}" title="通知">
                <i class="fa-regular fa-envelope"></i>
            </a>
            <button class="py-2 pr-6 text-fuchsia-300 hover:text-fuchsia-600" title="重新報名"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('clubs.prune', ['club_id' => $club->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-recycle"></i>
            </button>
            @endif
        </td>
    </tr>
    @endforeach
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
