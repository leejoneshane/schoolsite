@extends('layouts.game')

@section('content')
<div class="w-full h-screen flex flex-col justify-between">
    <div class="w-full h-screen flex-initial">
        <div class="relative h-full flex flex-row">
            <div id="me" class="w-1/3 inline-flex content-end">
                <div class="m-2 flex flex-col gap-1">
                    <div class="w-24 h-8 text-white text-xl font-extrabold" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;">{{ $character->name }}</div>
                    <div class="w-24 h-4 bg-gray-200 rounded-full leading-none">
                        <div id="hp" class="h-4 bg-green-500 text-xs font-medium text-green-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($character->hp / $character->max_hp * 100) }}%;">{{ $character->hp }}</div>
                    </div>
                    <div class="w-24 h-4 bg-gray-200 rounded-full leading-none">
                        <div id="mp" class="h-4 bg-blue-500 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($character->mp / $character->max_mp * 100) }}%;">{{ $character->mp }}</div>
                    </div>
                    <div id="status" class="w-24 h-8 text-white" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;">正常</div>
                    <img title="{{ $character->name }}" src="{{ $character->url ?: '' }}" class="absolute bottom-40 w-1/3 z-50">
                </div>
            </div>
            <div class="w-1/3 text-center inline-flex flex-col">
                <h1 id="caption" class="hidden text-white text-3xl" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;"></h1>
                <div id="help" class="p-2">
                    <ul class="text-left text-white" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;">
                        <li>地下城遊戲規則：</li>
                        <li>1. 先選擇要進入的地下城。</li>
                        <li>2. 你有 30 秒的時間回答問題，超過時間怪物會採取行動。</li>
                        <li>3. 回答正確的話，你可以採取行動，錯誤的話，怪物會採取行動。</li>
                        <li>4. 擊敗怪物可以獲得經驗值或金幣。</li>
                        <li>5. 角色死亡或通過關卡將自動結束冒險。</li>
                    </ul>
                </div>
                <div id="dungeons" class="p-2">
                    @if ($dungeons->count() > 0)
                    <button onclick="dungeonModal.show();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        挑選要進入的地下城！
                    </button>
                    @else
                        沒有可以進入的地下城！
                    @endif
                </div>
                <div id="continue" class="hidden p-2">
                    <button onclick="show_question();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        繼續前進！
                    </button>
                </div>
                <div id="fight" class="hidden p-2 text-left bg-white bg-opacity-50">                 
                </div>
            </div>
            <div class="w-1/3 inline-flex content-end">
                <div id="monster" class="hidden m-2 flex flex-col gap-1">
                    <div id="monster_name" class="w-24 h-8 text-white text-xl font-extrabold" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;"></div>
                    <div class="w-24 h-4 bg-gray-200 rounded-full leading-none">
                        <div id="monster_hp" class="h-4 bg-green-500 text-xs font-medium text-green-100 text-center p-0.5 leading-none rounded-full" style="width: 100%;"></div>
                    </div>
                    <div id="monster_status" class="w-24 h-8 text-white" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;"></div>
                    <img id="monster_img" title="" src="" class="absolute bottom-40 w-1/3 z-50">
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
                <ul class="text-left">
                @foreach ($dungeons as $d)
                <li>
                    <input id="dungeon{{ $d->id }}" type="radio" name="dungeon" value="{{ $d->id }}" class="hidden peer" />
                    <label for="dungeon{{ $d->id }}" class="inline-block w-full p-2 {{ $d->monster->style }} bg-white border-2 border-gray-200 cursor-pointer peer-checked:border-blue-600 hover:bg-gray-50">
                        <span class="inline-block text-normal w-48">{{ $d->title }}</span>
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
                <button onclick="dungeonModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    我再想想！
                </button>
            </div>
        </div>
    </div>
