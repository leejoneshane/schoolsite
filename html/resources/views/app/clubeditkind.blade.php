@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯社團分類
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.kinds') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        不重複報名：指該類別的社團只允許一個學生報名一個社團。<br>
		人工審核：由管理員錄取報名學生，若要讓系統自動錄取學生，請勿勾選。<br>
		暫停報名：開啟此選項將讓所有該類社團全部無法報名。<br>
		報名和截止日期將統一在社團分類設置，報名時間與休息時間是指在報名期間系統每天開啟報名功能的時段。<br>
    </p>
</div>
<form id="edit-unit" action="{{ route('clubs.editkind', ['kid' => $kind->id]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="title" class="inline">類別名稱：</label>
        <input class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="title" value="{{ $kind->name }}">
    </p>
    <p><div class="p-3">
        <label for="single" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="single" name="single" value="yes" class="sr-only peer"{{($kind->single) ? ' checked' : ''}}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">不重複報名</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="auditing" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="auditing" name="auditing" value="yes" class="sr-only peer"{{($kind->manual_auditing) ? ' checked' : ''}}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">人工審核</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="stop" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="stop" name="stop" value="yes" class="sr-only peer"{{($kind->stop_enroll) ? ' checked' : ''}}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">暫停報名</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label class="inline">報名與截止日期：</label>
        <input class="inline w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" id="enroll" name="enroll" value="{{ $kind->enrollDate }}">到
        <input class="inline w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" id="expire" name="expire" value="{{ substr($kind->expireDate, 0, 10) }}">
    </div></p>
    <p><div id="part_time" class="p-3">
        <label class="inline">每日報名時間：</label>
        <input class="inline w-36 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="time" id="work" name="work" value="{{ $kind->workTime }}" step="300">到
        <input class="inline w-36 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="time" id="rest" name="rest" value="{{ substr($kind->restTime, 0, 10) }}" step="300">
    </div></p>
    <p><div class="p-3">
        <label for="style" class="inline">Tailwind 樣式：</label>
        <input class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="style" value="{{ $kind->style }}">
    </p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>    
    </p>
</form>
@endsection
