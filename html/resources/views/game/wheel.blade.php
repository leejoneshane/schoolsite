@extends('layouts.game')

@section('content')
<div class="w-full h-screen flex">
    <div class="w-1/2 items-center flex flex-col">
        <div class="text-lg text-white sm:block drop-shadow-lg">
            <label for="choise" class="ml-3 cursor-pointer">抽選角色</label>
            <label for="choise" class="relative align-center inline-flex items-center cursor-pointer">
                <input type="checkbox" id="choise" name="choise" value="yes" class="sr-only peer" onchange="change();">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            </label>
            <label for="choise" class="ml-3 cursor-pointer">抽選公會</label>
        </div>
        <img id="choise_character" src="" class="mt-0 h-3/4" />
        <div id="choise_party" class="relative mt-0 min-w-full h-3/4 hidden">
        </div>
    </div>
    <div class="w-1/2 text-center flex flex-col">
        <div id="buttons" class="mt-20">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" onclick="openModal(1);">
                <i class="fa-solid fa-plus"></i>獎勵
            </button>
            <button class="ml-6 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full" onclick="openModal(2);">
                <i class="fa-solid fa-minus"></i>懲罰
            </button>
        </div>
        <div id="hit" class="min-h-96 min-w-96 flex justify-center"></div>
        <div id="next">
            <button class="ml-6 bg-amber-300 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-full" onclick="pick_up();">
                <i class="fa-solid fa-rotate-left"></i>下一個
            </button>
        </div>    
    </div>
</div>
<div id="warnModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[80] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">警告</h3>
            </div>
            <div id="message" class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="warnModal.hide();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    我知道了
                </button>
            </div>
        </div>
    </div>
</div>
<div id="positiveModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">請選擇獎勵條款：</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul>
                    @foreach ($positive_rules as $r)
                    <li>
                    <input type="radio" id="{{ $r->id }}" name="positive" value="{{ $r->id }}" class="hidden peer" />
                    <label for="{{ $r->id }}" class="inline-block w-full p-2 text-gray-500 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-teal-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-teal-600 dark:peer-checked:text-blue-300 peer-checked:text-blue-600 hover:bg-teal-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-teal-700">
                        <span class="inline-block w-96">{{ $r->description }}</span>
                        XP:<input type="number" id="xp{{ $r->id }}" name="xp" value="{{ $r->effect_xp }}" class="inline w-8 border-0 border-b p-0"> 
                        GP:<input type="number" id="gp{{ $r->id }}" name="gp" value="{{ $r->effect_gp }}" class="inline w-8 border-0 border-b p-0"> 
                        <select id="item{{ $r->id }}" name="item" class="ms-1 inline w-12 border-0 border-b p-0">
                        <option value=""></option>
                        @foreach ($items as $i)
                        <option value="{{ $i->id }}"{{ $i->id == $r->effect_item ? ' selected' : '' }}>{{ $i->name }}</option>
                        @endforeach
                        </select>
                    </label>
                    </li>
                    @endforeach
                    <li>
                    <input type="radio" id="p0" name="positive" value="0" class="hidden peer" />
                    <label for="p0" class="inline-block w-full  p-2 text-gray-500 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-teal-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-teal-600 dark:peer-checked:text-blue-300 peer-checked:text-blue-600 hover:bg-teal-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-teal-700">
                        <input type="text" id="p_reason" name="reason" class="inline w-96 border-0 border-b p-0" placeholder="請輸入臨時獎勵條款...">
                        XP:<input type="number" id="xp" name="xp" class="inline w-8 border-0 border-b p-0"> 
                        GP:<input type="number" id="gp" name="gp" class="inline w-8 border-0 border-b p-0"> 
                        <select name="item" id="item" class="ms-1 inline w-12 border-0 border-b p-0">
                        <option value=""></option>
                        @foreach ($items as $i)
                        <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                        </select>
                    </label>
                    </li>
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="positive_act();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即執行
                </button>
                <button onclick="restore();positiveModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="negativeModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 id="modalHeader" class="text-center text-xl font-semibold text-gray-900 dark:text-white">請選擇懲罰條款：</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul>
                    @foreach ($negative_rules as $r)
                    <li>
                    <input type="radio" id="{{ $r->id }}" name="negative" value="{{ $r->id }}" class="hidden peer" />
                    <label for="{{ $r->id }}" class="inline-block w-full p-2 text-gray-500 bg-white border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <span class="inline-block w-[28rem]">{{ $r->description }}</span>
                        HP:<input type="number" id="hp{{ $r->id }}" name="hp" value="{{ $r->effect_hp }}" class="inline w-8 border-0 border-b p-0"> 
                        MP:<input type="number" id="mp{{ $r->id }}" name="mp" value="{{ $r->effect_mp }}" class="inline w-8 border-0 border-b p-0"> 
                    </label>
                    </li>
                    @endforeach
                    <li>
                    <input type="radio" id="n0" name="negative" value="0" class="hidden peer" />
                    <label for="n0" class="inline-block w-full w-full p-2 text-gray-500 bg-white border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <input type="text" id="n_reason" name="reason" class="inline w-[28rem] border-0 border-b p-0" placeholder="請輸入臨時懲罰條款...">
                        HP:<input type="number" id="hp" name="hp" class="inline w-8 border-0 border-b p-0">
                        MP:<input type="number" id="mp" name="mp" class="inline w-8 border-0 border-b p-0">
                    </label>
                    </li>
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="negative_act();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即執行
                </button>
                <button onclick="negative_delay();" type="button" class="ms-3 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    延遲處置
                </button>
                <button onclick="restore();negativeModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<span class="hidden top-0 top-20 top-40 left-0 left-20 left-40 left-60 left-80"></span>
