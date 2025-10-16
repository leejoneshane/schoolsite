@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    新增怪物種族
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.monsters') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.monster_add') }}" method="POST">
    @csrf
    <input type="hidden" id="style" name="style" value="">
    <p><div class="p-3">
        <label for="name" class="inline">怪物種族：</label>
        <input type="text" id="name" name="name" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="inline">怪物種族描述：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120"></textarea>
    </div></p>
    <p><div class="p-3">
        <label class="inline">等級範圍：</label>
        <div class="inline">
            <input class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="min_level" min="1" max="30" step="1" value="1">
            ～
            <input class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="max_level" min="1" max="30" step="1" value="5">
        </div>
    </div></p>
    <p><div class="p-3">
        <label for="crit" class="text-sm">爆擊命中率：</label>
        <input id="crit" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="crit_rate" min="0.1" max="1" step="0.1" value="0.2">
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
    <p><div class="p-3">
        <label class="inline">獲勝後的獎勵：</label>
        <div class="inline">
            <label for="xp" class="text-sm">經驗值：</label>
            <input id="xp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="xp" min="0" step="10" value="0">
            <label for="gp" class="text-sm">金幣：</label>
            <input id="gp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                type="number" name="gp" min="0" step="1" value="0">
        </div>
    </div></p>
    <p><div class="p-3">
        <button data-modal-toggle="colorModal" type="button" id="picker" class="bg-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
            難易度顏色碼
        </button>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>
    </p>
</form>
<div id="colorModal" data-modal-target="colorModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[70] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">請選擇顏色：</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <span>
                    <input type="radio" id="pink" name="color" value="text-pink-500" class="hidden peer" />
                    <label for="pink" class="inline-block w-8 h-8 p-1 bg-pink-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="rose" name="color" value="text-rose-500" class="hidden peer" />
                    <label for="rose" class="inline-block w-8 h-8 p-1 bg-rose-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="red" name="color" value="text-red-500" class="hidden peer" />
                    <label for="red" class="inline-block w-8 h-8 p-1 bg-red-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="orange" name="color" value="text-orange-500" class="hidden peer" />
                    <label for="orange" class="inline-block w-8 h-8 p-1 bg-orange-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="amber" name="color" value="text-amber-500" class="hidden peer" />
                    <label for="amber" class="inline-block w-8 h-8 p-1 bg-amber-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="yellow" name="color" value="text-yellow-500" class="hidden peer" />
                    <label for="yellow" class="inline-block w-8 h-8 p-1 bg-yellow-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <br>
                <span>
                    <input type="radio" id="lime" name="color" value="text-lime-500" class="hidden peer" />
                    <label for="lime" class="inline-block w-8 h-8 p-1 bg-lime-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="green" name="color" value="text-green-500" class="hidden peer" />
                    <label for="green" class="inline-block w-8 h-8 p-1 bg-green-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="emerald" name="color" value="text-emerald-500" class="hidden peer" />
                    <label for="emerald" class="inline-block w-8 h-8 p-1 bg-emerald-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="teal" name="color" value="text-teal-500" class="hidden peer" />
                    <label for="teal" class="inline-block w-8 h-8 p-1 bg-teal-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="cyan" name="color" value="text-cyan-500" class="hidden peer" />
                    <label for="cyan" class="inline-block w-8 h-8 p-1 bg-cyan-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="sky" name="color" value="text-sky-500" class="hidden peer" />
                    <label for="sky" class="inline-block w-8 h-8 p-1 bg-sky-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <br>
                <span>
                    <input type="radio" id="blue" name="color" value="text-blue-500" class="hidden peer" />
                    <label for="blue" class="inline-block w-8 h-8 p-1 bg-blue-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="indigo" name="color" value="text-indigo-500" class="hidden peer" />
                    <label for="indigo" class="inline-block w-8 h-8 p-1 bg-indigo-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="violet" name="color" value="text-violet-500" class="hidden peer" />
                    <label for="violet" class="inline-block w-8 h-8 p-1 bg-violet-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="purple" name="color" value="text-purple-500" class="hidden peer" />
                    <label for="purple" class="inline-block w-8 h-8 p-1 bg-purple-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
                <span>
                    <input type="radio" id="fuchsia" name="color" value="text-fuchsia-500" class="hidden peer" />
                    <label for="fuchsia" class="inline-block w-8 h-8 p-1 bg-fuchsia-500 border-2 cursor-pointer peer-checked:border-blue-600 hover:border-teal-600"></label>    
                </span>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="set_color();" data-modal-toggle="colorModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    選好了！
                </button>
                <button data-modal-toggle="colorModal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var old = '';
    function set_color() {
        var style = document.querySelector('input[name="color"]:checked');
        var node = document.getElementById('style');
        node.value = style.value;
        var btn = document.getElementById('picker');
        if (old == '') {
            btn.classList.add(style.value);
        } else {
            btn.classList.replace(old, style.value);
        }
        old = style.value;
    }
</script>
@endsection
