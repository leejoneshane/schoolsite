@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    將學生分組
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.enrolls', ['club_id' => $enroll->club->id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            營隊全名
        </th>
        <th scope="col" class="p-2">
            指導老師
        </th>
        <th scope="col" class="p-2">
            招生年級
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
    </tr>
    @php
        $club = $enroll->club;
        $section = $enroll->club_section();
    @endphp
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 {{ $club->style }}">
        <td class="p-2">{{ $club->name }}</td>
        <td class="p-2">{{ $section->teacher ?: ''}}</td>
        <td class="p-2">{{ $club->grade }}</td>
        <td class="p-2">{{ $section->studytime }}</td>
        <td class="p-2">{{ $section->location }}</td>
        <td class="p-2">{{ $section->total }}</td>
        <td class="p-2">{{ $section->maximum }}</td>
        <td class="p-2">{{ $club->count_enrolls() }}</td>
    </tr>
</table>
<div class="flex flex-col gap-3 justify-center items-center">
    <div class="bg-white rounded p-10">
        <form method="POST" action="{{ route('clubs.selgrp', ['enroll_id' => $enroll->id]) }}">
            @csrf
            <div class="p-3">
                <label for="parent" class="inline">班級座號：{{ $enroll->student->stdno }}</label>
            </div>
            <div class="p-3">
                <label for="parent" class="inline">學生姓名：{{ $enroll->student->realname }}</label>
            </div>
            <div class="p-3">
                <label for="parent" class="inline">聯絡人：{{ $enroll->parent }}</label>
            </div>
            <div class="p-3">
                <label for="email" class="inline">電子郵件地址：{{ $enroll->email }}</label>
            </div>
            <div class="p-3">
                <label for="mobile" class="inline">行動電話號碼：{{ $enroll->mobile }}</label>
            </div>
            <div class="p-3">
                <label for="groups">請選擇組別：</label>
                <select name="group" id="group" class="inline w-48 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700">
                    @foreach ($groups as $g)
                    <option value="{{ $g }}"{{ ($g == $enroll->devide) ? ' selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <p class="p-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    重新分組
                </button>
            </p>
        </form>
    </div>
</div>
@endsection
