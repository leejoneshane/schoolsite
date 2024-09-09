@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增職業
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('game.classes') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.class_add') }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="name" class="inline">職業名稱：</label>
        <input type="text" id="name" name="name" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="inline">職業描述：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120"></textarea>
    </div></p>
    <p><div class="p-3">
        <label class="inline">升級比率：</label>
        <div class="inline">
            <label for="hp_lvlup" class="text-sm">最大健康值：</label>
            <input id="hp_lvlup" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="hp_lvlup" min="0.1" max="5" step="0.1" value="0.1">
            <label for="mp_lvlup" class="text-sm">最大行動力：</label>
            <input id="mp_lvlup" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="mp_lvlup" min="0.1" max="5" step="0.1" value="0.1">
            <label for="ap_lvlup" class="text-sm">攻擊力：</label>
            <input id="ap_lvlup" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="ap_lvlup" min="0.1" max="5" step="0.1" value="0.1">
            <label for="dp_lvlup" class="text-sm">防禦力：</label>
            <input id="dp_lvlup" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="dp_lvlup" min="0.1" max="5" step="0.1" value="0.1">
            <label for="sp_lvlup" class="text-sm">敏捷力：</label>
            <input id="sp_lvlup" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="sp_lvlup" min="0.1" max="5" step="0.1" value="0.1">
        </div>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>升級時，各項點數提升的方式，0.1~0.9 表示提升1點的機率。1 以上表示必然提升點數</span>
    </div></p>
    <p><div class="p-3">
        <label class="inline">基礎能力：</label>
        <div class="inline">
            <label for="base_hp" class="text-sm">最大健康值：</label>
            <input id="base_hp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="base_hp" min="10" max=100" step="1" value="10">
            <label for="base_mp" class="text-sm">最大行動力：</label>
            <input id="base_mp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="base_mp" min="10" max="100" step="1" value="10">
            <label for="base_ap" class="text-sm">攻擊力：</label>
            <input id="base_ap" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="base_ap" min="10" max="50" step="1" value="10">
            <label for="base_dp" class="text-sm">防禦力：</label>
            <input id="base_dp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="base_dp" min="10" max="50" step="1" value="10">
            <label for="base_sp" class="text-sm">敏捷力：</label>
            <input id="base_sp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="base_sp" min="10" max="50" step="1" value="10">
        </div>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>
    </p>
</form>
@endsection
