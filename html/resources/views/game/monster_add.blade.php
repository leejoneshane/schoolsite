@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增怪物
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('game.monsters') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.monster_add') }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="name" class="inline">怪物種族：</label>
        <input type="text" id="name" name="name" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="inline">怪物描述：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120"></textarea>
    </div></p>
    <p><div class="p-3">
        <label class="inline">攻擊能力：</label>
        <div class="inline">
            <label for="hit" class="text-sm">一般攻擊命中率：</label>
            <input id="hit" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="hit_rate" min="0.1" max="1" step="0.1" value="0.8">
            <label for="crit" class="text-sm">爆擊命中率：</label>
            <input id="crit" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="crit_rate" min="0.1" max="1" step="0.1" value="0.2">
        </div>
    </div></p>
    <p><div class="p-3">
        <label class="inline">基礎能力：</label>
        <div class="inline">
            <label for="base_hp" class="text-sm">最大健康值：</label>
            <input id="base_hp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="hp" min="10" max="500" step="10" value="100">
            <label for="base_ap" class="text-sm">攻擊力：</label>
            <input id="base_ap" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="ap" min="10" max="50" step="1" value="10">
            <label for="base_dp" class="text-sm">防禦力：</label>
            <input id="base_dp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="dp" min="10" max="50" step="1" value="10">
            <label for="base_sp" class="text-sm">敏捷力：</label>
            <input id="base_sp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="sp" min="10" max="50" step="1" value="10">
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
