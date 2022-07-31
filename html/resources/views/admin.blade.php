@extends('layouts.admin')

@section('content')
<div class="m-5 bg-white relative flex flex-col gap-3 justify-center items-center">
    <div class="md:border md:border-gray-300 bg-white md:shadow-lg shadow rounded p-10">
        <div class="text-2xl font-bold leading-normal text-center pb-5" >親愛的{{ (Auth::user()->profile) ? Auth::user()->profile['realname'] : Auth::user()->name }}</div>

        @if (session('status'))
        <div class="border-green-500 bg-green-100 border-t-2" role="alert">
            {{ session('status') }}
        </div>
        @endif

        <div class="relative mb-6">
            歡迎使用管理介面，請從左側選單點選功能！
        </div>
    </div>
</div>
@endsection
