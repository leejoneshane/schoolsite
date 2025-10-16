@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    重複報名清冊
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $kind]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            年班座號
        </th>
        <th scope="col" class="p-2">
            學生姓名
        </th>
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
            報名社團
        </th>
        <th scope="col" class="p-2">
            報名順位
        </th>
        <th scope="col" class="p-2">
            錄取（或候補）
        </th>
    </tr>
    @forelse ($students as $student)
        @if ($student)
        @foreach ($student->section_enrolls($section) as $enroll)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        @if($loop->first)
        <td class="p-2">{{ $student->class_id }}{{ $student->seat }}</td>
        <td class="p-2">{{ $student->realname }}</td>
        <td class="p-2">{{ $enroll->parent }}</td>
        <td class="p-2">{{ $enroll->email }}</td>
        <td class="p-2">{{ $enroll->mobile }}</td>
        @else
        <td class="p-2"></td>
        <td class="p-2"></td>
        <td class="p-2"></td>
        <td class="p-2"></td>
        <td class="p-2"></td>
        @endif
        <td class="p-2 {{ $enroll->club->style }}">{{ $enroll->club->name }}</td>
        <td class="p-2">{{ $enroll->section_order() + 1 }}</td>
        <td class="p-2">
            @if ($enroll->accepted)
            <i class="fa-solid fa-check"></i>
            @endif
        </td>
    </tr>
        @endforeach
        @endif
    @empty
    <tr>
        <td colspan="8" class="text-xl font-bold">目前查無重複報名的學生！</td>
    </tr>
    @endforelse
</table>
@endsection
