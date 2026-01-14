@extends('layouts.game')

@section('content')
<div class="w-full flex gap-4">
    <div class="w-80 h-full flex flex-col">
        <div class="text-2xl font-bold leading-normal p-5 drop-shadow-md">
            學習任務
            <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.worksheets') }}">
                <i class="fa-solid fa-eject"></i>返回上一頁
            </a>
        </div>
        <table class="w-full h-full text-left font-normal">
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">學習單標題</th>
                <td class="p-2">{{ $worksheet->title }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">設計者</th>
                <td class="p-2">{{ $worksheet->teacher_name }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">科目名稱</th>
                <td class="p-2">{{ $worksheet->subject }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">適用年級</th>
                <td class="p-2">{{ $worksheet->grade->name }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" colspan="2" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">任務列表：</th>
            </tr>
            <tr id="empty" class="{{ $worksheet->tasks->count() > 0 ? 'hidden ' : '' }}odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td colspan="2" class="p-2">還沒有學習任務！</td>
            </tr>
            @foreach ($worksheet->tasks as $t)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td colspan="2" class="p-2">
                    <button type="button" id="list{{ $t->id }}" onclick="open_editor({{ $t->id }})" class="hover:bg-teal-100">{{ $t->title }}</button>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="w-full h-full flex justify-center">
        <canvas id="myCanvas" width="700px" height="700px" z-index="0"></canvas>
    </div>
</div>
<div class="sr-only">
    <svg id="dot" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="width:1.5em;height:1.5em;vertical-align:-0.125em;color:darkgrey;">
        <path fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>
    </svg>
    <svg id="spot" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width:1.5em;height:1.5em;vertical-align:-0.125em;color:darkgrey;">
        <path fill="currentColor" d="M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256-96a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/>
    </svg>
</div>
<div id="taskModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-blue-700">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 id="modalHeader" class="text-xl font-semibold text-gray-900 dark:text-white">
                    瀏覽學習任務
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" onclick="taskModal.hide();">
                    <i class="fa-solid fa-xmark"></i>
                    <span class="sr-only">關閉視窗</span>
                </button>
            </div>
            <div class="p-2 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <label for="title" class="text-lg text-black font-bold">標題：</label>
                <div id="title" class="block w-full rounded"></div>
                <label class="text-lg text-black font-bold">故事：</label>
                <div id="story" class="block w-full max-h-64 overflow-y-scroll"></div>
                <label class="text-lg text-black font-bold">任務：</label>
                <div id="task" class="block w-full max-h-64 overflow-y-scroll"></div>
                <div class="p-2">
                    <label for="review" class="inline-flex relative items-center cursor-pointer">
                        <input type="checkbox" id="review" name="review" value="yes" class="sr-only peer" disabled>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-gray-900 dark:text-gray-300">需要審核</span>
                    </label>
                </div>
                <div class="p-2">
                    <label for="xp" class="text-lg text-black font-bold">經驗獎勵：</label>
                    <div id="xp" class="inline-block w-16 rounded bg-white"></div>
                    <label for="gp" class="pl-2 text-lg text-black font-bold">金幣獎勵：</label>
                    <div id="gp" class="inline-block w-16 rounded bg-white"></div>
                    <label for="item" class="pl-2 text-lg text-black font-bold">道具獎勵：</label>
                    <div id="item" class="inline-block w-16 rounded bg-white"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var worksheet = {!! $worksheet->toJson(JSON_UNESCAPED_UNICODE) !!};
    var tasks = [];
    @foreach ($worksheet->tasks as $t)
    tasks[{{ $t->id }}] = {!! $t->toJson(JSON_UNESCAPED_UNICODE) !!};
    @endforeach
    var items = [];
    @foreach ($items as $t)
    items[{{ $t->id }}] = {!! $t->toJson(JSON_UNESCAPED_UNICODE) !!};
    @endforeach

    var $targetEl = document.getElementById('taskModal');
    const taskModal = new window.Modal($targetEl);
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext("2d");
    const dot = document.getElementById('dot');
    const spot = document.getElementById('spot');
    const map = new Image(700, 700);
    map.src = '{{ $worksheet->map->url() }}';
    map.addEventListener("load", (e) => {
        @foreach ($worksheet->tasks as $t)
        create_dot({{ $t->id }}, {{ $t->coordinate_x }}, {{ $t->coordinate_y }});
        @endforeach
        redraw_lines();
    });
    window.addEventListener("resize", (event) => {
        var rect = canvas.getBoundingClientRect();
        var nodes = document.querySelectorAll('img[role="task"]');
        if (nodes) {
            nodes.forEach( (node) => {
                node.style.top = parseInt(node.getAttribute('data-y')) + parseInt(rect.top) + 'px';
                node.style.left = parseInt(node.getAttribute('data-x')) + parseInt(rect.left) + 'px';
            });
        }
    });

    function create_dot(id, x, y) {
        x -= 16;
        y -= 16;
        var rect = canvas.getBoundingClientRect();
        spot.style.color = 'aqua';
        var xml = new XMLSerializer().serializeToString(spot);
        var b64 = 'data:image/svg+xml;base64,' + btoa(xml);
        var tmp = document.createElement('img');
        tmp.setAttribute('id', 'spot' + id);
        tmp.setAttribute('src', b64);
        tmp.setAttribute('data-id', id);
        tmp.setAttribute('data-x', x);
        tmp.setAttribute('data-y', y);
        tmp.setAttribute('draggable', false);
        tmp.setAttribute('onclick', 'edit_task(this)');
        tmp.style.position = 'absolute';
        tmp.style.zIndex = 2;
        tmp.style.top = y + parseInt(rect.top) + 'px';
        tmp.style.left = x + parseInt(rect.left) + 'px';
        tmp.style.width = '32px';
        tmp.style.height = '32px';
        document.body.appendChild(tmp);
    }

    function redraw_lines() {
        ctx.drawImage(map, 0, 0, canvas.width, canvas.height);
        var from = worksheet.next_task;
        while (from > 0) {
            var to = tasks[from].next_task;
            if (to > 0) {
                ctx.beginPath();
                ctx.moveTo(tasks[from].coordinate_x, tasks[from].coordinate_y);
                ctx.lineTo(tasks[to].coordinate_x, tasks[to].coordinate_y);
                ctx.strokeStyle = 'aqua';
                ctx.lineWidth = 3;
                ctx.stroke();
            }
            from = to;
        }
    }

    function edit_task(img) {
        var tid = img.getAttribute('data-id');
        open_editor(tid);
    }

    function open_editor(tno) {
        document.getElementById('title').innerHTML = tasks[tno].title;
        document.getElementById('story').innerHTML = tasks[tno].story;
        document.getElementById('task').innerHTML = tasks[tno].task;
        if (tasks[tno].review == 1) {
            document.getElementById('review').checked = true;
        } else {
            document.getElementById('review').checked = false;
        }
        var xp = tasks[tno].reward_xp;
        if (xp) {
            document.getElementById('xp').innerHTML = xp;
        } else {
            document.getElementById('xp').innerHTML = '無';
        }
        var gp = tasks[tno].reward_gp;
        if (gp) {
            document.getElementById('gp').innerHTML = gp;
        } else {
            document.getElementById('gp').innerHTML = '無';
        }
        var myid = tasks[tno].reward_item;
        if (myid) {
            document.getElementById('item').innerHTML = items[myid].name;
        } else {
            document.getElementById('item').innerHTML = '無';
        }
        taskModal.show();
    }
</script>
@endsection
