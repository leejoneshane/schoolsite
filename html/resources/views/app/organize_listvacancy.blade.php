@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    職缺一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize') }}">
        <i class="fa-solid fa-eject"></i>回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listresult') }}">
        <i class="fa-solid fa-user-check"></i>職編結果一覽表
    </a>
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
            保留職缺
        </th>
    </tr>
    @foreach ($vacancys as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2">
            {{ $v->shortfall }}
        </td>
        <td class="p-2">
            @foreach ($v->reserved() as $t)
            <span class="pl-4">{{ $t->realname }}</span>
            @endforeach
        </td>
    </tr>
    @endforeach
</table>
@endsection