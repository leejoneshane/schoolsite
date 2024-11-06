@extends('layouts.game')

@section('content')
<div class="w-full flex flex-row justify-between">
    <div class="w-48">
        <table class="w-full bg-white text-left font-normal">
            <tr>
                <td colspan="2" class="bg-gray-500 text-white text-center">基本資料</td>
            </tr>
            <tr>
                <td colspan="2" class="p-2">{{ $character->seat }} {{ $character->name }}</td>
            </tr>
            <tr>
                <td class="w-16 p-2">稱號</td><td>{{ $character->title ?: '無' }}</td>
            </tr>
            <tr>
                <td class="w-16 p-2">職業</td>
                <td>{{ $character->profession->name }}</td>
            </tr>
            <tr>
                <td class="w-16 p-2">等級</td><td id="level">{{ $character->level }}</td>
            </tr>
            <tr>
                <td class="w-16 p-2">HP</td>
                <td>
                    <div class="w-full h-4 bg-gray-200 rounded-full dark:bg-gray-700 text-right leading-none text-xs font-medium">
                        <div id="hp" class="h-4 bg-green-600 text-xs font-medium text-green-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($character->hp / $character->max_hp * 100) }}%">{{ $character->hp }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-16 p-2">MP</td>
                <td>
                    <div class="w-full h-4 bg-gray-200 rounded-full dark:bg-gray-700 text-right leading-none text-xs font-medium">
                        <div id="mp" class="h-4 bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($character->mp / $character->max_mp * 100) }}%">{{ $character->mp }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-16 p-2">AP</td><td><span id="final_ap" class="text-red-500">{{ $character->final_ap }}</span>[<span id="ap" class="text-blue-500">{{ $character->ap }}</span>]</td>
            </tr>
            <tr>
                <td class="w-16 p-2">DP</td><td><span id="final_dp" class="text-red-500">{{ $character->final_dp }}</span>[<span id="dp" class="text-blue-500">{{ $character->dp }}</span>]</td>
            </tr>
            <tr>
                <td class="w-16 p-2">SP</td><td><span id="final_sp" class="text-red-500">{{ $character->final_sp }}</span>[<span id="sp" class="text-blue-500">{{ $character->sp }}</span>]</td>
            </tr>
            <tr>
                <td class="w-16 p-2">狀態</td><td><span id="status" class="text-blue-500">{{ $character->status_desc }}</span></td>
            </tr>
            <tr>
                <td class="w-16 p-2">XP</td><td><span id="xp" class="text-lime-500">{{ $character->xp }}</span></td>
            </tr>
            <tr>
                <td class="w-16 p-2">GP</td><td><span id="gp" class="text-lime-500">{{ $character->gp }}</span></td>
            </tr>
        </table>
    </div>
    <div class="bg-white bg-opacity-50">
        <table class="w-72 h-auto">
                <tr>
                    <td colspan="3" class="bg-gray-500 text-white text-center">背包</td>
                </tr>
                @for ($i=0; $i<5; $i++)
                <tr class="h-24">
                    @for ($j=0; $j<3; $j++)
                    <td id="bag{{ $j + $i * 3 }}" class="w-24"></td>
                    @endfor
                </tr>
                @endfor
                <tr>
                    <td colspan="3" class="h-8"></td>
                </tr>
            </tbody>
        </table>
    </div>
    @if ($configure && $configure->change_class)
    <button class="absolute z-[55] w-16 h-12 left-1/3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" onclick="change_class();">轉職</button>
    @endif
    <div class="w-1/3 inline-flex flex-row justify-center">
        <img src="{{ $character->image->url() }}" id="big" class="relative w-auto h-full z-50" />
    </div>
    <div class="w-1/3">
        <div class="bg-gray-500 text-white text-center">技能書</div>
        <table class="w-full text-left font-normal bg-game-book bg-cover">
            <tbody class="bg-white bg-opacity-50">
                <tr>
                    <td colspan="3" class="h-8"></td>
                </tr>
                @foreach ($character->profession->skills as $skill)
                <tr class="text-lg h-12">
                    <td class="w-8">{{ $skill->pivot->level }}</td>
                    @if ($skill->passive && $skill->pivot->level <= $character->level && $skill->cost_mp <= $character->mp)
                    <td class="w-32">
                        <button class="bg-amber-500 hover:bg-amber-700 text-white font-bold rounded-full py-2 px-4" onclick="skill_cast({{ $skill->id }});"> {{ $skill->name }} </button>
                    </td>
                    @else
                    <td class="w-32">{{ $skill->name }}</td>
                    @endif
                    <td class="w-auto text-xs">{{ $skill->description }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="h-10"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div id="itemsModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
<div id="teammateModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[70] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">對象</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul id="memberList" >
                    @forelse ($character->members() as $m)
                    <li>
                        <input id="team{{ $m->uuid }}" type="radio" name="teammate" value="{{ $m->uuid }}" class="hidden peer">
                        <label for="team{{ $m->uuid }}" class="inline-block w-full w-full p-2 text-gray-500 bg-white border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <div class="inline-block w-16 text-base">{{ $m->seat }}</div>
                            <div class="inline-block w-48 text-base">{{ $m->name }}</div>
                        </label>
                    </li>
                    @empty
                    <li>
                        尚未加入任何公會！
                    </li>
                    @endforelse
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="cast();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即使用
                </button>
                <button onclick="teamModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var character = {!! $character->toJson(JSON_UNESCAPED_UNICODE) !!};
    var data_type;
    var data_skill;
    var data_item;
    var skills = [];
    @foreach ($character->passive_skills() as $skill)
    skills[{{ $skill->id }}] = {!! $skill->toJson(JSON_UNESCAPED_UNICODE) !!};
    @endforeach
    var items = [];
    window.onload = bag;

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-door');
    var $targetEl = document.getElementById('itemsModal');
    const itemsModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('teammateModal');
    const teamModal = new window.Modal($targetEl);

    function bag() {
        for (i=0; i<15; i++) {
            var node = document.getElementById('bag' + i);
            node.innerHTML = '';
        }
        window.axios.post('{{ route('game.get_myitems') }}', {
            uuid: character.uuid,
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
            var seq = 0;
            items.forEach( (item) => {
                var node = document.getElementById('bag' + seq);
                if (item.passive == 1 && character.status != 'DEAD' && character.status != 'COMA') {
                    var btn = document.createElement('button');
                    btn.setAttribute('type', 'button');
                    btn.classList.add('relative','inline-flex','items-center','p-3','text-sm','font-medium','text-center','text-white','rounded-lg','hover:bg-blue-800','focus:ring-4','focus:outline-none','focus:ring-blue-300',);
                    btn.setAttribute('onclick', 'item_use(' + item.id + ')');
                    var image = document.createElement('img');
                    image.src = '{{ asset(GAME_ITEM) }}/' + item.image_file;
                    image.setAttribute('title', item.name);
                    btn.appendChild(image);
                    var badge = document.createElement('div');
                    badge.classList.add('absolute','inline-flex','items-center','justify-center','w-6','h-6','text-xs','font-bold','text-white','bg-red-500','border-2','border-white','rounded-full','top-0','end-0','dark:border-gray-900');
                    badge.innerHTML = item.pivot.quantity;
                    btn.appendChild(badge);
                    node.appendChild(btn);
                } else {
                    var btn = document.createElement('div');
                    btn.classList.add('relative','inline-flex','items-center','p-3','text-sm','font-medium','text-center','text-white','rounded-lg');
                    var image = document.createElement('img');
                    image.src = '{{ asset(GAME_ITEM) }}/' + item.image_file;
                    image.setAttribute('title', item.name);
                    btn.appendChild(image);
                    var badge = document.createElement('div');
                    badge.classList.add('absolute','inline-flex','items-center','justify-center','w-6','h-6','text-xs','font-bold','text-white','bg-red-500','border-2','border-white','rounded-full','top-0','end-0','dark:border-gray-900');
                    badge.innerHTML = item.pivot.quantity;
                    btn.appendChild(badge);
                    node.appendChild(btn);
                }
                seq ++;
            });
        });
    }

    function skill_cast(id) {
        data_skill = id;
        data_type = 'skill';
        var data_target = skills[id].object;
        var data_inspire = skills[id].inspire;
        if (data_inspire == 'throw') {
            data_type = 'skill_then_item';
            prepare_item();
            return;
        }
        if (data_target == 'partner') {
            teamModal.show();
        } else {
            window.axios.post('{{ route('game.myskill_cast') }}', {
                self: character.uuid,
                target: character.uuid,
                skill: data_skill,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            window.location.reload();
        }
    }

    function prepare_item() {
        var ul = document.getElementById('itemList');
        ul.innerHTML = '';
        if (data_type == 'skill_then_item') {
            data_type = 'item_after_skill';
        }
        window.axios.post('{{ route('game.get_items') }}', {
            uuid: character.uuid,
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

    function skill_then_item() {
        var item_obj = document.querySelector('input[name="item"]:checked');
        if (item_obj == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇道具！';
            warnModal.show();
            return;
        }
        itemsModal.hide();
        data_item = item_obj.value;
        teamModal.show();
    }

    function item_use(id) {
        data_item = id;
        data_type = 'item';
        var data_target = items[id].object;
        if (data_target == 'partner') {
            teamModal.show();
        } else {
            window.axios.post('{{ route('game.myitem_use') }}', {
                self: character.uuid,
                target: character.uuid,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                character = response.data.character;
                var node = document.getElementById('level');
                node.innerHTML = character.level;
                var hp = document.getElementById('hp');
                hp.style.width = Math.round(character.hp / character.max_hp * 100) + '%';
                hp.innerHTML = character.hp;
                var mp = document.getElementById('mp');
                mp.style.width = Math.round(character.mp / character.max_mp * 100) + '%';
                mp.innerHTML = character.mp;
                var ap = document.getElementById('final_ap');
                ap.innerHTML = character.final_ap;
                var ap = document.getElementById('ap');
                ap.innerHTML = character.ap;
                var dp = document.getElementById('final_dp');
                dp.innerHTML = character.final_dp;
                var dp = document.getElementById('dp');
                dp.innerHTML = character.dp;
                var sp = document.getElementById('final_sp');
                sp.innerHTML = character.final_sp;
                var sp = document.getElementById('sp');
                sp.innerHTML = character.sp;
                var status = document.getElementById('status');
                status.innerHTML = character.status_desc;
                var xp = document.getElementById('xp');
                xp.innerHTML = character.xp;
                var gp = document.getElementById('gp');
                gp.innerHTML = character.gp;
                bag();
            });
        }
    }

    function cast() {
        if (data_type == 'skill') {
            var obj = document.querySelector('input[name="teammate"]:checked');
            if (obj == null) {
                var msg = document.getElementById('message');
                msg.innerHTML = '您尚未選擇技能施展對象！';
                warnModal.show();
                return;
            }
            teamModal.hide();
            window.axios.post('{{ route('game.myskill_cast') }}', {
                self: character.uuid,
                target: obj.value,
                skill: data_skill,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                character = response.data.character;
                var node = document.getElementById('level');
                node.innerHTML = character.level;
                var hp = document.getElementById('hp');
                hp.style.width = Math.round(character.hp / character.max_hp * 100) + '%';
                hp.innerHTML = character.hp;
                var mp = document.getElementById('mp');
                mp.style.width = Math.round(character.mp / character.max_mp * 100) + '%';
                mp.innerHTML = character.mp;
                var ap = document.getElementById('final_ap');
                ap.innerHTML = character.final_ap;
                var ap = document.getElementById('ap');
                ap.innerHTML = character.ap;
                var dp = document.getElementById('final_dp');
                dp.innerHTML = character.final_dp;
                var dp = document.getElementById('dp');
                dp.innerHTML = character.dp;
                var sp = document.getElementById('final_sp');
                sp.innerHTML = character.final_sp;
                var sp = document.getElementById('sp');
                sp.innerHTML = character.sp;
                var status = document.getElementById('status');
                status.innerHTML = character.status_desc;
                var xp = document.getElementById('xp');
                xp.innerHTML = character.xp;
                var gp = document.getElementById('gp');
                gp.innerHTML = character.gp;
            });
        }
        if (data_type == 'item_after_skill') {
            var obj = document.querySelector('input[name="teammate"]:checked');
            if (obj == null) {
                var msg = document.getElementById('message');
                msg.innerHTML = '您尚未選擇技能施展對象！';
                warnModal.show();
                return;
            }
            teamModal.hide();
            window.axios.post('{{ route('game.myskill_cast') }}', {
                self: character.uuid,
                target: obj.value,
                skill: data_skill,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                character = response.data.character;
                var node = document.getElementById('level');
                node.innerHTML = character.level;
                var hp = document.getElementById('hp');
                hp.style.width = Math.round(character.hp / character.max_hp * 100) + '%';
                hp.innerHTML = character.hp;
                var mp = document.getElementById('mp');
                mp.style.width = Math.round(character.mp / character.max_mp * 100) + '%';
                mp.innerHTML = character.mp;
                var ap = document.getElementById('final_ap');
                ap.innerHTML = character.final_ap;
                var ap = document.getElementById('ap');
                ap.innerHTML = character.ap;
                var dp = document.getElementById('final_dp');
                dp.innerHTML = character.final_dp;
                var dp = document.getElementById('dp');
                dp.innerHTML = character.dp;
                var sp = document.getElementById('final_sp');
                sp.innerHTML = character.final_sp;
                var sp = document.getElementById('sp');
                sp.innerHTML = character.sp;
                var status = document.getElementById('status');
                status.innerHTML = character.status_desc;
                var xp = document.getElementById('xp');
                xp.innerHTML = character.xp;
                var gp = document.getElementById('gp');
                gp.innerHTML = character.gp;
                bag();
            });
        }
        if (data_type == 'item') {
            var obj = document.querySelector('input[name="teammate"]:checked');
            if (obj == null) {
                var msg = document.getElementById('message');
                msg.innerHTML = '您尚未選擇道具使用對象！';
                warnModal.show();
                return;
            }
            teamModal.hide();
            window.axios.post('{{ route('game.myitem_use') }}', {
                self: character.uuid,
                target: obj.value,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                character = response.data.character;
                var node = document.getElementById('level');
                node.innerHTML = character.level;
                var hp = document.getElementById('hp');
                hp.style.width = Math.round(character.hp / character.max_hp * 100) + '%';
                hp.innerHTML = character.hp;
                var mp = document.getElementById('mp');
                mp.style.width = Math.round(character.mp / character.max_mp * 100) + '%';
                mp.innerHTML = character.mp;
                var ap = document.getElementById('final_ap');
                ap.innerHTML = character.final_ap;
                var ap = document.getElementById('ap');
                ap.innerHTML = character.ap;
                var dp = document.getElementById('final_dp');
                dp.innerHTML = character.final_dp;
                var dp = document.getElementById('dp');
                dp.innerHTML = character.dp;
                var sp = document.getElementById('final_sp');
                sp.innerHTML = character.final_sp;
                var sp = document.getElementById('sp');
                sp.innerHTML = character.sp;
                var status = document.getElementById('status');
                status.innerHTML = character.status_desc;
                var xp = document.getElementById('xp');
                xp.innerHTML = character.xp;
                var gp = document.getElementById('gp');
                gp.innerHTML = character.gp;
                bag();
            });
        }
    }

    function change_class() {
        window.location = '{{ route('game.player_profession') }}';
    }
</script>
@endsection
