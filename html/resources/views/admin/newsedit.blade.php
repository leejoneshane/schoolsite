@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯電子報
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('news') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 m-5" role="alert">
    <p>
        資料物件模型（Model），必須繼承 Subscribeable 特性（Trait），提供 template 屬性以及 newsletter() 函式。<br>
        自動派報功能，由資料物件模型的控制器（Controller）執行，電子報系統將不做定時派報處理。
    </p>
</div>
<form id="edit-unit" action="{{ route('news.edit', [ 'news' => $news->id ]) }}" method="POST">
    @csrf
    <div class="block">
    <label for="role_id" class="inline p-2">名稱：</label>
    <input class="inline w-24 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="caption" value="{{ $news->name }}">　　
    <label for="role_unit" class="inline p-2">資料物件模型：</label>
    <select class="inline w-64 rounded px-3 py-2 border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        name="model">
    @foreach ($models as $model)
        <option value="{{ $model }}"{{ ($model == $news->model) ? ' selected' : '' }}>{{ $model }}</option>
    @endforeach
    </select>　　
    <label for="role_name" class="inline p-2">派報排程：</label>
    <select class="inline w-36 rounded px-3 py-2 border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        name="loop" onchange="
            const selected = this.value;
            if (selected == 'auto') {
                document.getElementById('day').classList.add('hidden');
                document.getElementById('weekday').classList.add('hidden');
            } else if (selected == 'monthly') {
                document.getElementById('day').classList.remove('hidden');
                document.getElementById('weekday').classList.add('hidden');
            } else {
                document.getElementById('day').classList.add('hidden');
                document.getElementById('weekday').classList.remove('hidden');
            }
    ">
        <option value="auto"{{ ($news->loop['loop'] == 'auto') ? ' selected' : '' }}>自動</option>
        <option value="monthly"{{ ($news->loop['loop'] == 'monthly') ? ' selected' : '' }}>每月</option>
        <option value="weekly"{{ ($news->loop['loop'] == 'weekly') ? ' selected' : '' }}>每週</option>
    </select>　　
    <input class="inline w-36 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200 hidden"
        type="text" id="day" name="day" value="{{ ($news->loop['loop'] == 'monthly') ? $news->loop['day'] : '' }}" pattern="^([1-9]|[12][0-9]|3[01])$">
    <select class="inline w-36 rounded px-3 py-2 border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200 hidden"
        id="weekday" name="weekday" value="{{ ($news->loop['loop'] == 'weekly') ? $news->loop['day'] : '' }}">
        <option value="1">ㄧ</option>
        <option value="2">二</option>
        <option value="3">三</option>
        <option value="4">四</option>
        <option value="5">五</option>
        <option value="6">六</option>
        <option value="0">日</option>
    </select>
    <div class="inline py-4 px-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            修改
        </button>
    </div>
    </div>
</form>
<script>
@if ($news->loop['loop'] == 'monthly')
document.getElementById('day').classList.remove('hidden');
document.getElementById('weekday').classList.add('hidden');
@endif
@if ($news->loop['loop'] == 'weekly')
document.getElementById('day').classList.add('hidden');
document.getElementById('weekday').classList.remove('hidden');
@endif
</script>
@endsection
