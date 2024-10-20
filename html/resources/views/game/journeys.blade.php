@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    評量歷程一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.answers', [ $answer->dungeon_id ]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            班級
        </th>
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
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $answer->classroom_id }}</td>
        <td class="p-2">{{ $answer->seat }}</td>
        <td class="p-2">{{ $answer->student }}</td>
        <td class="p-2">{{ $answer->score }}</td>
        <td class="p-2">{{ $answer->tested_at->format('Y-m-d') }}</td>
    </tr>
</table>
<table class="w-full p-4 bg-white text-left font-normal mb-32">
    @foreach ($journeys as $j)
    <tr class="bg-teal-100 text-black font-semibold text-lg">
        <td class="p-2">{{ $j->question->sequence }}</td>
        <td class="p-2">{{ $j->question->question }}</td>
        <td class="p-2">正確答案：{{ $j->question->correct->option }}</td>
        <td class="p-2">配分：{{ $j->question->score }}</td>
    </tr>
    @foreach ($j->question->options as $o)
    <tr class="bg-white dark:bg-gray-700">
        <td colspan="2" class="text-right">
            @if ($o->id == $j->option_id)
                @if ($j->is_correct)
                <i class="fa-solid fa-check"></i>
                @else
                <i class="fa-solid fa-xmark"></i>
                @endif
            @endif
        </td>
        <td class="p-2">{{ $o->sequence }}</td>
        <td id="option{{ $o->id }}" class="p-2">{{ $o->option }}</td>
    </tr>
    @endforeach
    @endforeach
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
