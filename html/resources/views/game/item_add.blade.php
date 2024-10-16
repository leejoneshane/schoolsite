@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    新增道具
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.items') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.item_add') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <p><div class="p-3">
        <label for="name" class="text-base">道具名稱：</label>
        <input type="text" id="name" name="name" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="text-base">道具描述：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120"></textarea>
    </div></p>
    <p><div class="p-3">
        <label for="passive" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="passive" name="passive" value="yes" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">被動道具</span>
        </label>
        <br><span class="text-sm font-semibold">非戰鬥道具，當被老師扣血時可以使用的道具。</span>
    </div></p>
    <p><div class="p-3">
        <label for="object" class="text-base">作用對象：</label>
        <select id="object" name="object" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="self">自己</option>
            <option value="partner">隊友</option>
            <option value="party">全隊</option>
            <option value="target">對手</option>
            <option value="all">所有對手</option>
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="hit" class="text-base">命中率：</label>
        <input id="hit" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="hit_rate" min="0.1" max="1" step="0.1" value="1">
        <br><span class="text-sm font-semibold">命中判定 = 命中率 +（自己敏捷點數 - 對方敏捷點數）/100</span>
    </div></p>
    <p><div class="p-3">
        <label for="status" class="text-base">解除狀態：</label>
        <select id="status" name="status" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="">無</option>
            <option value="DEAD">死亡</option>
            <option value="COMA">昏迷</option>
        </select>
        <br><span class="text-sm font-semibold">在計算增減效益前執行</span>
    </div></p>
    <p><div class="p-3">
        <label for="inspire" class="text-base">賦予狀態：</label>
        <select id="inspire" name="inspire" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="">無</option>
            <option value="protect">護衛</option>
            <option value="apportion">分散傷害</option>
            <option value="reflex">傷害反射</option>
            <option value="hatred">集中仇恨</option>
            <option value="invincible">無敵狀態</option>
            <option value="throw">投擲道具</option>
            <option value="weak">身體虛弱</option>
            <option value="paralysis">精神麻痹</option>
            <option value="poisoned">中毒</option>
        </select>
        <br><span class="text-sm font-semibold">在計算增減效益前執行</span>
    </div></p>
    <p><div class="p-3">
        <label for="gp" class="text-base">購買價格：</label>
        <input id="gp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="gp" min="100" step="1" value="100">
    </div></p>
    <p><div class="p-3">
        <label class="inline text-2xl">增減益效果：</label>
        <br><span class="font-semibold">所有效果將在購買家具後，合併到據點效果一併計算。</span>
    </div></p>
    <p><div class="p-3">
        <label for="hp" class="text-base">血量變化：
            <input id="hp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="hp" min="-50" max="50" step="0.1" value="0">
            <br><span class="text-sm font-semibold">大於 0 = 治療，小於 0 = 受傷，整數為加或扣HP，小數為 MAX HP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="mp" class="text-base">魔力變化：
            <input id="mp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="mp" min="-50" max="50" step="0.1" value="0">
            <br><span class="text-sm font-semibold">大於 0 = 回復精神，小於 0 = 精神傷害，整數為加或扣MP，小數為 MAX MP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="ap" class="text-base">攻擊力變化：
            <input id="ap" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="ap" min="-50" max="50" step="0.1" value="0">
            <br><span class="text-sm font-semibold">大於 0 = 回復疲勞，小於 0 = 疲勞傷害，整數為加或扣AP，小數為 AP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="dp" class="text-base">防禦力變化：
            <input id="dp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="dp" min="-50" max="50" step="0.1" value="0">
            <br><span class="text-sm font-semibold">大於 0 = 皮膚強化，小於 0 = 裝甲侵蝕，整數為加或扣DP，小數為 DP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="sp" class="text-base">敏捷力變化：
            <input id="sp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="sp" min="-50" max="50" step="0.1" value="0">
            <br><span class="text-sm font-semibold">大於 0 = 重力減輕，小於 0 = 重力加倍，整數為加或扣SP，小數為 SP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="times" class="text-base">持續時間：
            <input id="times" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="effect_times" min="0" max="480" step="10" value="0">
            <br><span class="text-sm font-semibold">攻擊力、防禦力、敏捷力變化的持續時間，以分鐘為單位</span>
        </label>
    </div></p>
    <td class="p-3">
        <label for="file" class="text-base">道具圖片：</label>
        <input id="file" type="file" name="file" accept=".png,.gif" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
        <br><span class="text-sm font-semibold">支援 .PNG .GIF 圖片格式，須去背，解析度請勿大於 300x300</span>
    </td>
    <p class="p-6">
        <div class="text-xl">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>
    </p>
    <p class="h-12"></p>
</form>
@endsection
