@extends('layouts.game')

@section('content')
<div class="w-full h-screen flex flex-col justify-between">
    <div id="message" class="w-full h-1/3 flex-initial">
        <div class="relative h-full flex flex-row">
            <div class="w-1/3">
                <ul id="our_action"></ul>
            </div>
            <div id="connect" class="w-1/3 text-center inline-flex flex-col" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;">
            @if ($character->configure && $character->configure->arena_open)
                <div class="p-2">
                    <span class="text-xl text-white">等候公會成員集合......</span>
                    @if ($character->is_leader())
                    <button onclick="ring_bell();" class="m-2 w-40 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                        發送集合通知
                    </button>
                    @endif
                </div>
                @if ($character->is_leader())
                <div class="p-2">
                    <select id="parties" class="w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                    </select>
                    <button onclick="invite();" class="w-40 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                        送出對戰邀請
                    </button>
                </div>
                @endif
            @else
            <div class="p-2">
                <span class="text-xl text-white">很抱歉，場地清潔中，暫不開放！</span>
            </div>
            @endif
            </div>
            <div class="w-1/3">
                <ul id="enemy_action"></ul>
            </div>
        </div>
    </div>
    <div class="w-full h-2/3 flex-initial">
        <div class="relative h-full flex flex-row">
            <div id="our_side" class="w-1/3 inline-flex content-end">
            </div>
            <div class="w-1/3 text-center">
                <div id="action" class="hidden p-2">
                    在每場戰鬥中，每人只能進行一項動作，請與隊友討論策略。決定好之後，請點選隊友或對手，然後挑選要進行的動作！
                </div>
            </div>
            <div id="enemy_side" class="w-1/3 inline-flex content-end">
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
<div id="confirmModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">對戰邀請</h3>
            </div>
            <div id="message" class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
            </div>
            <div class="w-full inline-flex justify-center gap-4 p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="agree();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    同意
                </button>
                <button onclick="disgree(); confirmModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    拒絕
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
            <div class="w-full inline-flex justify-center gap-4 p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
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
            <div class="w-full inline-flex justify-center gap-4 p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
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
            <div class="w-full inline-flex justify-center gap-4 p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
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
    var ls_leader = {{ $character->is_leader() }};
    var enemy_party = '';
    var invite_from;
    var member_count = {{ $character->party->members->count() }};
    var members = [];
    var parties = [];
    var enemys = [];
    var skills = [];
    var items =[];
    var target; // uuid
    var target_type; //self or enemy or partner
    var data_type; //skill or item
    var data_skill; //skill id
    var data_item; //item id
    var done = false;

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-arena');
    var $targetEl = document.getElementById('warnModal');
    const warnModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('confirmModal');
    const confirmModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('actionModal');
    const actionModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('skillsModal');
    const skillsModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('itemsModal');
    const itemsModal = new window.Modal($targetEl);
    var our_side = document.getElementById('our_side');
    var enemy_side = document.getElementById('enemy_side');
    var our_action = document.getElementById('our_action');
    var enemy_action = document.getElementById('enemy_action');
    var party_node = document.getElementById('parties');
    window.onload = refresh;
@if ($character->configure && $character->configure->arena_open)
    setInterval(refresh, 3000);
