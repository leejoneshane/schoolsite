@extends('layouts.admin')

@section('content')
<div class="p-10">
    @if (!isset($error) && !isset($success) && !isset($message))
        @if (Auth::check())
        <div class="text-2xl font-bold leading-normal pb-5">親愛的{{ (Auth::user()->profile) ? Auth::user()->profile->realname : Auth::user()->name }}</div>
        @else
        <div class="text-2xl font-bold leading-normal pb-5">歡迎光臨</div>
        @endif
        <div class="relative mb-6">
            歡迎使用管理介面，請從左側選單點選功能！
        </div>
    @endif
</div>
@endsection
