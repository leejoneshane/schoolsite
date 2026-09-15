@extends('layouts.main')

@section('content')
<div class="p-10">
    @if (Auth::check())
    <div class="text-2xl font-bold leading-normal pb-5">
        親愛的 {{ employee()?->realname }}
    </div>
    @else
    <div class="text-2xl font-bold leading-normal pb-5">親愛的訪客</div>
    @endif
    <div class="relative mb-6">
        歡迎使用E化服務網，請從左側選單點選功能！
    </div>
    @teacher
    <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">點擊編輯個人資料<i class="fa-solid fa-pen-to-square"></i></a>
    @endteacher
</div>
@endsection