@endif

    function refresh() {
        window.axios.post('{{ route('game.refresh_arena') }}', {
            uuid: character,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            members = [];
            for (var k in response.data.characters) {
                var member = response.data.characters[k];
                members[k] = member;
            }
            if (members.length > 0) {
                our_side.innerHTML = '';
                var z = 50;
                members.forEach( member => {
                    var div = document.createElement('div');
                    div.classList.add('m-2','flex', 'flex-col','gap-1');
                    var myname = document.createElement('div');
                    myname.classList.add('w-24','h-8','font-extrabold');
                    myname.innerHTML = member.name;
                    div.appendChild(myname);
                    var hp = document.createElement('div');
                    hp.classList.add('w-24','h-4','bg-gray-200','rounded-full','leading-none');
                    var hp_bar = document.createElement('div');
                    hp_bar.classList.add('h-4','bg-green-600','text-xs','font-medium','text-green-100','text-center','p-0.5','leading-none','rounded-full');
                    hp_bar.style.width = Math.round(member.hp / member.max_hp * 100) + '%';
                    hp_bar.innerHTML = member.hp;
                    hp.appendChild(hp_bar);
                    div.appendChild(hp);
                    var mp = document.createElement('div');
                    mp.classList.add('w-24','h-4','bg-gray-200','rounded-full','leading-none');
                    var mp_bar = document.createElement('div');
                    mp_bar.classList.add('h-4','bg-blue-600','text-xs','font-medium','text-blue-100','text-center','p-0.5','leading-none','rounded-full');
                    mp_bar.style.width = Math.round(member.mp / member.max_mp * 100) + '%';
                    mp_bar.innerHTML = member.mp;
                    mp.appendChild(mp_bar);
                    div.appendChild(mp);
                    var status = document.createElement('div');
                    status.classList.add('w-24','h-8');
                    status.innerHTML = member.status_desc;
                    div.appendChild(status);
                    var image = document.createElement('img');
                    image.classList.add('absolute', 'bottom-40', 'w-1/6', 'z-' + z);
                    image.setAttribute('title', member.name);
                    if (member.url) {
                        image.src = member.url;
                    } else {
                        image.src = '{{ asset('images/game/blank.png') }}';
                    }
                    if (member.uuid == character) {
                        image.setAttribute('onclick', 'action_self()');
                    } else {
                        image.setAttribute('onclick', 'action_friend(' + member.uuid + ')');
                    }
                    div.appendChild(image);
                    our_side.appendChild(div);
                    z -= 10;
                });
                if (enemy_party == '' && members.length == member_count) {
                    var node = document.getElementById('connect');
                    node.innerHTML = '組隊完成! 請選擇要對戰的隊伍！';
                }
            }
            if (response.data.enemy) {
                if (enemy_party == '') {
                    var node = document.getElementById('connect');
                    node.innerHTML = '正在與'  + parties[enemy_party].name + '進行對戰！';
                    var node = document.getElementById('action');
                    node.classList.remove('hidden');
                }
                enemy_party = response.data.enemy;
                enemys = [];
                if (response.data.enemys) {
                    for (var k in response.data.enemys) {
                        var member = response.data.enemys[k];
                        enemys[k] = member;
                    }
                    if (enemys.length > 0) {
                        enemy_side.innerHTML = '';
                        var z = 10;
                        enemys.forEach( member => {
                            var div = document.createElement('div');
                            div.classList.add('m-2','flex', 'flex-col','gap-1');
                            var myname = document.createElement('div');
                            myname.classList.add('w-24','h-8','font-extrabold');
                            myname.innerHTML = member.name;
                            div.appendChild(myname);
                            var hp = document.createElement('div');
                            hp.classList.add('w-24','h-4','bg-gray-200','rounded-full','leading-none');
                            var hp_bar = document.createElement('div');
                            hp_bar.classList.add('h-4','bg-green-600','text-xs','font-medium','text-green-100','text-center','p-0.5','leading-none','rounded-full');
                            hp_bar.style.width = Math.round(member.hp / member.max_hp * 100) + '%';
                            hp_bar.innerHTML = member.hp;
                            hp.appendChild(hp_bar);
                            div.appendChild(hp);
                            var mp = document.createElement('div');
                            mp.classList.add('w-24','h-4','bg-gray-200','rounded-full','leading-none');
                            var mp_bar = document.createElement('div');
                            mp_bar.classList.add('h-4','bg-blue-600','text-xs','font-medium','text-blue-100','text-center','p-0.5','leading-none','rounded-full');
                            mp_bar.style.width = Math.round(member.mp / member.max_mp * 100) + '%';
                            mp_bar.innerHTML = member.mp;
                            mp.appendChild(mp_bar);
                            div.appendChild(mp);
                            var status = document.createElement('div');
                            status.classList.add('w-24','h-8');
                            status.innerHTML = member.status_desc;
                            div.appendChild(status);
                            var image = document.createElement('img');
                            image.classList.add('absolute', 'bottom-40', 'w-1/6', 'z-' + z);
                            image.setAttribute('title', member.name);
                            if (member.url) {
                                image.src = member.url;
                            } else {
                                image.src = '{{ asset('images/game/blank.png') }}';
                            }
                            image.setAttribute('onclick', 'action_enemy(' + member.uuid + ')');
                            div.appendChild(image);
                            enemy_side.appendChild(div);
                            z += 10;
                        });
                    }
                }
                if (response.data.our_actions) {
                    for (var k in response.data.our_actions) {
                        var message = response.data.our_actions[k];
                        var li = document.createElement('li');
                        li.classList.add('p-1','leading-relaxed');
                        li.innerHTML = message;
                        our_action.appendChild(li);
                    }
                }
                if (response.data.enemy_actions) {
                    for (var k in response.data.enemy_actions) {
                        var message = response.data.enemy_actions[k];
                        var li = document.createElement('li');
                        li.classList.add('p-1','leading-relaxed');
                        li.innerHTML = message;
                        enemy_action.appendChild(li);
                    }
                }
            } else {
                parties = [];
                for (var k in response.data.parties) {
                    var party = response.data.parties[k];
                    parties[party.id] = party;
                }
                party_node.innerHTML = '';
                if (parties.length > 0) {
                    parties.forEach( party => {
                        var opt = document.createElement('option');
                        opt.value = party.id;
                        opt.innerHTML = party.name;
                        party_node.appendChild(opt);
                    });
                }
            }

        });
    }

    function ring_bell() {
        window.axios.post('{{ route('game.come_arena') }}', {
            uuid: character,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        var node = document.getElementById('connect');
        node.innerHTML = '集合訊息已經發送，繼續等候公會成員集合......';
    }

    function invite() {
        var pid = party_node.value;
        window.axios.post('{{ route('game.invite_battle') }}', {
            uuid: character,
            party: pid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }

    function received_invite(event) {
        if (enemy_party == '') {
            invite_from = event.from.uuid;
            var msg = document.getElementById('message');
            msg.innerHTML = event.from_party.name + '邀請貴公會進行對戰練習！';
            confirmModal.show();
        }
    }

    function agree() {
        window.axios.post('{{ route('game.accept_battle') }}', {
            uuid: character,
            from: invite_from,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            if (response.data.error) {
                var msg = document.getElementById('info');
                msg.innerHTML = response.data.error;
                warnModal.show();
            }
        });
    }

    function dissgree() {
        window.axios.post('{{ route('game.reject_battle') }}', {
            uuid: character,
            from: invite_from,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }

    function accept_invite(event) {
        if (enemy_party == '') {
            var msg = document.getElementById('info');
            msg.innerHTML = event.from_party.name + '已經同意與貴公會進行對戰練習！';
            warnModal.show();
        }
    }

    function reject_invite(event) {
        if (enemy_party == '') {
            var msg = document.getElementById('info');
            msg.innerHTML = event.from_party.name + '已經拒絕與貴公會進行對戰練習！';
            warnModal.show();
        }
    }

    function action_self() {
        if (done) return;
        target = character;
        target_type = 'self';
        var msg = document.getElementById('action_target');
        msg.innerHTML = '要對自己施展技能或使用道具？';
        actionModal.show();
    }

    function action_friend(uuid) {
        if (done) return;
        target = uuid;
        target_type = 'friend';
        var msg = document.getElementById('action_target');
        msg.innerHTML = '要對隊友施展技能或使用道具？';
        actionModal.show();
    }

    function action_enemy(uuid) {
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
                    cost.classList.add('inline-block','w-16','text-base','text-center');
                    cost.innerHTML = '-' + skill.cost_mp + 'MP';
                    label.appendChild(cost);
                    if (skill.ap > 0) {
                        var ap = document.createElement('div');
                        ap.classList.add('inline-block','w-16','text-base','text-center');
                        ap.innerHTML = skill.ap + 'AP';
                        label.appendChild(ap);
                    }
                    if (skill.xp > 0) {
                        var xp = document.createElement('div');
                        xp.classList.add('inline-block','w-16','text-base','text-center');
                        xp.innerHTML = skill.xp + 'XP';
                        label.appendChild(xp);
                    }
                    if (skill.gp > 0) {
                        var gp = document.createElement('div');
                        gp.classList.add('inline-block','w-16','text-base','text-center');
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
                    quantity.classList.add('inline-block','w-16','text-base','text-center');
                    quantity.innerHTML = item.pivot.quantity + '個';
                    label.appendChild(quantity);
                    if (item.hp > 0) {
                        var hp = document.createElement('div');
                        hp.classList.add('inline-block','w-16','text-base','text-center');
                        hp.innerHTML = item.hp + 'HP';
                        label.appendChild(hp);
                    }
                    if (item.mp > 0) {
                        var mp = document.createElement('div');
                        mp.classList.add('inline-block','w-16','text-base','text-center');
                        mp.innerHTML = item.mp + 'MP';
                        label.appendChild(mp);
                    }
                    if (item.ap > 0) {
                        var ap = document.createElement('div');
                        ap.classList.add('inline-block','w-16','text-base','text-center');
                        ap.innerHTML = item.ap + 'AP';
                        label.appendChild(ap);
                    }
                    if (item.dp > 0) {
                        var dp = document.createElement('div');
                        dp.classList.add('inline-block','w-16','text-base','text-center');
                        dp.innerHTML = item.dp + 'DP';
                        label.appendChild(dp);
                    }
                    if (item.sp > 0) {
                        var sp = document.createElement('div');
                        sp.classList.add('inline-block','w-16','text-base','text-center');
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
