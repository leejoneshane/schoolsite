@extends('layouts.game')

@section('content')
<div class="w-full h-screen flex flex-col justify-between">
    <div class="w-full h-screen flex-initial">
        <div class="relative h-full flex flex-row">
            <div id="me" class="w-1/3 inline-flex content-end">
                <div class="m-2 flex flex-col gap-1">
                    <div class="w-24 h-8 font-extrabold">{{ $character->name }}</div>
                    <div class="w-24 h-4 bg-gray-200 rounded-full leading-none">
                        <div id="hp" class="h-4 bg-green-500 text-xs font-medium text-green-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($character->hp / $character->max_hp * 100) }}%;">{{ $character->hp }}</div>
                    </div>
                    <div class="w-24 h-4 bg-gray-200 rounded-full leading-none">
                        <div id="mp" class="h-4 bg-blue-500 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($character->mp / $character->max_mp * 100) }}%;">{{ $character->mp }}</div>
                    </div>
                    <div id="status" class="w-24 h-8">正常</div>
                    <img title="{{ $character->name }}" src="{{ $character->url ?: '' }}" class="absolute bottom-40 w-1/3 z-50">
                </div>
            </div>
            <div class="w-1/3 text-center">
                <div id="dungeons" class="p-2">
                    @if ($dungeons->count() > 0)
                    <button onclick="choice();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        挑選要進入的地下城！
                    </button>
                    @else
                        沒有可以進入的場所！
                    @endif
                </div>
            </div>
            <div id="monster" class="w-1/3 inline-flex content-end">
                <div class="m-2 flex flex-col gap-1">
                    <div id="monster_name" class="w-24 h-8 font-extrabold"></div>
                    <div class="w-24 h-4 bg-gray-200 rounded-full leading-none">
                        <div id="monster_hp" class="h-4 bg-green-500 text-xs font-medium text-green-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($monster->hp / $monster->max_hp * 100) }}%;">{{ $monster->hp }}</div>
                    </div>
                    <img id="monster_img" title="{{ $monster->name }}" src="{{ $monster->random_url() ?: '' }}" class="absolute bottom-40 w-1/3 z-50">
                </div>
            </div>    
        </div>
    </div>
</div>
<div id="warnModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[90] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">警告</h3>
            </div>
            <div id="info" class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="warnModal.hide();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    我知道了
                </button>
            </div>
        </div>
    </div>
</div>
<div id="dungeonModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">已開放地下城：</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul>
                @foreach ($dungeons as $d)
                <li>
                    <input type="radio" id="dun{{ $d->id }}" name="dungeon" value="{{ $d->id }}" class="hidden peer" />
                    <label for="{{ $d->id }}" class="inline-block w-full p-2 {{ $d->monster->style }} bg-white border-2 border-gray-200 cursor-pointer peer-checked:border-blue-600 hover:bg-gray-50">
                        <span class="inline-block text-normal w-48">{{ $d->name }}</span>
                        <span class="inline-block text-xs w-80">{{ $d->description }}</span>
                    </label>
                </li>
                @endforeach
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="enter();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    進入
                </button>
                <button onclick="exit(); dungeonModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    我再想想！
                </button>
            </div>
        </div>
    </div>
</div>
<div id="actionModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">動作選單</h3>
            </div>
            <div id="action_target" class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="prepare_skill(); actionModal.hide();" type="button" class="bg-amber-300 hover:bg-amber-500 text-white font-bold py-2 px-4 rounded-full">
                    技能
                </button>
                <button onclick="prepare_item(); actionModal.hide();" type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">
                    道具
                </button>
            </div>
        </div>
    </div>
