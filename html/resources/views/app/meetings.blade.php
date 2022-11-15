@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    網路朝會
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('meeting', ['date' => date("Y-m-d", strtotime('+1 day', strtotime($date)))]) }}">
        <i class="fa-solid fa-backward"></i>前一天
    </a>
    <span class="pl-6">
        <input class="inline w-36 rounded px-2 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="date" value="{{ $date }}" onchange="window.location.replace('{{ route('meeting') }}' + '/' + this.value);">
    </span>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('meeting', ['date' => date("Y-m-d", strtotime('+1 day', strtotime($date)))]) }}">
        後一天<i class="fa-solid fa-forward"></i>
    </a>
    @if ($create)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('meeting.add') }}">
        <i class="fa-solid fa-circle-plus"></i>張貼業務報告
    </a>
    @endif
</div>
<table class="w-full py-4 text-left font-normal">
    @forelse ($meets as $meet)
    <tr class="text-white bg-blue-700 font-semibold text-lg">
        <th class="p-2 w-full">
            {{ $meet->role . $meet->reporter }}：{{ $meet->created_at . $meet->unit->name }}業務報告
            @if ($create && $unit == $meet->unit_id)
            <a class="text-sm py-2 pl-6 rounded text-gray-300 hover:text-gray-100" href="{{ route('meeting.edit', ['id' => $meet->id]) }}">
                <i class="fa-solid fa-pen"></i>編輯
            </a>
            <a class="py-2 pr-6 text-red-300 hover:text-red-100" href="void()" title="刪除"
            onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('meeting.remove', ['id' => $meet->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>刪除
            </a>
            @endif
        </th>
    </tr>
    <tr class="bg-white">
        <td class="p-2">{{ $meet->words }}</td>
    </tr>
    @empty
    <tr class="text-white bg-blue-700 font-semibold text-lg">
        <th class="p-2 w-full text-center">本日朝會尚未舉行, 請稍候片刻......</th>
    </tr>
    @endforelse
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
