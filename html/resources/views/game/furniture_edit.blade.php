@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯家具
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('game.furnitures') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.furniture_edit', [ 'furniture_id' => $furniture->id ]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="name" class="text-furniture">據點名稱：</label>
        <input type="text" id="name" name="name" value="{{ $furniture->name }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="text-furniture">據點描述：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120">{{ $furniture->description }}</textarea>
    </div></p>
    <p><div class="p-3">
        <label class="inline text-2xl">每日執行一次：</label>
        <br><span class="font-semibold">對所有隊員有效，已死亡或昏迷的隊員會自動回復。</span>
    </div></p>
    <p><div class="p-3">
        <label for="hp" class="text-base">血量變化：
            <input id="hp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="hp" min="-50" max=50" step="0.1" value="{{ $furniture->hp }}">
            <br><span class="text-sm font-semibold">大於 0 = 治療，小於 0 = 受傷，整數為加或扣HP，小數為 MAX HP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="mp" class="text-base">魔力變化：
            <input id="mp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="mp" min="-50" max="50" step="0.1" value="{{ $furniture->mp }}">
            <br><span class="text-sm font-semibold">大於 0 = 回復精神，小於 0 = 精神傷害，整數為加或扣MP，小數為 MAX MP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label class="inline text-2xl">增減效益：</label>
        <br><span class="font-semibold">對死亡者無效，對其他隊員永久有效。</span>
    </div></p>
    <p><div class="p-3">
        <label for="ap" class="text-base">攻擊力變化：
            <input id="ap" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="ap" min="-50" max="50" step="0.1" value="{{ $furniture->ap }}">
            <br><span class="text-sm font-semibold">大於 0 = 回復疲勞，小於 0 = 疲勞傷害，整數為加或扣AP，小數為 AP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="dp" class="text-base">防禦力變化：
            <input id="dp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="dp" min="-50" max="50" step="0.1" value="{{ $furniture->dp }}">
            <br><span class="text-sm font-semibold">大於 0 = 皮膚強化，小於 0 = 裝甲侵蝕，整數為加或扣DP，小數為 DP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="sp" class="text-base">敏捷力變化：
            <input id="sp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="sp" min="-50" max="50" step="0.1" value="{{ $furniture->sp }}">
            <br><span class="text-sm font-semibold">大於 0 = 重力減輕，小於 0 = 重力加倍，整數為加或扣SP，小數為 SP 的比率</span>
        </label>
    </div></p>
    <td class="p-3">
        <label for="file" class="text-base">據點圖片：</label>
        @if ($furniture->image_avaliable())
        <img src="{{ $furniture->image_url() }}" />
        @endif
        <input id="file" type="file" name="file" accept=".png,.gif" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
        <br><span class="text-sm font-semibold">支援 .PNG .GIF 圖片格式，須去背，解析度請勿大於 300x300</span>
    </td>
    <p class="p-6">
        <div class="text-xl">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
@endsection
