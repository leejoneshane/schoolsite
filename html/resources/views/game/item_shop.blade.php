@extends('layouts.game')

@section('content')
<div class="relative w-full h-full flex flex-col gap-10">
    <div class="w-1/2 h-64 inline-flex flex-wrap gap-4">
        @foreach ($items as $f)
            @if ($f->image_avaliable())
            <div class="relative">
                <img src="{{ $f->image_url() }}" class="w-24 h-24 z-10" title="{{ $f->name . ' ' . $f->description }}">
                <div class="absolute bottom-0 right-0 bg-white bg-opacity-50 z-20">{{ $f->gp }}</div>    
            </div>
            @endif
        @endforeach
    </div>
    <div class="relative w-full h-40 right-0 inline-flex flex-row justify-end">
        <div class="relative w-1/3 h-40 top-0 mx-10 bg-white rounded-lg drop-shadow-md z-20 after:content-[''] after:absolute after:rotate-45 after:top-12 after:-right-6 after:w-12 after:h-12 after:z-10 after:bg-white">
            <div id="shop" class="mr-12 p-4 text-lg">
            </div>
        </div>
        <span class="relative"></span>
        <div class="w-40 h-40">
            <img src="{{ asset('images/game/elf.png') }}" title="精靈店長" class="relative top-0 w-40 h-40">
        </div>
    </div>
    <div class="relative w-full h-40 left-0 inline-flex flex-row justify-start">
        <div class="w-40 inline-flex flex-col">
            <img src="{{ $character->image->thumb_url() }}" title="{{ $character->name }}" class="relative bottom-0 w-40 h-40">
            <div class="ml-8 w-24 h-8 text-white text-xl font-extrabold text-shadow">L{{ $character->level }} {{ $character->title ?: '' }}{{ $character->name }}</div>
        </div>
        <span class="relative"></span>
        <div class="relative w-1/3 h-40 bottom-0 mx-10 bg-white rounded-lg drop-shadow-md z-20 before:content-[''] before:absolute before:rotate-45 before:w-12 before:h-12 before:top-12 before:-left-6 before:z-10 before:bg-white">
            <div id="dialog" class="hidden ml-12 p-4 text-lg">
                蛤，怎麼這樣！
            </div>
            <div id="service" class="hidden ml-12 p-4 text-lg">
                <button onclick="buy();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                    購買道具
                </button>
                <button onclick="sell();" class="ml-6 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">
                    販售道具
                </button>
            </div>
            <div id="newgoods" class="hidden ml-12 p-4 text-lg">
                <select id="buywhat" class="form-select w-full m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                </select>
                <button onclick="pay();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                    就要這個！
                </button>
                <button onclick="init();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    算了！
                </button>
            </div>
            <div id="oldgoods" class="hidden ml-12 p-4 text-lg">
                <select id="sellwhat" class="form-select w-full m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                </select>
                <button onclick="get();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                    就是它了！
                </button>
                <button onclick="init();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    算了！
                </button>
            </div>
            <div id="confirm" class="hidden ml-12 p-4 text-lg">
                <button onclick="done();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                    沒問題，我確定！
                </button>
                <button onclick="init();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    算了！
                </button>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var character = {!! $character->toJson(JSON_UNESCAPED_UNICODE) !!};
    var shop_open = {!! $configure && $configure->item_shop ? 'true' : 'false' !!}; 
    var items = [];
    var step;
    var item;
    var money;
    @foreach ($items as $f)
    items[{{ $f->id }}] = {!! $f->toJson(JSON_UNESCAPED_UNICODE); !!}
    @endforeach
    var new_items = [];
    var old_items = [];

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-itemshop');
    const seller = document.getElementById("shop");
    const dialog = document.getElementById("dialog");
    const service = document.getElementById("service");
    const newgoods = document.getElementById("newgoods");
    const oldgoods = document.getElementById("oldgoods");
    const buywhat = document.getElementById("buywhat");
    const sellwhat = document.getElementById("sellwhat");
    const confirm = document.getElementById("confirm");
    window.onload = init;

    function init() {
        if (shop_open) {
            seller.innerHTML = '歡迎光臨，今天需要什麼服務呢？';
            dialog.classList.add('hidden');
            service.classList.remove('hidden');
            oldgoods.classList.add('hidden');
            newgoods.classList.add('hidden');
            confirm.classList.add('hidden');
        } else {
            seller.innerHTML = '很抱歉，東西都賣完了，請下次再來！';
            dialog.classList.remove('hidden');
            service.classList.add('hidden');
            oldgoods.classList.add('hidden');
            newgoods.classList.add('hidden');
            confirm.classList.add('hidden');
        }
        window.axios.post('{{ route('game.get_myitems') }}', {
            uuid: character.uuid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            money = response.data.money;
            old_items = [];
            for (var k in response.data.items) {
                var f = response.data.items[k];
                old_items[f.id] = f;
            }
            sellwhat.innerHTML = '';
            if (old_items.length > 0) {
                old_items.forEach( item => {
                    var opt = document.createElement('option');
                    opt.value = item.id;
                    opt.innerHTML = item.name;
                    sellwhat.appendChild(opt);
                });
            }
            buywhat.innerHTML = '';
            if (items.length > 0) {
                items.forEach( item => {
                    var opt = document.createElement('option');
                    opt.value = item.id;
                    opt.innerHTML = item.name;
                    buywhat.appendChild(opt);
                });
            }
        });
    }

    function buy() {
        dialog.classList.add('hidden');
        service.classList.add('hidden');
        oldgoods.classList.add('hidden');
        newgoods.classList.remove('hidden');
        confirm.classList.add('hidden');
    }

    function sell() {
        if (old_items.length < 1) {
            seller.innerHTML = '您的背包裡空無一物，沒有道具可以販賣!';
            dialog.classList.add('hidden');
            service.classList.remove('hidden');
            oldgoods.classList.add('hidden');
            newgoods.classList.add('hidden');
            confirm.classList.add('hidden');
        } else {
            dialog.classList.add('hidden');
            service.classList.add('hidden');
            oldgoods.classList.remove('hidden');
            newgoods.classList.add('hidden');
            confirm.classList.add('hidden');
        }
    }

    function pay() {
        step = 'buy';
        item = buywhat.value;
        if (items[item].gp > money) {
            seller.innerHTML = '客倌，您的錢不夠喔！您要不要選別的？';
        } else {
            seller.innerHTML = '客倌，這個道具是' + items[item].description + '需要花你' + items[item].gp +'枚金幣，您確定要購買嗎？';
            dialog.classList.add('hidden');
            service.classList.add('hidden');
            oldgoods.classList.add('hidden');
            newgoods.classList.add('hidden');
            confirm.classList.remove('hidden');
        }
    }

    function get() {
        step = 'sell';
        item = sellwhat.value;
        seller.innerHTML = '客倌，這個道具還沒用過，我願意用原價' + items[item].gp +'枚金幣買回，可以嗎？';
        dialog.classList.add('hidden');
        service.classList.add('hidden');
        oldgoods.classList.add('hidden');
        newgoods.classList.add('hidden');
        confirm.classList.remove('hidden');
    }

    function done() {
        if (step == 'buy') {
            window.axios.post('{{ route('game.buy_item') }}', {
                uuid: character.uuid,
                item: item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                money = response.data.gp;
            });
        } else {
            window.axios.post('{{ route('game.sell_item') }}', {
                uuid: character.uuid,
                item: item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                money = response.data.gp;
            });
        }
        init();
    }
</script>
@endsection
