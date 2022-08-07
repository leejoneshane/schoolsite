@extends('layouts.admin')

@section('content')
<div class="m-5 relative bg-white dark:bg-gray-700 text-black dark:text-gray-200">
    <div class="p-10">
        @if (session()->missing('error') && session()->missing('success') && session()->missing('message'))
            @if (Auth::check())
            <div class="text-2xl font-bold leading-normal pb-5">親愛的{{ (Auth::user()->profile) ? Auth::user()->profile['realname'] : Auth::user()->name }}</div>
            @else
            <div class="text-2xl font-bold leading-normal pb-5">歡迎光臨</div>
            @endif
            <div class="relative mb-6">
                歡迎使用管理介面，請從左側選單點選功能！
            </div>
        @endif

        @if (session('error'))
        <div class="border-red-500 bg-red-100 border-b-2" role="alert">
            {{ session('error') }}
        </div>
        @endif
        @if (session('success'))
        <div class="border-green-500 bg-green-100 border-b-2" role="alert">
            {{ session('success') }}
        </div>
        @endif
        @if (session('message'))
        <div class="border-blue-500 bg-blue-100 border-b-2" role="alert">
            {{ session('message') }}
        </div>
        @endif
    </div>
</div>
@endsection
