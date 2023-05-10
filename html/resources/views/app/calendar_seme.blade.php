@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    學期行事曆
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar').'?current='.$current }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    @adminorteacher
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.training').'?current='.$current }}">
        <i class="fa-solid fa-calendar-days"></i>研習行事曆
    </a>
    @endadminorteacher
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.student').'?current='.$current }}">
        <i class="fa-solid fa-calendar-check"></i>學生行事曆
    </a>
</div>
<div class="p-3">
    <span class="text-xl font-bold">{{ substr($section,0 , -1) }}學年{{ (substr($section, -1) == '1') ? '上' : '下' }}學期行事曆</span>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.seme').'?current='.$current.'&section='.$prev }}"><i class="fa-solid fa-angles-left"></i>上一學期</a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.seme').'?current='.$current.'&section='.$next }}">下一學期<i class="fa-solid fa-angles-right"></i></a>
</div>
<table class="w-full text-sm text-left">
    <tr class="bg-gray-300 font-semibold text-lg">
        <th scope="col" class="p-2 w-8">月</th>
        <th scope="col" class="p-2 w-8">日</th>
        <th scope="col" class="p-2 w-16">星期</th>
        <th scope="col" class="p-2 text-center">當　　日　　行　　事</th>
    </tr>
    @foreach ($events as $event)
    <tr class="bg-white">
        <td class="p-2 text-center">{{ $event->month }}</td>
        <td class="p-2 text-center">{{ $event->day }}</td>
        <td class="p-2 text-center">{{ $event->weekday }}</td>
        <td class="p-2">{{ $event->content }}</td>
    </tr>
    @endforeach
</table>
@endsection
