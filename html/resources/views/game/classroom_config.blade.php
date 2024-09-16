@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    {{ $room->name }}遊戲規則
</div>
<form id="add-class" action="{{ route('game.classroom_config') }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="daily" class="text-base">每日回復 MP：</label>
        <input id="daily" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="mp" min="1" max="50" step="1" value="{{ $config && $config->daily_mp ? $config->daily_mp : 4 }}">
    </div></p>
    <p><div class="p-3">
        <span class="text-sm leading-normal text-gray-400 sm:block">
            <label for="base" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="base" name="change_base" value="yes" class="sr-only peer"{{ $config && $config->change_base ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-gray-900 dark:text-gray-300">允許公會變更據點</span>
            </label>
        </span>
    </div></p>
    <p><div class="p-3">
        <span class="text-sm leading-normal text-gray-400 sm:block">
            <label for="pro" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="pro" name="change_class" value="yes" class="sr-only peer"{{ $config && $config->change_class ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-gray-900 dark:text-gray-300">允許學生變更職業</span>
            </label>
        </span>
    </div></p>
    <p><div class="p-3">
        <span class="text-sm leading-normal text-gray-400 sm:block">
            <label for="arena" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="arena" name="arena_open" value="yes" class="sr-only peer"{{ $config && $config->arena_open ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-gray-900 dark:text-gray-300">允許學生對戰</span>
            </label>
        </span>
    </div></p>
    <p><div class="p-3">
        <span class="text-sm leading-normal text-gray-400 sm:block">
            <label for="furniture" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="furniture" name="furniture_shop" value="yes" class="sr-only peer"{{ $config && $config->furniture_shop ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-gray-900 dark:text-gray-300">允許公會購買家具</span>
            </label>
        </span>
    </div></p>
    <p><div class="p-3">
        <span class="text-sm leading-normal text-gray-400 sm:block">
            <label for="item" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="item" name="item_shop" value="yes" class="sr-only peer"{{ $config && $config->item_shop ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-gray-900 dark:text-gray-300">允許學生購買道具</span>
            </label>
        </span>
    </div></p>
    <p><div class="p-3">
        <span class="text-sm leading-normal text-gray-400 sm:block">
            <label for="pet" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="pet" name="pet_shop" value="yes" class="sr-only peer"{{ $config && $config->pet_shop ? ' checked' : '' }} disabled>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-gray-900 dark:text-gray-300">允許學生購買寵物</span>
            </label>
        </span>
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