</div>
<div id="skillsModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[70] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">技能書</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul id="skillList" >
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="skill_cast();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即行動
                </button>
                <button onclick="skillsModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="itemsModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[70] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">背包</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul id="itemList" >
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="item_use();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即使用
                </button>
                <button onclick="itemsModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var character = '{{ $character->uuid }}';
    var evaluate; //evaluate_id 
    var dungeon; //dungeon_id
    var monster; //spawn_id
    var skills = [];
    var items =[];
    var target; // monster_id
    var target_type; //self or monster
    var data_type; //skill or item
    var data_skill; //skill id
    var data_item; //item id

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-dungeon');
    var $targetEl = document.getElementById('warnModal');
    const warnModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('dungeonModal');
    const dungeonModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('actionModal');
    const actionModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('skillsModal');
    const skillsModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('itemsModal');
    const itemsModal = new window.Modal($targetEl);
    window.onload = refresh;
    window.onunload = warn;

    function enter() {
    }

    function refresh() {
            var me = response.data.character;
            var hp = document.getElementById('hp');
            hp.style.width = Math.round(me.hp / me.max_hp * 100) + '%';
            hp.innerHTML = me.hp;
            var mp = document.getElementById('mp');
            mp.style.width = Math.round(me.mp / me.max_mp * 100) + '%';
            mp.innerHTML = me.mp;
            var status = document.getElementById('status');
            status.innerHTML = me.status_str;
            var monster = response.data.monster;
            var myname = document.getElementById('monster_name');
            myname.innerHTML = monster.name;
            hp = document.getElementById('monster_hp');
            hp.style.width = Math.round(monster.hp / monster.max_hp * 100) + '%';
            hp.innerHTML = monster.hp;
    }

    function action_self() {
        if (done) return;
        target = character;
        target_type = 'self';
        var msg = document.getElementById('action_target');
        msg.innerHTML = '要對自己施展技能或使用道具？';
        actionModal.show();
    }

    function action_monster(uuid) {
        if (done) return;
        target = uuid;
        target_type = 'enemy';
        var msg = document.getElementById('action_target');
        msg.innerHTML = '要對敵人施展技能或使用道具？';
        actionModal.show();
    }

    function prepare_skill() {
        var ul = document.getElementById('skillList');
        ul.innerHTML = '';
        window.axios.post('{{ route('game.get_myskills') }}', {
            uuid: character,
            kind: target_type,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            skills = [];
            for (var k in response.data.skills) {
                var skill = response.data.skills[k];
                skills[skill.id] = skill;
            }
            if (skills.length > 0) {
                skills.forEach( skill => {
                    var li = document.createElement('li');
                    var radio = document.createElement('input');
                    radio.id = 'skill' + skill.id;
                    radio.value = skill.id;
                    radio.setAttribute('type', 'radio');
                    radio.setAttribute('name', 'skill');
                    radio.classList.add('hidden','peer');
                    li.appendChild(radio);
                    var label = document.createElement('label');
                    label.setAttribute('for', 'skill' + skill.id);
                    label.classList.add('inline-block','w-full','p-2','text-gray-500','bg-white','rounded-lg','border-2','border-gray-200','cursor-pointer','peer-checked:border-blue-600','hover:text-teal-600','peer-checked:text-blue-600','hover:bg-teal-50');
                    var name = document.createElement('div');
                    name.classList.add('inline-block','w-auto','text-base');
                    name.innerHTML = skill.name;
                    label.appendChild(name);
                    var cost = document.createElement('div');
                    cost.classList.add('inline-block','w-16','text-base');
                    cost.innerHTML = '-' + skill.cost_mp + 'MP';
                    label.appendChild(cost);
                    if (skill.ap > 0) {
                        var ap = document.createElement('div');
                        ap.classList.add('inline-block','w-16','text-base');
                        ap.innerHTML = skill.ap + 'AP';
                        label.appendChild(ap);
                    }
                    if (skill.xp > 0) {
                        var xp = document.createElement('div');
                        xp.classList.add('inline-block','w-16','text-base');
                        xp.innerHTML = skill.xp + 'XP';
                        label.appendChild(xp);
                    }
                    if (skill.gp > 0) {
                        var gp = document.createElement('div');
                        gp.classList.add('inline-block','w-16','text-base');
                        gp.innerHTML = skill.gp + 'GP';
                        label.appendChild(gp);
                    }
                    var help = document.createElement('div');
                    help.classList.add('inline-block','w-96','text-sm');
                    help.innerHTML = skill.description;
                    label.appendChild(help);
                    li.appendChild(label);
                    ul.appendChild(li);
                });
            } else {
                ul.innerHTML = '沒有可用的技能！';
            }
            skillsModal.show();
        });
    }

    function prepare_item() {
        var ul = document.getElementById('itemList');
        ul.innerHTML = '';
        window.axios.post('{{ route('game.get_myitems') }}', {
            uuid: character,
            kind: target_type,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            items = [];
            for (var k in response.data.items) {
                var item = response.data.items[k];
                items[item.id] = item;
            }
            if (items.length > 0) {
                items.forEach( item => {
                    var li = document.createElement('li');
                    var radio = document.createElement('input');
                    radio.id = 'bag' + item.id;
                    radio.value = item.id;
                    radio.setAttribute('type', 'radio');
                    radio.setAttribute('name', 'item');
                    radio.classList.add('hidden','peer');
                    li.appendChild(radio);
                    var label = document.createElement('label');
                    label.setAttribute('for', 'bag' + item.id);
                    label.classList.add('inline-block','w-full','p-2','text-gray-500','bg-white','rounded-lg','border-2','border-gray-200','cursor-pointer','peer-checked:border-blue-600','hover:text-teal-600','peer-checked:text-blue-600','hover:bg-teal-50');
                    var name = document.createElement('div');
                    name.classList.add('inline-block','w-auto','text-base');
                    name.innerHTML = item.name;
                    label.appendChild(name);
                    var quantity = document.createElement('div');
                    quantity.classList.add('inline-block','w-16','text-base','pl-4');
                    quantity.innerHTML = item.pivot.quantity + '個';
                    label.appendChild(quantity);
                    if (item.hp > 0) {
                        var hp = document.createElement('div');
                        hp.classList.add('inline-block','w-16','text-base','pl-4');
                        hp.innerHTML = item.hp + 'HP';
                        label.appendChild(hp);
                    }
                    if (item.mp > 0) {
                        var mp = document.createElement('div');
                        mp.classList.add('inline-block','w-16','text-base','pl-4');
                        mp.innerHTML = item.mp + 'MP';
                        label.appendChild(mp);
                    }
                    if (item.ap > 0) {
                        var ap = document.createElement('div');
                        ap.classList.add('inline-block','w-16','text-base','pl-4');
                        ap.innerHTML = item.ap + 'AP';
                        label.appendChild(ap);
                    }
                    if (item.dp > 0) {
                        var dp = document.createElement('div');
                        dp.classList.add('inline-block','w-16','text-base','pl-4');
                        dp.innerHTML = item.dp + 'DP';
                        label.appendChild(dp);
                    }
                    if (item.sp > 0) {
                        var sp = document.createElement('div');
                        sp.classList.add('inline-block','w-16','text-base','pl-4');
                        sp.innerHTML = item.sp + 'SP';
                        label.appendChild(sp);
                    }
                    var help = document.createElement('div');
                    help.classList.add('inline-block','w-full','text-sm');
                    help.innerHTML = item.description;
                    label.appendChild(help);
                    li.appendChild(label);
                    ul.appendChild(li);
                });
            } else {
                ul.innerHTML = '沒有任何道具！';
            }
            itemsModal.show();
        });
    }

    function skill_cast() {
        var skill_obj = document.querySelector('input[name="skill"]:checked');
        if (skill_obj == null) {
            var msg = document.getElementById('info');
            msg.innerHTML = '您尚未選擇技能！';
            warnModal.show();
            return;
        }
        skillsModal.hide();
        data_type = '';
        data_skill = skill_obj.value;
        var data_inspire = skills[data_skill].inspire;
        if (data_inspire == 'throw') {
            data_type = 'skill_then_item';
            prepare_item();
            return;
        } else {
            window.axios.post('{{ route('game.skill_cast') }}', {
                self: character,
                target: target,
                skill: data_skill,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            done = true;
        }
    }

    function item_use() {
        var item_obj = document.querySelector('input[name="item"]:checked');
        if (item_obj == null) {
            var msg = document.getElementById('info');
            msg.innerHTML = '您尚未選擇道具！';
            warnModal.show();
            return;
        }
        itemsModal.hide();
        data_item = item_obj.value;
        if (data_type == 'skill_then_item') {
            window.axios.post('{{ route('game.skill_cast') }}', {
                self: character,
                target: target,
                skill: data_skill,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            data_type = '';
        } else {
            window.axios.post('{{ route('game.item_use') }}', {
                self: character,
                target: target,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
        done = true;
    }
</script>
@endsection
