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
            <img src="{{ $m->image->url() }}" title="{{ $m->name }}" class="h-full z-10" />
            @endif
            @endforeach
        </div>
        <div class="w-full inline-flex flex-row justify-center rounded bg-transparent font-extrabold drop-shadow-md p-8 z-20">
            <input type="text" id="name" name="name" value="{{ $party->name }}" class="w-64 h-8 text-3xl text-white rounded border-0 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none bg-transparent" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;" 
                placeholder="請輸入公會名稱...">
            <textarea id="description" class="z-20 w-96 text-3xl text-white rounded border-0 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none bg-transparent" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;" 
                name="description" rows="2" cols="12" placeholder="請輸入公會成立宗旨、目標、或口號...">{{ $party->description }}</textarea>
            <span class="text-3xl text-white" style="text-shadow: 1px 1px 0 #000000, -1px -1px 0 black, -1px 1px 0 black, 1px -1px 0 black, 1px 1px 0 black;">公會長：{{ $party->leader->name }}</span>
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
                        <span class="text-blue-500">{{ $party->effect_hp }}</span>
                    </td>
                </tr>
                <tr>
                    <td>每日回復MP</td>
                    <td>
                        <span class="text-blue-500">{{ $party->effect_mp }}</span>
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
                        <button class="hover:border hover:border-2 hover:border-blue-700 text-white font-bold py-2 px-4 rounded-full" title="賣出" onclick="sell({{ $f->id }});">
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
            <button class="hover:border hover:border-2 hover:border-blue-700 text-white font-bold py-2 px-4 rounded-full" title="捐款" onclick="donate();">
                <div class="w-36 h-36 bg-game-chest bg-contain bg-no-repeat">
                    <span class="absolute bottom-0 text-lg text-amber-500 font-bold">{{ $party->treasury }}</span>
                </div>
            </button>
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
<div id="teammateModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[70] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">對象</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul id="memberList" >
                    @forelse ($character->teammate() as $m)
                    <li>
                        <input id="team{{ $m->uuid }}" type="radio" name="teammate" class="hidden peer">
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
    var character = '{{ $character->uuid }}';
    var furnitures = [];
    window.onload = bag;

    var $targetEl = document.getElementById('itemsModal');
    const itemsModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('teammateModal');
    const teamModal = new window.Modal($targetEl);

    function bag() {
        for (i=1; i<13; i++) {
            var node = document.getElementById('bag' + i);
            node.innerHTML = '';
        }
        window.axios.post('{{ route('game.get_myitems') }}', {
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
            items.forEach( (item, k) => {
                var node = document.getElementById('bag' + k);
                if (item.passive == 1) {
                    var btn = document.createElement('button');
                    btn.setAttribute('type', 'button');
                    btn.classList.add('relative','inline-flex','items-center','p-3','text-sm','font-medium','text-center','text-white','rounded-lg','hover:bg-blue-800','focus:ring-4','focus:outline-none','focus:ring-blue-300',);
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
            });
        });
    }
</script>
@endsection