<script nonce="selfhost">
    var type = 0;
    var uuids = [];
    var positive = [];
    @foreach ($positive_rules as $rule)
    positive[{{ $rule->id }}] = { 'xp':'{{ $rule->effect_xp }}', 'gp':'{{ $rule->effect_gp }}', 'item':'{{ $rule->effect_item }}'  };
    @endforeach
    var negative = [];
    @foreach ($negative_rules as $rule)
    negative[{{ $rule->id }}] = { 'hp':'{{ $rule->effect_hp }}', 'mp':'{{ $rule->effect_mp }}'  };
    @endforeach

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-wheel');
    var spin = document.getElementById('hit');
    var $targetEl = document.getElementById('warnModal');
    const warnModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('positiveModal');
    const positiveModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('negativeModal');
    const negativeModal = new window.Modal($targetEl);

    function stop() {
        spin.classList.remove('animate-spin');
    }

    function change() {
        const box = document.getElementById('choise');
        const one = document.getElementById('choise_character');
        const group = document.getElementById('choise_party');
        if (box.checked) {
            one.classList.add('hidden');
            group.classList.remove('hidden');
            type = 1;
        } else {
            one.classList.remove('hidden');
            group.classList.add('hidden');
            type = 0;
        }
    }

    function pick_up() {
        uuids = [];
        window.axios.post('{{ route('game.pickup', [ 'room_id' => $room->id ]) }}', {
            type: type,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Cache-Control': 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0'
            }
        }).then(function (response) {
            spin.innerHTML = '';
            var table = document.createElement('table');
            table.classList.add('text-2xl','text-white','drop-shadow-md');
            var body = document.createElement('tbody');
            if (type == 0) {
                var character = response.data.uuids[0];
                uuids = [ character ];
                if (character.url) {
                    var image = document.getElementById('choise_character');
                    image.setAttribute('src', character.url);
                }
                var tr = document.createElement('tr');
                var td = document.createElement('td');
                td.setAttribute('colSpan', '2');
                td.classList.add('text-center');
                if (character.title) {
                    td.appendChild(document.createTextNode(character.title));
                }
                tr.appendChild(td);
                body.appendChild(tr);
                tr = document.createElement('tr');
                td = document.createElement('td');
                td.classList.add('text-right');
                td.appendChild(document.createTextNode(character.seat));
                tr.appendChild(td);
                td = document.createElement('td');
                td.classList.add('text-left');
                td.appendChild(document.createTextNode(character.title + character.name));
                tr.appendChild(td);
                body.appendChild(tr);
                table.appendChild(body);
            } else {
                var party = response.data.party;
                var table = document.createElement('table');
                table.classList.add('text-2xl','text-white','drop-shadow-md');
                var body = document.createElement('tbody');
                var tr = document.createElement('tr');
                var td = document.createElement('td');
                td.setAttribute('colSpan', '2');
                td.classList.add('text-center');
                td.appendChild(document.createTextNode(party));
                tr.appendChild(td);
                body.appendChild(tr);
                var images = document.getElementById('choise_party');
                images.innerHTML = '';
                var characters = response.data.uuids;
                uuids = [];
                z = 10;
                col = 1;
                i = 0;
                l = 0;
                t = 0;
                characters.forEach( (character, k) => {
                    uuids[k] = character;
                    if (character.url) {
                        var image = document.createElement('img');
                        image.setAttribute('src', character.url);
                        image.classList.add('absolute','top-' + t,'left-' + l,'h-3/4','z-' + z);
                        images.appendChild(image);
                        l += 40;
                        i++;
                        if (col == 1 && i > 2) {
                            i = 0;
                            col = 2;
                            l = 20;
                            z += 10;
                            t += 20;
                        }
                        if (col == 2 && i > 1) {
                            i = 0;
                            col = 3;
                            l = 40;
                            z += 10;
                            t += 20;
                        }
                    }
                    tr = document.createElement('tr');
                    td = document.createElement('td');
                    td.classList.add('text-right');
                    td.appendChild(document.createTextNode(character.seat));
                    tr.appendChild(td);
                    td = document.createElement('td');
                    td.classList.add('text-left');
                    td.appendChild(document.createTextNode(character.title + character.name));
                    tr.appendChild(td);
                    body.appendChild(tr);
                });
            }
            table.appendChild(body);
            spin.appendChild(table);
            spin.classList.add('animate-spin');
            setTimeout(stop, 1000);
        });
    }

    function openModal(type) {
        if (uuids.length < 1) {
            var msg = document.getElementById('message');
            msg.innerHTML = '請先抽籤！';
            warnModal.show();
        } else {
            if (type == 1) {
                positiveModal.show();
            } else {
                negativeModal.show();
            }
        }
    }

    function restore() {
        var nodes = document.querySelectorAll('input[name="positive"]');
        nodes.forEach( (node) => {
            if (node.id != 'p0') {
                document.getElementById('xp' + node.id).value = positive[node.id].xp;
                document.getElementById('gp' + node.id).value = positive[node.id].gp;
                document.getElementById('item' + node.id).value = positive[node.id].item;
            }
        });
        document.getElementById('p_reason').value = '';
        document.getElementById('xp').value = '';
        document.getElementById('gp').value = '';
        document.getElementById('item').value = '';

        var nodes = document.querySelectorAll('input[name="negative"]');
        nodes.forEach( (node) => {
            if (node.id != 'n0') {
                document.getElementById('hp' + node.id).value = negative[node.id].hp;
                document.getElementById('mp' + node.id).value = negative[node.id].mp;
            }
        });
        document.getElementById('n_reason').value = '';
        document.getElementById('hp').value = '';
        document.getElementById('mp').value = '';
    }

    function positive_act() {
        var rule = document.querySelector('input[name="positive"]:checked');
        if (rule == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇條款！';
            warnModal.show();
            return;
        }
        positiveModal.hide();
        var rule_id = rule.value;
        var reason = document.getElementById('p_reason').value;
        if (rule_id == 0) {
            var xp = document.getElementById('xp').value;
            var gp = document.getElementById('gp').value;
            var item = document.getElementById('item').value;
        } else {
            var xp = document.getElementById('xp' + rule_id).value;
            var gp = document.getElementById('gp' + rule_id).value;
            var item = document.getElementById('item' + rule_id).value;
        }
        restore();
        window.axios.post('{{ route('game.positive_act') }}', {
            uuids: uuids.toString(),
            rule: rule_id,
            reason: reason,
            xp: xp,
            gp: gp,
            item: item,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        window.location.reload();
    }

    function negative_act() {
        var rule = document.querySelector('input[name="negative"]:checked');
        if (rule == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇條款！';
            warnModal.show();
            return;
        }
        negativeModal.hide();
        var rule_id = rule.value;
        var reason = document.getElementById('n_reason').value;
        if (rule_id == 0) {
            var hp = document.getElementById('hp').value;
            var mp = document.getElementById('mp').value;
        } else {
            var hp = document.getElementById('hp' + rule_id).value;
            var mp = document.getElementById('mp' + rule_id).value;
        }
        restore();
        window.axios.post('{{ route('game.negative_act') }}', {
            uuids: uuids.toString(),
            rule: rule_id,
            reason: reason,
            hp: hp,
            mp: mp,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        window.location.reload();
    }

    function negative_delay() {
        var rule = document.querySelector('input[name="negative"]:checked');
        if (rule == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇條款！';
            warnModal.show();
            return;
        }
        negativeModal.hide();
        var rule_id = rule.value;
        var reason = document.getElementById('n_reason').value;
        if (rule_id == 0) {
            var hp = document.getElementById('hp').value;
            var mp = document.getElementById('mp').value;
        } else {
            var hp = document.getElementById('hp' + rule_id).value;
            var mp = document.getElementById('mp' + rule_id).value;
        }
        restore();
        window.axios.post('{{ route('game.negative_delay') }}', {
            uuids: uuids.toString(),
            rule: rule_id,
            reason: reason,
            hp: hp,
            mp: mp,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        window.location.reload();
    }
</script>
@endsection
