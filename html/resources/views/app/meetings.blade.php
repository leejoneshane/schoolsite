@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    網路朝會
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('meeting', ['date' => date("Y-m-d", strtotime('-1 day', strtotime($date)))]) }}">
        <i class="fa-solid fa-backward"></i>前一天
    </a>
    <span class="pl-6">
        <input class="w-36 rounded p-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
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
    <button type="button" class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600"
    onclick="
        const myform = document.getElementById('remove');
        myform.action = '{{ route('meeting.send') }}';
        myform.submit();
    ">
        <i class="fa-regular fa-paper-plane"></i>補寄業務報告
    </button>
</div>
<table class="w-full py-4 text-left font-normal">
    @forelse ($meets as $meet)
    <tr class="text-white bg-blue-700 font-semibold text-lg">
        <th class="p-2 w-full">
            {{ $meet->unit->name }}業務報告：{{ $meet->reporter . $meet->created_at}}
            @if ($create && $unit->id == $meet->unit_id)
            <a class="text-sm py-2 pl-6 rounded text-gray-300 hover:text-gray-100" href="{{ route('meeting.edit', ['id' => $meet->id]) }}">
                <i class="fa-solid fa-pen"></i>編輯
            </a>
            <button class="text-sm py-2 pl-6 text-red-300 hover:text-red-100" title="刪除"
            onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('meeting.remove', ['id' => $meet->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>刪除
            </button>
            @endif
        </th>
    </tr>
    <tr class="bg-white">
        <td class="p-2">{!! $meet->words !!}</td>
    </tr>
    @empty
    <tr class="text-white bg-blue-700 font-semibold text-lg">
        <th class="p-2 w-full text-center">本日朝會尚未舉行，請稍候片刻......</th>
    </tr>
    @endforelse
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
