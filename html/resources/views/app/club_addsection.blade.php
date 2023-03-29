@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    課外社團開班
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-section" action="{{ route('clubs.addsection', ['club_id' => $section->club_id]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="kind" class="inline">社團分類：{{ $club->kind->name }}</label>
    </div></p>
    <p><div class="p-3">
        <label for="unit" class="inline">負責單位：{{ $club->unit->name }}</label>
    </div></p>
    <p><div class="p-3">
        <label for="title" class="inline">營隊全名：{{ $club->name }}</label>
    </div></p>
    <p><div class="p-3">
        <label for="total" class="inline">招生人數：</label>
        <input type="number" name="total" min="0" class="inline w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請輸入數字，0或留白代表無限制！</span>
    </div></p>
    <p><div class="p-3">
        <label for="teacher" class="inline">指導教師：</label>
        <input type="text" name="teacher" class="inline w-48 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>若有多位老師，請用空格區隔！</span>
    </div></p>
    <p><div class="p-3">
        <label for="location" class="inline">授課地點：</label>
        <input type="text" name="location" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="memo" class="inline">備註：</label>
        <textarea name="memo" rows="4" class="inline block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </textarea>
    </div></p>
    <p><div class="p-3">
        <label class="inline">開課日期：</label>
        <input class="inline w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="startdate" value="{{ date('Y-m-d') }}">　到　
        <input class="inline w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="enddate" value="{{ date('Y-m-d') }}">
    </div></p>
    <p><div id="part_time" class="p-3">
        <label class="inline">上課時段：每週</label>
        <div id="weekdays" class="inline">
            <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="weekdays[]" value="1"><span class="text-sm" onclick="check_self(this)">一　</span>
            <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="weekdays[]" value="2"><span class="text-sm" onclick="check_self(this)">二　</span>
            <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="weekdays[]" value="3"><span class="text-sm" onclick="check_self(this)">三　</span>
            <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="weekdays[]" value="4"><span class="text-sm" onclick="check_self(this)">四　</span>
            <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="weekdays[]" value="5"><span class="text-sm" onclick="check_self(this)">五　</span>
            <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" id="selfdefine" name="selfdefine" value="yes" onclick="
                    if (this.checked) {
                        const collection = document.getElementsByName('weekdays[]');
                        for (let i = 0; i < collection.length; i++) {
                            collection[i].checked = false;
                        }
                    }
                "><span class="text-sm">家長自訂　</span>
            <input class="pl-6 inline w-36 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="time" name="starttime" value="16:00" step="300">　到　
            <input class="inline w-36 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="time" name="endtime" value="17:00" step="300">
        </div>
    </div></p>
    <p><div class="p-3">
        <label for="cash" class="inline">費用：</label>
        新台幣<input type="number" name="cash" min="0" class="inline w-24 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>元
    </div></p>
    <p><div class="p-3">
        <label for="limit" class="inline">報名上限：</label>
        <input type="number" name="limit" min="0" class="inline w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請輸入數字，0或留白代表無限制！報名上限低於招生人數時，將會保留招生名額，作為現場報名或其他安排。報名上限超過招生人數時，超過部分視同候補，將不會自動錄取！</span>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>
    </p>
</form>
<script>
    function check_self(ele) {
        if (ele.checked) {
            document.getElementById('selfdefine').checked = false;
        }
    }
</script>
@endsection
