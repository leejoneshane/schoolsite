@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    新增地圖探險
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.worksheet_assign', [ 'worksheet_id' => $worksheet->id ]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.adventure_add', [ 'worksheet_id' => $worksheet->id ]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label class="text-base">學習單標題：{{ $worksheet->title }}</label>
    </div></p>
    <p><div class="p-3">
        <label class="text-base">指派班級：</label>
        @foreach ($teacher->classrooms as $cls)
        <span class="px-2">
            <input type="checkbox" id="cls{{ $cls->id }}" name="classrooms[]" value="{{ $cls->id }}">{{ $cls->name }}
        </span>
        @endforeach
    </div></p>
    <p class="p-6">
        <div class="text-xl">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>
    </p>
</form>
@endsection
