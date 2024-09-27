@extends('layouts.game')

@section('content')
<div class="w-full h-screen flex flex-col">
    <div class="relative w-full h-3/4 truncate flex flex-col">
        @if ($party->fundation)
        <img src="{{  $party->fundation->url() }}" class="absolute bottom-0 w-full z-0" />
        @endif
        <div class="absolute w-full h-3/4 bottom-0 inline-flex flex-row justify-center z-10">
            @foreach ($party->members as $m)
            @if ($m->image)
            <img src="{{ $m->image->url() }}" title="{{ $m->name }}" class="h-full z-10" onclick="prepare_item('{{ $m->uuid }}')" />
            @endif
            @endforeach
        </div>
        <div class="w-full inline-flex flex-row justify-center rounded bg-transparent font-extrabold drop-shadow-md p-8 z-20">
            <input type="text" id="name" name="name" value="{{ $party ? $party->name : '' }}" class="w-64 h-8 text-3xl text-white rounded border-0 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none bg-transparent" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;" 
                placeholder="請輸入公會名稱..." onchange="change_name();"{{ ($character->uuid != $party->uuid) ? ' disabled' : '' }}>
            <textarea id="description" class="z-20 w-96 text-3xl text-white rounded border-0 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none bg-transparent" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;" 
                name="description" rows="2" cols="12" placeholder="請輸入公會成立宗旨、目標、或口號..." onchange="change_desc();"{{ ($character->uuid != $party->uuid) ? ' disabled' : '' }}>{{ $party ? $party->description : '' }}</textarea>
            <div class="text-3xl text-white" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;">
                @if ($character->uuid == $party->uuid)
                <label for="leader">公會長：</label>
                <select id="leader" name="leader" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    onchange="change_leader();">
                    <option value="">尚未設定</option>
                    @foreach ($party->withAbsent as $c)
                    <option value="{{ $c->uuid }}"{{ $c->uuid == $party->uuid ? ' selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @else
                公會長：{{ $party->leader ? $party->leader->name : '' }}
                @endif
            </div>
            @if ($party->configure && $party->configure->change_base)
            <div class="ml-2 text-3xl text-white" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;">
                @if ($character->uuid == $party->uuid)
                <label for="base">變更據點：</label>
                <select id="base" name="base" class="form-select w-48 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    onchange="change_base();">
                    <option value="">尚未設定</option>
                    @foreach ($bases as $b)
                    <option value="{{ $b->id }}"{{ $b->id == $party->base_id ? ' selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
            @endif
        </div>
    </div>
    <div class="w-full h-1/4  bg-white flex flex-row justify-center">
        <div class="w-52">
            <table class="text-left font-normal pb-6">
                <tr>
                    <td colspan="2" class="bg-gray-500 text-white">影響效果</td>
                </tr>
                <tr>
                    <td class="w-24">每日回復HP</td>
                    <td class="w-16">
                        @if ($party->effect_hp > 0)
                        <span class="text-blue-500">{{ $party->effect_hp }}</span>
                        @else
                        <span class="text-red-500">{{ $party->effect_hp }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>每日回復MP</td>
                    <td>
                        @if ($party->effect_mp > 0)
                        <span class="text-blue-500">{{ $party->effect_mp }}</span>
                        @else
                        <span class="text-red-500">{{ $party->effect_mp }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>AP增益</td>
                    <td>
                        @if ($party->effect_ap > 0)
                        <span class="text-blue-500">{{ $party->effect_ap }}</span>
                        @else
                        <span class="text-red-500">{{ $party->effect_ap }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>DP增益</td>
                    <td>
                        @if ($party->effect_dp > 0)
                        <span class="text-blue-500">{{ $party->effect_dp }}</span>
                        @else
                        <span class="text-red-500">{{ $party->effect_dp }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="w-24">SP增益</td>
                    <td>
                        @if ($party->effect_sp > 0)
                        <span class="text-blue-500">{{ $party->effect_sp }}</span>
                        @else
                        <span class="text-red-500">{{ $party->effect_sp }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="w-full">
            <div class="sr-only">家具</div>
            <table class="w-full h-full">
                <tr>
                    @if ($party->furnitures)
                    @foreach ($party->furnitures as $f)
                    <td class="w-1/5">
                        @if ($character->uuid == $party->uuid)
                        <button type="button" title="賣出" onclick="furniture('{{ $f->id }}', '{{ $f->name }}');" class="hover:border hover:border-2 hover:border-blue-700 text-white font-bold py-2 px-4 rounded-full">
                            <img src="{{ $f->image_url(); }}" title="{{ $f->name . ' ' . $f->description }}" />
                        </button>
                        @else
                        <img src="{{ $f->image_url(); }}" title="{{ $f->name . ' ' . $f->description }}" />
                        @endif
                    </td>
                    @endforeach
                    @endif
                    @php
                        $remain = 5 - ($party->furnitures ? $party->furnitures->count() : 0);    
                    @endphp
                    @if ($remain > 0)
                        @for ($i=0; $i<$remain; $i++)
                    <td class="w-1/5"></td>
                        @endfor
                    @endif
                </tr>
            </table>
        </div>
        <div class="w-auto">
            <div class="sr-only">金庫</div>
            <button type="button" title="捐款" onclick="donateModal.show();" class="hover:border hover:border-2 hover:border-blue-700 text-white font-bold py-2 px-4 rounded-full">
                <div class="w-36 h-36 bg-game-chest bg-contain bg-no-repeat">
                    <span class="absolute bottom-0 text-lg text-amber-500 font-bold">{{ $party->treasury }}</span>
                </div>
            </button>
        </div>
    </div>
</div>
<div id="warnModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[80] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
<div id="confirmModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[80] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">再次確認</h3>
            </div>
            <div id="message" class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="sell();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    確認
                </button>
                <button onclick="confirmModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="donateModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">捐款</h3>
            </div>
            <div class="p-6 w-full text-base leading-relaxed text-gray-500 dark:text-gray-400 inline-flex flex-col">
                <div class="pb-8 text-center text-red-500">您的錢包目前有 {{ $character->gp }} 枚金幣！</div>
                <div class="relative w-full inline-flex justify-center">
                    <input type="number" id="currency-input" min="0" max="{{ $character->gp }}" class="block w-16 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-s-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500" value="0" required />
                </div>
                <div class="relative">
                    <input id="price-range-input" type="range" value="0" min="0" max="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-0 -bottom-6">0 GP</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-1/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">25 GP</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-2/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">50 GP</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-3/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">75 GP</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute end-0 -bottom-6">100 GP</span>
                </div>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="donate();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    確認
                </button>
                <button onclick="donateModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="itemsModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
                <button onclick="given();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    贈與
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
    var party = {{ $party->id }};
    var money = {{ $character->gp }};
    var cash = 0;
    var data_character;
    var data_furniture;
    var furnitures = [];

    var $targetEl = document.getElementById('itemsModal');
    const itemsModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('warnModal');
    const warnModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('confirmModal');
    const confirmModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('donateModal');
    const donateModal = new window.Modal($targetEl);

    var rangeInput = document.getElementById('price-range-input');
    var currencyInput = document.getElementById('currency-input');
    function updateCurrencyInput() {
        if (rangeInput.value > money) {
            rangeInput.value = money;
        }
        currencyInput.value = rangeInput.value;
    }

    rangeInput.addEventListener('input', updateCurrencyInput);

    function change_name() {
        var node = document.getElementById('name');
        if (node.value) {
            window.axios.post('{{ route('game.party_name') }}', {
                party: party,
                name: node.value,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }

    function change_desc() {
        var node = document.getElementById('description');
        if (node.value) {
            window.axios.post('{{ route('game.party_desc') }}', {
                party: party,
                desc: node.value,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }

    function change_leader() {
        var node = document.getElementById('leader');
        if (node.value) {
            window.axios.post('{{ route('game.party_leader') }}', {
                party: party,
                leader: node.value,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }

    function change_base() {
        var node = document.getElementById('base');
        if (node.value) {
            window.axios.post('{{ route('game.party_base') }}', {
                party: party,
                base: node.value,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch( (error) => console.log(error));
            window.location.reload();
        }
    }

    function furniture(id, caption) {
        data_furniture = id;
        var msg = document.getElementById('message');
        msg.innerHTML = '您確定要賣出' + caption + '？';
        confirmModal.show();
    }

    function sell() {
        window.axios.post('{{ route('game.sell_furniture') }}', {
            party: party,
            furniture: data_furniture,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        window.location.reload();
    }

    function donate() {
        cash = document.getElementById('currency-input').value;
        if (cash > money) {
            var msg = document.getElementById('info');
            msg.innerHTML = '您沒有足夠的錢可以捐獻！';
            warnModal.show();
        } else if (cash > 0) {
            window.axios.post('{{ route('game.donate') }}', {
                uuid: character,
                party: party,
                cash: cash,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
        donateModal.hide();
        window.location.reload();
    }

    function prepare_item(uuid) {
        if (character == uuid) return;
        data_character = uuid;
        var ul = document.getElementById('itemList');
        ul.innerHTML = '';
        window.axios.post('{{ route('game.get_items') }}', {
            uuid: character,
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

    function given() {
        var item_obj = document.querySelector('input[name="item"]:checked');
        if (item_obj == null) {
            var msg = document.getElementById('info');
            msg.innerHTML = '您尚未選擇道具！';
            warnModal.show();
            return;
        }
        itemsModal.hide();
        window.axios.post('{{ route('game.given') }}', {
            uuid: character,
            target: data_character,
            item: item_obj.value,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
</script>
@endsection
