@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    評量成績一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.dungeons') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            座號
        </th>
        <th scope="col" class="p-2">
            姓名
        </th>
        <th scope="col" class="p-2">
            分數
        </th>
        <th scope="col" class="p-2">
            測驗日期
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @forelse ($answers as $a)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $a->seat }}</td>
        <td class="p-2">{{ $a->student }}</td>
        <td class="p-2">{{ $a->score }}</td>
        <td class="p-2">{{ $a->tested_at->format('Y-m-d') }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.journeys', ['answer_id' => $a->id]) }}" title="測驗歷程">
                <i class="fa-solid fa-timeline"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.answer_remove', ['answer_id' => $a->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
    @empty
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2" colspan="8">尚未有學生參加測驗！</td>
    </tr>
    @endforelse
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
