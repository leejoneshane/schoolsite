@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    編輯地下城
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.evaluate_assign', [ 'evaluate_id' => $dungeon->evaluate_id ]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.dungeon_edit', [ 'dungeon_id' => $dungeon->id ]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="title" class="text-base">地下城名稱：</label>
        <input type="text" id="title" name="title" value="{{ $dungeon->title }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="text-base">地下城描述：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120">{{ $dungeon->description }}</textarea>
    </div></p>
    <p><div class="p-3">
        <label class="text-base">試卷名稱：{{ $dungeon->evaluate->title }}</label>
    </div></p>
    <p><div class="p-3">
        <label for="monster" class="text-base">配置怪物：</label>
        <select id="monster" name="monster" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($monsters as $m)
            <option value="{{ $m->id }}"{{ ($m->id == $dungeon->monster_id) ? ' checked' : '' }}>{{ $m->name }}</option>
            @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="times" class="text-base">挑戰次數限制：</label>
        <input type="number" id="times" name="times" value="{{ $dungeon->times }}" min="0" max="100" step="1" class="inline w-20 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
        <br><span class="text-sm font-semibold">0 為不限次數</span>
    </div></p>
    <p><div class="p-3">
        <label class="inline">開放日期：</label>
        <input class="w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="open_date" value="{{ $dungeon->opened_at ? $dungeon->opened_at->format('Y-m-d') : date('Y-m-d') }}">
    </div></p>
    <p><div class="p-3">
        <label class="inline">關閉日期：</label>
        <input class="w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="close_date" value="{{ $dungeon->closed_at ? $dungeon->closed_at->format('Y-m-d') : date('Y-m-d') }}">
    </div></p>
    <p class="p-6">
        <div class="text-xl">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
@endsection
