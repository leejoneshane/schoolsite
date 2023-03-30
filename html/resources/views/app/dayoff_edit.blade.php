@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯公假單
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="edit-dayoff" action="{{ route('dayoff.edit', ['id' => $report->id]) }}" method="POST">
    @csrf
    <p class="p-3">
        <label for="reason" class="inline">公假事由：</label>
        <input type="text" id="reason" name="reason" value="{{ $report->reason }}" class="inline w-96 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" maxlength="25" required>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>事由請勿超過 25 個字，詳細內容請填寫於備註欄。</span>
    </p>
    <p class="p-3">
        <label for="rdate" class="inline">自訂時間字串：</label>
        <input type="text" id="rdate" name="rdate" value="{{ $report->rdate }}" class="inline w-96 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" maxlength="25">
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>若已經輸入自訂時間字串，底下的「公假時間」欄位可以留白。</span>
    </p>
    <p class="p-3">
        <label>公假時間：</label>
        @foreach ($report->datetimes as $dd)
        <br>日期：<input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" type="date" name="dates[]" min="{{ current_between_date()->mindate }}" max="{{ current_between_date()->maxdate }}" value="{{ $dd['date'] }}"> 時間：<input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" type="time" name="from[]" min="08:00" max="16:00" value="{{ $dd['from'] }}"> ～ <input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" type="time" name="to[]" min="08:00" max="16:00" value="{{ $dd['to'] }}">
        <button type="button" class="inline py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_datetime(this);"><i class="fa-solid fa-circle-minus"></i></button>
        @endforeach
        <button id="new" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="add_datetime()"><i class="fa-solid fa-circle-plus"></i>
        </button>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>「自訂時間字串」或「公假時間」至少要有一項有資料。</span>
    </p>
    <p class="p-3">
        <label for="location" class="inline">地點：</label>
        <input type="text" id="location" name="location" value="{{ $report->location }}" class="inline w-96 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" maxlength="25">
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>若沒有特定地點可以留白。</span>
    </p>
    <p><div class="p-3">
        <label for="who" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="who" name="who" value="yes" class="sr-only peer"{{ $report->who ? ' checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">要求科任教師簽名</span>
        </label>
    </div></p>
    <p class="p-3">
        <label for="memo" class="inline">備註：</label>
        <textarea class="inline w-1/2 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            id="memo" name="memo" rows="5" cols="120">{{ $report->memo }}</textarea>
    </p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
<script>
    function remove_datetime(elem) {
        const parent = elem.parentNode;
        const child = elem.previousElementSibling;
        parent.removeChild(child);
        parent.removeChild(elem);
    }

    function add_datetime() {
        var target = document.getElementById('new');
        const elem = document.createElement('label');
        var my_date = '<br>日期：<input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" type="date" name="dates[]" min="{{ current_between_date()->mindate }}" max="{{ current_between_date()->maxdate }}" value="{{ date('Y-m-d') }}"> 時間：<input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" type="time" name="from[]" min="08:00" max="16:00" value="08:00"> ～ <input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" type="time" name="to[]" min="08:00" max="16:00" value="16:00">';
        elem.innerHTML = my_date;
        target.parentNode.insertBefore(elem, target);
        const elemb = document.createElement('button');
        target.parentNode.insertBefore(elemb, target);
        my_btn = '<button type="button" class="inline py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_datetime(this);"><i class="fa-solid fa-circle-minus"></i></button>';
        elemb.outerHTML = my_btn;
    }
</script>
@endsection
