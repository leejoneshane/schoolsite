@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    社團一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        點擊社團名稱可以管理社團報名資訊，點擊圖示「信封」寄信給報名的家長。<br>
    </p>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            分類
        </th>
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
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 {{ $club->kind->style }}">
        <td class="p-2">{{ $club->kind->name }}</td>
        <td class="p-2">{{ $club->name }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        @php
            $section = $club->section();
        @endphp
        @if ($section)
        <td class="p-2">{{ $section->teacher }}</td>
        <td class="p-2">{{ $section->studytime }}</td>
        <td class="p-2">{{ $section->location }}</td>
        <td class="p-2">{{ $section->total }}</td>
        <td class="p-2">{{ ($section->maximum == 0) ? '—' : $section->maximum}}</td>
        <td class="p-2">{{ $club->count_enrolls() }}</td>
        @else
        <td colspan="6" class="p-2">本學期未開班</td>
        @endif
        <td class="p-2">
            <a class="py-2 pr-6 text-green-300 hover:text-green-600"
                href="{{ route('clubs.enrolls', ['club_id' => $club->id]) }}" title="學生管理">
                <i class="fa-solid fa-user-group"></i>
            </a>
            <a class="py-2 pr-6 text-gray-500 hover:text-black"
                href="{{ route('clubs.mail', ['club_id' => $club->id]) }}" title="通知">
                <i class="fa-regular fa-envelope"></i>
            </a>
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endsection
