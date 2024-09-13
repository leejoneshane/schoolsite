@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯道具
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('game.items') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.item_edit', [ 'item_id' => $item->id ]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <p><div class="p-3">
        <label for="name" class="text-base">道具名稱：</label>
        <input type="text" id="name" name="name" value="{{ $item->name }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="text-base">道具描述：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120">{{ $item->description }}</textarea>
    </div></p>
    <p><div class="p-3">
        <label for="object" class="text-base">作用對象：</label>
        <select id="object" name="object" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="self"{{ $item->object == 'self' ? ' selected' : '' }}>自己</option>
            <option value="partner"{{ $item->object == 'partner' ? ' selected' : '' }}>隊友</option>
            <option value="party"{{ $item->object == 'party' ? ' selected' : '' }}>全隊</option>
            <option value="target"{{ $item->object == 'target' ? ' selected' : '' }}>對手</option>
            <option value="all"{{ $item->object == 'all' ? ' selected' : '' }}>所有對手</option>
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="hit" class="text-base">命中率：</label>
        <input id="hit" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="hit_rate" min="0.1" max="1" step="0.1" value="{{ $item->hit_rate }}">
        <br><span class="text-sm font-semibold">命中判定 = 命中率 +（自己敏捷點數 - 對方敏捷點數）/100</span>
    </div></p>
    <p><div class="p-3">
        <label for="status" class="text-base">解除狀態：</label>
        <select id="status" name="status" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="">無</option>
            <option value="DEAD"{{ $item->status == 'DEAD' ? ' selected' : '' }}>死亡</option>
            <option value="COMA"{{ $item->status == 'COMA' ? ' selected' : '' }}>昏迷</option>
        </select>
        <br><span class="text-sm font-semibold">在計算增減效益前執行</span>
    </div></p>
    <p><div class="p-3">
        <label for="gp" class="text-base">購買價格：</label>
        <input id="gp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="gp" min="100" step="1" value="{{ $item->gp }}">
    </div></p>
    <p><div class="p-3">
        <label class="inline text-2xl">增減益效果：</label>
        <br><span class="font-semibold">所有效果將在購買家具後，合併到據點效果一併計算。</span>
    </div></p>
    <p><div class="p-3">
        <label for="hp" class="text-base">血量變化：
            <input id="hp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="hp" min="-50" max=50" step="0.1" value="{{ $item->hp }}">
            <br><span class="text-sm font-semibold">大於 0 = 治療，小於 0 = 受傷，整數為加或扣HP，小數為 MAX HP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="mp" class="text-base">魔力變化：
            <input id="mp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="mp" min="-50" max="50" step="0.1" value="{{ $item->mp }}">
            <br><span class="text-sm font-semibold">大於 0 = 回復精神，小於 0 = 精神傷害，整數為加或扣MP，小數為 MAX MP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="ap" class="text-base">攻擊力變化：
            <input id="ap" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="ap" min="-50" max="50" step="0.1" value="{{ $item->ap }}">
            <br><span class="text-sm font-semibold">大於 0 = 回復疲勞，小於 0 = 疲勞傷害，整數為加或扣AP，小數為 AP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="dp" class="text-base">防禦力變化：
            <input id="dp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="dp" min="-50" max="50" step="0.1" value="{{ $item->dp }}">
            <br><span class="text-sm font-semibold">大於 0 = 皮膚強化，小於 0 = 裝甲侵蝕，整數為加或扣DP，小數為 DP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="sp" class="text-base">敏捷力變化：
            <input id="sp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="sp" min="-50" max="50" step="0.1" value="{{ $item->sp }}">
            <br><span class="text-sm font-semibold">大於 0 = 重力減輕，小於 0 = 重力加倍，整數為加或扣SP，小數為 SP 的比率</span>
        </label>
    </div></p>
    <td class="p-3">
        <label for="file" class="text-base">道具圖片：</label>
        @if ($item->image_avaliable())
        <img src="{{ $item->image_url() }}" />
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
