@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    編輯技能
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.skills') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.skill_edit', [ 'skill_id' => $skill->id ]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <p><div class="p-3">
        <label for="name" class="text-base">技能名稱：</label>
        <input type="text" id="name" name="name" value="{{ $skill->name }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="text-base">技能描述：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120">{{ $skill->description }}</textarea>
    </div></p>
    <p><div class="p-3">
        <label for="passive" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="passive" name="passive" value="yes" class="sr-only peer"{{ $skill->passive ? ' checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">被動技能</span>
        </label>
        <br><span class="text-sm font-semibold">非戰鬥技能，當被老師扣血時可以使用的技能。</span>
    </div></p>
    <p><div class="p-3">
        <label for="object" class="text-base">作用對象：</label>
        <select id="object" name="object" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="self"{{ $skill->object == 'self' ? ' selected' :''}}>自己</option>
            <option value="partner"{{ $skill->object == 'partner' ? ' selected' :''}}>隊友</option>
            <option value="party"{{ $skill->object == 'party' ? ' selected' :''}}>全隊</option>
            <option value="target"{{ $skill->object == 'target' ? ' selected' :''}}>對手</option>
            <option value="all"{{ $skill->object == 'all' ? ' selected' :''}}>所有對手</option>
            <option value="all"{{ $skill->object == 'any' ? ' selected' :''}}>不限對象</option>
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="hit" class="text-base">命中率：</label>
        <input id="hit" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="hit_rate" min="0.1" max="2" step="0.1" value="{{ $skill->hit_rate }}">
        <br><span class="text-sm font-semibold">命中判定 = 命中率 +（自己敏捷點數 - 對方敏捷點數）/100</span>
    </div></p>
    <p><div class="p-3">
        <label for="cost" class="text-base">消耗魔力：</label>
        <input id="cost" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="cost_mp" min="0" max="100" step="1" value="{{ $skill->cost_mp }}">
    </div></p>
    <p><div class="p-3">
        <label for="attack" class="text-base">攻擊力：</label>
        <input id="attack" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="ap" min="0" max="50" step="1" value="{{ $skill->ap }}">
            <br><span class="text-sm font-semibold">攻擊威力 = (攻擊力 + 自己攻擊點數) - 對方防禦點數</span>
    </div></p>
    <p><div class="p-3">
        <label class="inline text-2xl">偷盜效果</label>
    </div></p>
    <p><div class="p-3">
        <label for="stealhp" class="text-base">吸血率：
            <input id="stealhp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="steal_hp" min="0" max="1" step="0.1" value="{{ $skill->steal_hp }}">
            <br><span class="text-sm font-semibold">自己補血點數 = 對方受傷點數 * 吸血率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="stealmp" class="text-base">回魔率：
            <input id="stealmp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="steal_mp" min="0" max="1" step="0.1" value="{{ $skill->steal_mp }}">
            <br><span class="text-sm font-semibold">自己補魔點數 = 自己消耗的魔力 * 回魔率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="stealgp" class="text-base">扒竊率：
            <input id="stealgp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
             type="number" name="steal_gp" min="0" max="0.45" step="0.05" value="{{ $skill->steal_gp }}">
            <br><span class="text-sm font-semibold">自己獲得的金幣 = 對方失去的金幣 = 對方的金幣數 * 扒竊率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label class="inline text-2xl">狀態變化</label>
    </div></p>
    <p><div class="p-3">
        <label for="status" class="text-base">解除狀態：</label>
        <select id="status" name="status" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="">無</option>
            <option value="DEAD"{{ $skill->status == 'DEAD' ? ' selected' :''}}>死亡</option>
            <option value="COMA"{{ $skill->status == 'COMA' ? ' selected' :''}}>昏迷</option>
        </select>
        <br><span class="text-sm font-semibold">在計算增減效益前執行</span>
    </div></p>
    <p><div class="p-3">
        <label for="inspire" class="text-base">賦予狀態：</label>
        <select id="inspire" name="inspire" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="">無</option>
            <option value="protect"{{ $skill->inspire == 'protect' ? ' selected' :''}}>護衛</option>
            <option value="apportion"{{ $skill->inspire == 'apportion' ? ' selected' :''}}>分散傷害</option>
            <option value="reflex"{{ $skill->inspire == 'reflex' ? ' selected' :''}}>傷害反射</option>
            <option value="hatred"{{ $skill->inspire == 'hatred' ? ' selected' :''}}>集中仇恨</option>
            <option value="invincible"{{ $skill->inspire == 'invincible' ? ' selected' :''}}>無敵狀態</option>
            <option value="throw"{{ $skill->inspire == 'throw' ? ' selected' :''}}>投擲道具</option>
            <option value="weak"{{ $skill->inspire == 'weak' ? ' selected' :''}}>身體虛弱</option>
            <option value="paralysis"{{ $skill->inspire == 'paralysis' ? ' selected' :''}}>精神麻痹</option>
            <option value="poisoned"{{ $skill->inspire == 'poisoned' ? ' selected' :''}}>中毒</option>
            <option value="escape"{{ $skill->inspire == 'escape' ? ' selected' :''}}>脫逃</option>
        </select>
        <br><span class="text-sm font-semibold">在計算增減效益前執行</span>
    </div></p>
    <p><div class="p-3">
        <label class="inline text-2xl">對象的增減效益：</label>
        <br><span class="font-semibold">對死亡者無效，魔力變化對昏迷者無效</span>
    </div></p>
    <p><div class="p-3">
        <label for="effecthp" class="text-base">血量變化：
            <input id="effecthp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="effect_hp" min="-200" max="200" step="0.1" value="{{ $skill->effect_hp }}">
            <br><span class="text-sm font-semibold">大於 0 = 治療，小於 0 = 受傷，整數為加或扣HP，小數為 MAX HP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="effectmp" class="text-base">魔力變化：
            <input id="effectmp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="effect_mp" min="-200" max="200" step="0.1" value="{{ $skill->effect_mp }}">
            <br><span class="text-sm font-semibold">大於 0 = 回復精神，小於 0 = 精神傷害，整數為加或扣MP，小數為 MAX MP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="effectap" class="text-base">攻擊力變化：
            <input id="effectap" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="effect_ap" min="-50" max="50" step="0.1" value="{{ $skill->effect_ap }}">
            <br><span class="text-sm font-semibold">大於 0 = 回復疲勞，小於 0 = 疲勞傷害，整數為加或扣AP，小數為 AP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="effectdp" class="text-base">防禦力變化：
            <input id="effectdp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="effect_dp" min="-50" max="50" step="0.1" value="{{ $skill->effect_dp }}">
            <br><span class="text-sm font-semibold">大於 0 = 皮膚強化，小於 0 = 裝甲侵蝕，整數為加或扣DP，小數為 DP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="effectsp" class="text-base">敏捷力變化：
            <input id="effectsp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="effect_sp" min="-50" max="50" step="0.1" value="{{ $skill->effect_sp }}">
            <br><span class="text-sm font-semibold">大於 0 = 重力減輕，小於 0 = 重力加倍，整數為加或扣SP，小數為 SP 的比率</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label for="times" class="text-base">持續時間：
            <input id="times" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="effect_times" min="0" max="480" step="1" value="{{ $skill->effect_times }}">
                <br><span class="text-sm font-semibold">賦予狀態、攻擊力、防禦力、敏捷力變化的持續時間，以分鐘為單位</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <label class="inline text-2xl">技能使用回饋</label>
    </div></p>
    <p><div class="p-3">
        <label for="earnxp" class="text-base">獲取經驗：</label>
        <input id="earnxp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="earn_xp" min="0" max="500" step="1" value="{{ $skill->earn_xp }}">
    </div></p>
    <p><div class="p-3">
        <label for="earngp" class="text-base">獲取金幣：</label>
        <input id="earngp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="earn_gp" min="0" max="500" step="1" value="{{ $skill->earn_gp }}">
    </div></p>
    <td class="p-3">
        <label for="file" class="text-base">特效動畫：</label>
        @if ($skill->image_avaliable())
        <img src="{{ $skill->image_url() }}" />
        @endif
        <input id="file" type="file" name="file" accept=".gif" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
        <br><span class="text-sm font-semibold">僅支援 .GIF 圖片格式</span>
    </td>
    <p class="p-6">
        <div class="text-xl">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
    <p class="h-12"></p>
</form>
@endsection
