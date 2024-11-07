@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    試題瀏覽
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.evaluates') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full px-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            試卷名稱
        </th>
        <th scope="col" class="p-2">
            科目名稱
        </th>
        <th scope="col" class="p-2">
            出題範圍
        </th>
        <th scope="col" class="p-2">
            適用年級
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $evaluate->title }}</td>
        <td class="p-2">{{ $evaluate->subject }}</td>
        <td class="p-2">{{ $evaluate->range }}</td>
        <td class="p-2">{{ $evaluate->grade->name }}</td>
    </tr>
</table>
<table class="w-full p-4 bg-white text-left font-normal mb-32">
<tbody id="qlist">
@foreach ($evaluate->questions as $q)
    <tr id="q{{ $q->id }}" class="bg-teal-100 text-black font-semibold text-lg">
        <td class="w-12 p-2">{{ $q->sequence }}</td>
        <td id="caption{{ $q->id }}" class="p-2">{{ $q->question }}</td>
        <td id="score{{ $q->id }}" class="p-2">配分：{{ $q->score }}</td>
        <td class="w-48 p-2">
        </td>
        <td class="w-1/2"></td>
    </tr>
    <tr class="bg-white dark:bg-gray-700">
        <td class="p-2" colspan="4">
        <td>
            <table class="w-full float-right text-left font-normal">
                <tbody id="olist{{ $q->id }}">
                    @foreach ($q->options as $o)
                    <tr class="odd:bg-white even:bg-gray-100">
                        <td class="w-12 p-2">{{ $o->sequence }}</td>
                        <td id="option{{ $o->id }}" class="p-2">{{ $o->option }}</td>
                        <td class="w-48 p-2">
                            @if ($q->answer == $o->id)
                            <i class="fa-solid fa-check"></i>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
@endforeach
</tbody>
</table>
@endsection
