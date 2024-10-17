@extends('layouts.main')

@section('content')
<div class="p-10">
    @if (Auth::check())
    <div class="text-2xl font-bold leading-normal pb-5">
        親愛的{{ employee()->realname }}
    </div>
    @else
    <div class="text-2xl font-bold leading-normal pb-5">歡迎光臨</div>
    @endif
    <div class="relative mb-6">
        歡迎使用E化服務網，請從左側選單點選功能！
    </div>
</div>
@endsection
