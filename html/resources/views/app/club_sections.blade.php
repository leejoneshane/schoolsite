@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    學生社團時程管理
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $club->kind->id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    @if (!$current)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.addsection', ['club_id' => $club->id]) }}">
        <i class="fa-solid fa-circle-plus"></i>為本學期開班
    </a>
    @endif
    @if (!$next)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.addsection', ['club_id' => $club->id, 'section' => next_section()]) }}">
        <i class="fa-solid fa-circle-plus"></i>為下學期開班
    </a>
    @endif
</div>
<p><div class="p-3">
    <label for="kind" class="px-3 inline">社團分類：{{ $club->kind->name }}</label>
    <label for="unit" class="px-3 inline">負責單位：{{ $club->unit->name }}</label>
    <label for="title" class="px-3 inline">營隊全名：{{ $club->name }}</label>
</div></p>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            學期
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
    @foreach ($sections as $sec)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 {{ $club->style }}">
        <td class="p-2">{{ $sec->name }}</td>
        <td class="p-2">{{ $sec->teacher }}</td>
        <td class="p-2">{{ $sec->studytime }}</td>
        <td class="p-2">{{ $sec->location }}</td>
        <td class="p-2">{{ $sec->total }}</td>
        <td class="p-2">{{ $sec->maximum }}</td>
        <td class="p-2">{{ $club->count_enrolls($sec->section) }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('clubs.editsection', ['section_id' => $sec->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('clubs.removesection', ['section_id' => $sec->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
    @endforeach
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection