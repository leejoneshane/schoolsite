@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增課外社團
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-club" action="{{ route('clubs.add') }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="kind" class="inline">社團分類：</label>
        <select name="kind" class="inline w-48 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($kinds as $k)
            <option value="{{ $k->id }}"{{ ($kind == $k->id) ? ' selected' : '' }}>{{ $k->name }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="unit" class="inline">負責單位：</label>
        <select name="unit" class="inline w-48 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        @foreach ($units as $u)
            <option value="{{ $u->id }}" {{ ($unit == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="manager" class="inline">管理員：</label>
        <select name="manager" class="inline w-48 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value=""></option>
        @foreach ($teachers as $t)
        @php
            $gap = '';
            $rname = '';
            if ($t->role_name) $rname = $t->role_name;
            for ($i=0;$i<6-mb_strlen($rname);$i++) {
                $gap .= '　';
            }
            $display = $t->role_name . $gap . $t->realname;
        @endphp
            <option value="{{ $t->uuid }}">{{ $display }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="title" class="inline">營隊全名：</label>
        <input type="text" name="title" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請不要包含學年學期資訊，系統會自動將學生報名資訊依學期分開管理。</span>
    </div></p>
    <p><div class="p-3">
        <label for="short" class="inline">簡稱：</label>
        <input type="text" name="short" class="inline w-32 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>列印報表使用，請不要超過 5 個中文字！</span>
    </div></p>
    <p><div class="p-3">
        <label for="grades" class="inline">招生年級：</label>
        <div id="grades" class="inline">
            <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="grades[]" value="1"><span class="text-sm">一　</span>
            <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="grades[]" value="2"><span class="text-sm">二　</span>
            <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="grades[]" value="3"><span class="text-sm">三　</span>
            <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="grades[]" value="4"><span class="text-sm">四　</span>
            <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="grades[]" value="5"><span class="text-sm">五　</span>
            <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="checkbox" name="grades[]" value="6"><span class="text-sm">六年級</span>
        </div>
    </div></p>
    <p><div class="p-3">
        <label for="remove" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="remove" name="remove" value="yes" class="sr-only peer" checked>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">開放[取消報名、編輯]選項</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="lunch" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="lunch" name="lunch" value="yes" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">顯示午餐選項</span>
        </label>
        <div class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>午餐選項包含：自理、葷食、素食。</div>
    </div></p>
    <p><div class="p-3">
        <label for="devide" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="devide" name="devide" value="yes" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">分組上課</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="stop" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="stop" name="stop" value="yes" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">暫停報名</span>
        </label>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>
    </p>
</form>
<script nonce="selfhost">
    function check_self(ele) {
        if (ele.checked) {
            document.getElementById('selfdefine').checked = false;
        }
    }
</script>
@endsection
