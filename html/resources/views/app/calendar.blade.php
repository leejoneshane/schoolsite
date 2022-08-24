@extends('layouts.main')

@section('content')
<div class="relative bg-white dark:bg-gray-700 text-black dark:text-gray-200">
    <div class="p-2">
        @if (session('error'))
        <div class="m-5 border-red-500 bg-red-100 dark:bg-red-700 border-b-2" role="alert">
            {{ session('error') }}
        </div>
        @endif
        @if (session('success'))
        <div class="m-5 border-green-500 bg-green-100 dark:bg-green-700 border-b-2" role="alert">
            {{ session('success') }}
        </div>
        @endif
        @if (session('message'))
        <div class="m-5 border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2" role="alert">
            {{ session('message') }}
        </div>
        @endif
    </div>
    <div class="text-2xl font-bold leading-normal pb-5">
        學校行事曆
        @if ($create)
        <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.addEvent') }}">
            <i class="fa-solid fa-calendar-plus"></i>新增事件
        </a>
        @endif
        @admin
        <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.seme') }}">
            <i class="fa-solid fa-calendar"></i>學期行事曆
        </a>
        <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.traning') }}">
            <i class="fa-solid fa-calendar-days"></i>週三行事曆
        </a>
        @endadmin
        @teacher
        <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.seme') }}">
            <i class="fa-solid fa-calendar"></i>學期行事曆
        </a>
        <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.traning') }}">
            <i class="fa-solid fa-calendar-days"></i>週三行事曆
        </a>
        @endteacher
        <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.student') }}">
            <i class="fa-solid fa-calendar-check"></i>學生行事曆
        </a>
    </div>
    <form id="select-date" action="{{ route('calendar') }}" method="GET">
        <label for="current">顯示哪一天的事件:</label>
        <input type="date" id="current" name="current"
            value="{{ $current }}"
            min="{{ substr($seme['min'], 0, 10) }}"
            max="{{ substr($seme['max'], 0, 10) }}"
            onchange="document.getElementById('select-date').submit();">
    </form>
    <table class="w-full text-sm text-left">
        <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
            <th scope="col" class="p-2">負責單位</th>
            <th scope="col" class="p-2">起訖日期</th>
            <th scope="col" class="p-2">起訖時間</th>
            <th scope="col" class="p-2">事件摘要</th>
            <th scope="col" class="p-2">補充說明</th>
            <th scope="col" class="p-2">地點</th>
        </tr>
        @foreach ($events as $event)
        <tr class="even:bg-white odd:bg-gray-100 hover:bg-blue-100 dark:hover:bg-blue-600 dark:even:bg-gray-700 dark:odd:bg-gray-600">
            <td class="p-2">{{ $event->unit->name }}</td>
            <td class="p-2">{{ $event->startDate }}{{ ($event->startDate == $event->endDate) ? '' : '～'.$event->endDate }}</td>
            <td class="p-2">{{ ($event->all_day) ? '全天' : $event->startTime.'～'.$event->endTime }}</td>
            <td class="p-2">{{ $event->summary }}</td>
            <td class="p-2">{{ $event->description }}</td>
            <td class="p-2">{{ $event->location }}
            @if ($editable[$event->id])
            <a class="py-2 px-6 text-blue-300 hover:text-blue-600"
                href="{{ route('calendar.editEvent', ['event' => $event->id]) }}">
                <i class="fa-solid fa-pen"></i>
            </a>
            @endif
            @if ($deleteable[$event->id])
            <a class="py-2 px-6 text-blue-300 hover:text-blue-600"
                href="{{ route('calendar.removeEvent', ['event' => $event->id]) }}">
                <i class="fa-solid fa-trash"></i>
            </a>
            @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
