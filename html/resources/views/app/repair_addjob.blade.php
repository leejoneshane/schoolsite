@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增報修紀錄
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('repair.list', ['kind' => $kind->id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        {!! $kind->selftest !!}
    </p>
</div>
<form id="add-job" action="{{ route('repair.report', ['kind' => $kind->id]) }}" method="POST">
    @csrf
    <p class="p-3">
        <label class="inline">報修者：{{ Auth::user()->profile->realname }}</label>
    </p>
    <p class="p-3">
        <label for="place" class="inline">維修地點：</label>
        <input type="text" id="place" name="place" class="inline w-1/2 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </p>
    <p class="p-3">
        <label for="summary" class="inline">問題主旨：</label>
        <input type="text" id="place" name="summary" class="inline w-1/2 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </p>
    <p class="p-3">
        <label for="description" class="inline">問題描述：</label>
        <textarea id="description" name="description" rows="4" class="inline block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        ></textarea>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請依照上方訊息先進行自我檢測！</span>
    </p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                報修
            </button>
        </div>
    </p>
</form>
@endsection
