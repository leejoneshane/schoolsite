@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯學生表單
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('rosters') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-roster" action="{{ route('roster.edit', ['id' => $roster->id]) }}" method="POST">
    @csrf
    <p class="p-3">
        <label for="title" class="inline">名稱：</label>
        <input type="text" id="title" name="title" value="{{ $roster->name }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>名稱請勿包含年度和學期！</span>
    </p>
    <p class="p-3">
        <label for="grades" class="inline">填報年級：</label>
        @foreach ($grades as $grade)
        <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="checkbox" name="grades[]" value="{{ $grade->id }}"{{ (is_array($roster->grades) && in_array($grade->id, $roster->grades)) ? ' checked' : '' }}><span class="text-sm">{{ $grade->name }}　</span>
        @endforeach
    </p>
    <p class="p-3">
        <label for="fields" class="inline">顯示欄位：</label>
        @foreach ($fields as $field)
        <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="checkbox" name="fields[]" value="{{ $field['id'] }}"{{ (is_array($roster->fields) && in_array($field['id'], $roster->fields)) ? ' checked' : '' }}><span class="text-sm">{{ $field['name'] }}　</span>
        @endforeach
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>預設會顯示「班級」、「座號」、「姓名」欄位，也可以加上以上欄位！</span>
    </p>
    <p class="p-3">
        <label for="domains" class="inline">填報教師：</label>
        @foreach ($domains as $domain)
        <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="checkbox" name="domains[]" value="{{ $domain->id }}"{{ (is_array($roster->domains) && in_array($domain->id, $roster->domains)) ? ' checked' : '' }}><span class="text-sm">{{ $domain->name }}　</span>
        @endforeach
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>導師為預設填報者，無需設定！</span>
    </p>
    <p class="p-3">
        <label class="inline">填報日期：</label>
        <input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="start" value="{{ $roster->started_at->format('Y-m-d') }}">　到　
        <input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="end" value="{{ $roster->ended_at->format('Y-m-d') }}">
    </p>
    <p class="p-3">
        <label class="inline">人數限制：</label>
        <input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="number" name="min" min="1" max="10" value="{{ $roster->min }}">　到　
        <input class="inline w-36 rounded px-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="number" name="max" min="1" max="10" value="{{ $roster->max }}">
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請指定人數範圍，若為特定人數，請填同一數字！</span>
    </p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
@endsection
