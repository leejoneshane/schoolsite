@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯行事曆事件
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar').'?current='.$current }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    @adminorteacher
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.seme').'?current='.$current }}">
        <i class="fa-solid fa-calendar"></i>學期行事曆
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.training').'?current='.$current }}">
        <i class="fa-solid fa-calendar-days"></i>研習行事曆
    </a>
    @endadminorteacher
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.student').'?current='.$current }}">
        <i class="fa-solid fa-calendar-check"></i>學生行事曆
    </a>
</div>
<form id="edit-event" action="{{ route('calendar.editEvent', ['event' => $event->id]) }}" method="POST">
    @csrf
    <input type="hidden" name="current" value="{{ $current }}">
    <p><div class="p-3">
        <label for="calendar_id" class="inline">同步行事曆：</label>
        <select class="inline w-44 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            id="calendar_id" name="calendar_id">
            @foreach ($calendars as $c)
            <option value="{{ $c->id }}"{{ ($c->id == $event->id) ? ' selected' : ''}}>{{ $c->summary }}</option>
            @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="unit_id" class="inline">負責單位：</label>
        <select class="inline w-28 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            id="unit_id" name="unit_id">
            @foreach ($units as $u)
            <option value="{{ $u->id }}"{{($u->id == $event->unit_id) ? ' selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="important" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="important" name="important" value="yes" class="sr-only peer"{{ $event->important ? ' checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">學校重要活動</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="training" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="training" name="training" value="yes" class="sr-only peer"{{ $event->training ? ' checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">研習活動</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="sdate" class="inline">起迄日期：</label>
        <input class="w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" id="sdate" name="start_date" value="{{ $event->startDate->toDateString() }}" min="{{ $seme->mindate }}" max="{{ $seme->maxdate }}">到
        <input class="w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" id="edate" name="end_date" value="{{ $event->endDate->toDateString() }}" min="{{ $seme->mindate }}" max="{{ $seme->maxdate }}">
    </div></p>
    <p><div class="p-3">
        <label for="all_day" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="all_day" name="all_day" value="yes" class="sr-only peer" onclick="
                const allday = this.checked;
                if (allday) {
                    document.getElementById('part_time').classList.add('hidden');
                } else {
                    document.getElementById('part_time').classList.remove('hidden');
                }
            "{{ $event->all_day ? ' checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">全天</span>
        </label>
    </div></p>
    <p><div id="part_time" class="p-3{{ $event->all_day ? ' hidden' : '' }}">
        <label for="stime" class="inline">起迄時間：</label>
        <input class="w-36 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="time" id="stime" name="start_time" value="{{ $event->startTime->format('H:i:s') }}" min="07:00:00" max="18:00:00">到
        <input class="w-36 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="time" id="etime" name="end_time" value="{{ $event->endTime->format('H:i:s') }}" min="07:00:00" max="18:00:00">
    </div></p>
    <p><div class="p-3">
        <label for="summary" class="inline">事件摘要：</label>
        <input id="summary" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="summary" value="{{ $event->summary }}" required>
    </p>
    <p><div class="p-3">
        <label for="desc" class="inline">補充說明：</label>
        <textarea id="desc" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="desc" rows="5" cols="120">{{ $event->description }}</textarea>
    </div></p>
    <p><div class="p-3">
        <label for="location" class="inline">地點：</label>
        <input id="location" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="location" value="{{ $event->location }}">
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
@endsection
