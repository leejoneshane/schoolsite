@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    職編結果一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize') }}">
        <i class="fa-solid fa-eject"></i>回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listvacancy', ['year' => $year]) }}">
        <i class="fa-solid fa-square-poll-horizontal"></i>職缺一覽表
    </a>
</div>
<div class="w-full">
    <label for="years">請選擇學年度：</label>
    <select id="years" class="inline w-16 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
        onchange="
        var year = this.value;
        window.location.replace('{{ route('organize') }}' + '/' + year);
        ">
        @foreach ($years as $y)
        <option value="{{ $y }}"{{ ($y == $year) ? ' selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
</div>
<table class="w-full p-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="w-32 p-2">
            職務
        </th>
        <th scope="col" class="w-24 p-2">
            員額編制
        </th>
        <th scope="col" class="p-2">
            編排結果（<span class="text-green-500">綠色</span>為保留職缺人員，<span class="text-blue-500">藍色</span>為指派擔任職務人員）
        </th>
    </tr>
    @foreach ($vacancys as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall }}
        </td>
        <td class="p-2">
            @foreach ($v->reserved() as $t)
            <span class="pl-4 text-green-500">{{ $t->realname }}</span>
            @endforeach
            @foreach ($v->assigned() as $t)
            <span class="pl-4 text-blue-500">{{ $t->realname }}</span>
            @endforeach
        </td>
    </tr>
    @endforeach
</table>
@endsection