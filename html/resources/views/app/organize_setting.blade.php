@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    流程控制
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize') }}">
        <i class="fa-solid fa-eject"></i>回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.vacancy') }}">
        <i class="fa-solid fa-chair"></i>職缺設定
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.arrange') }}">
        <i class="fa-solid fa-puzzle-piece"></i>職務編排
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listvacancy') }}">
        <i class="fa-solid fa-square-poll-horizontal"></i>職缺一覽表
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listresult') }}">
        <i class="fa-solid fa-user-check"></i>職編結果一覽表
    </a>
</div>
<div class="relative flex flex-col justify-center items-center">
    <div class="w-auto">
        <form method="POST" action="{{ route('organize.setting') }}">
            @csrf
    
            <div class="relative mb-6">
                <label for="survey_at" class="inline mb-2 text-sm font-medium text-gray-900">填寫學經歷及積分：</label>
                <input id="survey_at" class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none"
                    type="date" name="survey_at" value="{{ $settings ? $settings->survey_at->format('Y-m-d') : '' }}" min="{{ $seme->mindate }}" max="{{ $seme->maxdate }}" required autofocus>
            </div>
            <div class="relative mb-6">
                <label for="first_stage" class="inline mb-2 text-sm font-medium text-gray-900">第一階段意願調查：</label>
                <input id="first_stage" class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none"
                    type="date" name="first_stage" value="{{ $settings ? $settings->first_stage->format('Y-m-d') : '' }}" min="{{ $seme->mindate }}" max="{{ $seme->maxdate }}" required autofocus>
            </div>
            <div class="relative mb-6">
                <label for="pause_at" class="inline mb-2 text-sm font-medium text-gray-900">第一階段截止日期：</label>
                <input id="pause_at" class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none"
                    type="date" name="pause_at" value="{{ $settings ? $settings->pause_at->format('Y-m-d') : '' }}" min="{{ $seme->mindate }}" max="{{ $seme->maxdate }}" required autofocus>
            </div>
            <div class="relative mb-6">
                <label for="second_stage" class="inline mb-2 text-sm font-medium text-gray-900">第二階段意願調查：</label>
                <input id="second_stage" class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none"
                    type="date" name="second_stage" value="{{ $settings ? $settings->second_stage->format('Y-m-d') : '' }}" min="{{ $seme->mindate }}" max="{{ $seme->maxdate }}" required autofocus>
            </div>
            <div class="relative mb-6">
                <label for="close_at" class="inline mb-2 text-sm font-medium text-gray-900">意願調查截止日期：</label>
                <input id="close_at" class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none"
                    type="date" name="close_at" value="{{ $settings ? $settings->close_at->format('Y-m-d') : '' }}" min="{{ $seme->mindate }}" max="{{ $seme->maxdate }}" required autofocus>
            </div>
            <div class="mb-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    設定完成！
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