</div>
<div id="questionModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">請回答以下問題：</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <label id="question" class="text-xl"></label>
                <ul id="options" class="text-lg">
                </ul>
            </div>
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 id="seconds" class="text-center text-5xl font-semibold text-red-500"></h3>
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
    var character = {!! $character->toJson(JSON_UNESCAPED_UNICODE) !!};
    var dungeon; //dungeon object
    var monster; //spawn object
    var no = 0; //question index
    var questions = [];
    var options = [];
    var answer;
    var skills = [];
    var items =[];
    var target_type; //self or monster
    var data_type; //skill or item
    var data_skill; //skill id
    var data_item; //item id
    var targetSeconds = 0;
    var timerId;
    var startTime;
    var remainingTime;

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-dungeon');
    var $targetEl = document.getElementById('warnModal');
    const warnModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('dungeonModal');
    const dungeonModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('questionModal');
    const questionModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('actionModal');
    const actionModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('skillsModal');
    const skillsModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('itemsModal');
    const itemsModal = new window.Modal($targetEl);
    var tictok = document.getElementById('seconds');
    window.onbeforeunload = exit;
    
    function exit(event) {
        fetch('{{ route('game.exit_dungeon') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            return response.data.success;
        });
    }

    function enter() {
        var node = document.querySelector('input[name="dungeon"]:checked');
        if (node == null) {
            var msg = document.getElementById('info');
            msg.innerHTML = '您尚未選擇地下城！';
            warnModal.show();
            return;
        }
        var myid = node.value;
        window.axios.post('{{ route('game.enter_dungeon') }}', {
            dungeon_id: myid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            dungeon = response.data.dungeon;
            var caption = document.getElementById('caption');
            caption.innerHTML = dungeon.title;
            caption.classList.remove('hidden');
            var node = document.getElementById('fight');
            node.classList.remove('hidden');
            node = document.getElementById('dungeons');
            node.classList.add('hidden');
            questions = [];
            no = 0;
            for (var k in response.data.questions) {
                var question = response.data.questions[k];
                questions[k] = question;
                options[k] = question.selection;
            }
            monster = response.data.monster;
            var myname = document.getElementById('monster_name');
            myname.innerHTML = monster.name;
            var hp = document.getElementById('monster_hp');
            hp.style.width = Math.round(monster.hp / monster.max_hp * 100) + '%';
            hp.innerHTML = monster.hp;
            var img = document.getElementById('monster_img');
            img.setAttribute('title', monster.name);
            img.setAttribute('src', monster.url);
            var status = document.getElementById('monster_status');
            status.innerHTML = monster.status;
            var mon = document.getElementById('monster');
            mon.classList.remove('hidden');
            var fight = document.getElementById('continue');
            fight.classList.remove('hidden');
            var fight = document.getElementById('fight');
            fight.classList.remove('hidden');
            var div = document.createElement('div');
            div.classList.add('w-full','text-xs');
            div.innerHTML = monster.name + '前來挑戰你！';
            fight.appendChild(div);
            show_question();
        });
        dungeonModal.hide();
    }

    function show_question() {
        answer = questions[no].answer;
        var node = document.getElementById('question');
        node.innerHTML = questions[no].question;
        var nodes = document.getElementById('options');
        nodes.innerHTML = '';
        for (var k in questions[no].selection) {
            var li = document.createElement('li');
            var box = document.createElement('button');
            box.setAttribute('id', 'option' + questions[no].selection[k].id);
            box.setAttribute('type', 'button');
            box.setAttribute('value', questions[no].selection[k].id);
            box.setAttribute('onclick', "check(this); questionModal.hide();");
            box.classList.add('w-full','p-2','border-2','border-gray-200','bg-white','hover:bg-blue-100');
            box.innerHTML = questions[no].selection[k].option;
            li.appendChild(box);
            nodes.appendChild(li);
        }
        no++;
        questionModal.show();
        start();
    }

    function start() {
        targetSeconds = 30;
        startTime = new Date().getTime();
        update(targetSeconds);
        timerId = setInterval(timer, 1000);
    }

    function stop() {
        clearInterval(timerId);
    }

    function timer() {
        var currentTime = new Date().getTime();
        var diffSec = Math.round((currentTime - startTime) / 1000);
        remainingTime = targetSeconds - diffSec;
        update(remainingTime);
        if (remainingTime <= 0) {
            clearInterval(timerId);
            update(0);
            questionModal.hide();
            monster_attack();
        }
    }

    function update (seconds) {
        tictok.innerHTML = seconds;
    }

    function syncDelay(milliseconds) {
        var start = new Date().getTime();
        var end = 0;
        while ((end - start) < milliseconds) {
            end = new Date().getTime();
        }
    }

    function check(node) {
        stop();
        if (answer == node.value) {
            action();
        } else {
            monster_attack();
        }
    }

    function monster_attack() {
        window.axios.post('{{ route('game.monster_attack') }}', {
            spawn_id: monster.id,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            var mskill = response.data.skill;
            var fight = document.getElementById('fight');
            var div = document.createElement('div');
            div.classList.add('w-full','text-xs');
            if (response.data.result == 5) {
                div.innerHTML = monster.name + '對你施展' + mskill.name + '，未命中!';
            } else {
                div.innerHTML = monster.name + '對你施展' + mskill.name + '!';
            }
            fight.appendChild(div);
            character = response.data.character;
            var hp = document.getElementById('hp');
            hp.style.width = Math.round(character.hp / character.max_hp * 100) + '%';
            hp.innerHTML = character.hp;
            var mp = document.getElementById('mp');
            mp.style.width = Math.round(character.mp / character.max_mp * 100) + '%';
            mp.innerHTML = character.mp;
            var status = document.getElementById('status');
            status.innerHTML = character.status_desc;
            monster = response.data.monster;
            var myname = document.getElementById('monster_name');
            myname.innerHTML = monster.name;
            hp = document.getElementById('monster_hp');
            hp.style.width = Math.round(monster.hp / monster.max_hp * 100) + '%';
            hp.innerHTML = monster.hp;
            if (monster.buff == 'escape') {
                div = document.createElement('div');
                div.classList.add('w-full','text-xs');
                div.innerHTML = monster.name + '已經逃跑離開戰鬥現場！';
                fight.appendChild(div);
                monster_respawn();
            } else if (monster.hp < 1) {
                monster_respawn();
            }
        });
    }

    function monster_respawn() {
        window.axios.post('{{ route('game.monster_respawn') }}', {
            monster_id: monster.monster_id,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            monster = response.data.monster;
            var myname = document.getElementById('monster_name');
            myname.innerHTML = monster.name;
            var hp = document.getElementById('monster_hp');
            hp.style.width = Math.round(monster.hp / monster.max_hp * 100) + '%';
            hp.innerHTML = monster.hp;
            var img = document.getElementById('monster_img');
            img.setAttribute('title', monster.name);
            img.setAttribute('src', monster.url);
            var status = document.getElementById('monster_status');
            status.innerHTML = monster.status;
            var mon = document.getElementById('monster');
            mon.classList.remove('hidden');
            var fight = document.getElementById('fight');
            fight.classList.remove('hidden');
            var div = document.createElement('div');
            div.classList.add('w-full','text-xs');
            div.innerHTML = monster.name + '前來挑戰你！';
            fight.appendChild(div);
            show_question();
        });
    }

    function action() {
        target = character;
        target_type = 'self';
        var msg = document.getElementById('action_target');
        msg.innerHTML = '要施展技能或使用道具？';
        actionModal.show();
    }

    function prepare_skill() {
        var ul = document.getElementById('skillList');
        ul.innerHTML = '';
        window.axios.post('{{ route('game.get_myskills') }}', {
            uuid: character.uuid,
            kind: 'monster',
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
            uuid: character.uuid,
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
            target_type = 'enemy';
            prepare_item();
            return;
        } else {
            window.axios.post('{{ route('game.skill_cast') }}', {
                self: character.uuid,
                target: target,
                skill: data_skill,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
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
                self: character.uuid,
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
    }
</script>
@endsection
