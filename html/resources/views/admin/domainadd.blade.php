@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增教學領域
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('domains') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-domain" action="{{ route('domains.add') }}" method="POST">
    @csrf
    <div class="block">
    <label for="domain_name" class="inline p-2">領域名稱：</label>
    <input class="inline w-36 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="domain_name">
    <div class="inline py-4 px-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            新增
        </button>
    </div>
    </div>
</form>
@endsection
