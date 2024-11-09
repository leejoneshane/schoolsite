@extends('layouts.game')

@section('content')
<div class="w-full flex gap-4">
    <div class="w-80 h-full flex flex-col">
        <div class="text-2xl font-bold leading-normal p-5 drop-shadow-md">
            地圖探險
        </div>
        <table class="w-full h-full text-left font-normal">
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">學習單標題</th>
                <td class="p-2">{{ $adventure->worksheet->title }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">設計者</th>
                <td class="p-2">{{ $adventure->worksheet->teacher_name }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">科目名稱</th>
                <td class="p-2">{{ $adventure->worksheet->subject }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">適用年級</th>
                <td class="p-2">{{ $adventure->worksheet->grade->name }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" colspan="2" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">任務列表：</th>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td colspan="2" class="p-2">
                    <button type="button" id="list0" onclick="open_view(0)" class="text-teal-500 hover:bg-teal-100">介紹</button>
                </td>
            </tr>
            @foreach ($adventure->worksheet->tasks as $t)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td colspan="2" class="p-2">
                    @if ($character->visited($adventure->id, $t->id))
                    <button type="button" id="list{{ $t->id }}" onclick="open_view({{ $t->id }})" class="text-teal-500 hover:bg-teal-100">{{ $t->title }}</button>
                    @elseif ($character->next_visite($adventure->id)->id == $t->id)
                    <button type="button" id="list{{ $t->id }}" onclick="open_view({{ $t->id }})" class="text-blue-500 hover:bg-teal-100">{{ $t->title }}</button>
                    @else
                    <button type="button" id="list{{ $t->id }}" class="text-gray-300 hover:bg-teal-100">{{ $t->title }}</button>
                    @endif
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
                <label class="text-lg text-black font-bold">獎勵：<span id="reward" class="text-base font-normal"></span></label>
            </div>
            <div class="p-2 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <label for="done" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="done" name="done" value="yes" class="sr-only peer" onchange="task_done()">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-blue-700 dark:text-blue-300">我已經完成！</span><span id="completed" class="block w-full rounded"></span>
                </label>
            </div>
            <div class="p-2 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <label class="text-lg text-black font-bold">評語：</label>
                <div id="comments" class="block w-full max-h-64 overflow-y-scroll"></div>
            </div>
            <div class="p-2 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <label class="text-lg text-black font-bold">過關時間：</label>
                <div id="reviewed" class="block w-full max-h-64 overflow-y-scroll"></div>
            </div>
        </div>
    </div>
</div>
<div id="introModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-blue-700">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 id="modalHeader" class="text-xl font-semibold text-gray-900 dark:text-white">
                    探險地圖介紹
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" onclick="introModal.hide();">
                    <i class="fa-solid fa-xmark"></i>
                    <span class="sr-only">關閉視窗</span>
                </button>
            </div>
            <div class="p-2 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <div class="block w-full max-h-96 overflow-y-scroll">
                    {!! $adventure->worksheet->intro !!}
                </div>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var character = {!! $character->toJson(JSON_UNESCAPED_UNICODE) !!};
    var adventure = {!! $adventure->toJson(JSON_UNESCAPED_UNICODE) !!};
    var worksheet = {!! $adventure->worksheet->toJson(JSON_UNESCAPED_UNICODE) !!};
    var tid;
    var tasks = [];
    @foreach ($adventure->worksheet->tasks as $t)
    tasks[{{ $t->id }}] = {!! $t->toJson(JSON_UNESCAPED_UNICODE); !!};
    tasks[{{ $t->id }}].visited = false;
    @endforeach
    @foreach ($character->travel($adventure->id) as $t)
    tasks[{{ $t->task_id }}].visited = true;
    @endforeach
    var process = [];
    @foreach ($character->travel($adventure->id) as $t)
    process[{{ $t->task->id }}] = {!! $t->toJson(JSON_UNESCAPED_UNICODE); !!};
    @endforeach
    var items = [];
    @foreach ($items as $t)
    items[{{ $t->id }}] = {!! $t->toJson(JSON_UNESCAPED_UNICODE); !!};
    @endforeach

    var $targetEl = document.getElementById('taskModal');
    const taskModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('introModal');
    const introModal = new window.Modal($targetEl);
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext("2d");
    const dot = document.getElementById('dot');
    const spot = document.getElementById('spot');
    const map = new Image(700, 700);
    map.src = '{{ $adventure->worksheet->map->url() }}';
    map.addEventListener("load", (e) => {
        redraw_lines();
        introModal.show();
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

    function create_mark(id) {
        var x = tasks[id].coordinate_x - 12;
        var y = tasks[id].coordinate_y - 32;
        var rect = canvas.getBoundingClientRect();
        dot.style.color = 'red';
        var xml = new XMLSerializer().serializeToString(dot);
        var b64 = 'data:image/svg+xml;base64,' + btoa(xml);
        var tmp = document.createElement('img');
        tmp.setAttribute('id', 'mark');
        tmp.setAttribute('src', b64);
        tmp.setAttribute('data-id', id);
        tmp.setAttribute('data-x', x);
        tmp.setAttribute('data-y', y);
        tmp.setAttribute('role', 'task');
        tmp.setAttribute('draggable', false);
        tmp.setAttribute('onclick', 'view_task(this)');
        tmp.style.position = 'absolute';
        tmp.style.zIndex = 3;
        tmp.style.top = y + parseInt(rect.top) + 'px';
        tmp.style.left = x + parseInt(rect.left) + 'px';
        tmp.style.width = '24px';
        tmp.style.height = '32px';
        document.body.appendChild(tmp);
    }

    function create_dot(id) {
        var x = tasks[id].coordinate_x - 16;
        var y = tasks[id].coordinate_y - 16;
        var rect = canvas.getBoundingClientRect();
        spot.style.color = 'aqua';
        if (tasks[id].visited && process[id].reviewed_at != null) {
            spot.style.color = 'darkgray';
        }
        var xml = new XMLSerializer().serializeToString(spot);
        var b64 = 'data:image/svg+xml;base64,' + btoa(xml);
        var tmp = document.createElement('img');
        tmp.setAttribute('id', 'spot' + id);
        tmp.setAttribute('src', b64);
        tmp.setAttribute('title', tasks[id].title);
        tmp.setAttribute('data-id', id);
        tmp.setAttribute('data-x', x);
        tmp.setAttribute('data-y', y);
        tmp.setAttribute('role', 'task');
        tmp.setAttribute('draggable', false);
        if (tasks[id].visited && process[id].reviewed_at != null) {
            tmp.setAttribute('onclick', 'view_task(this)');
        } else {
            tmp.setAttribute('onclick', 'move_mark(' + id + ')');
        }
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
            var done = tasks[from].visited;
            var tmp = document.getElementById('spot' + from); 
            if (typeof tmp === 'undefined' || tmp === null) {
                create_dot(from);
            } else {
                document.body.removeChild(tmp);
                create_dot(from);
            }
            if (done) {
                var to = tasks[from].next_task;
                var next = (tasks[to].visited && process[to].reviewed_at != null);
                if (to > 0) {
                    if (next) {
                        ctx.beginPath();
                        ctx.moveTo(tasks[from].coordinate_x, tasks[from].coordinate_y);
                        ctx.lineTo(tasks[to].coordinate_x, tasks[to].coordinate_y);
                        ctx.strokeStyle = 'darkgray';
                        ctx.lineWidth = 3;
                        ctx.stroke();
                    } else {
                        ctx.beginPath();
                        ctx.moveTo(tasks[from].coordinate_x, tasks[from].coordinate_y);
                        ctx.lineTo(tasks[to].coordinate_x, tasks[to].coordinate_y);
                        ctx.strokeStyle = 'aqua';
                        ctx.lineWidth = 3;
                        ctx.stroke();
                        break;
                    }
                }
                from = to;
            } else {
                break;
            }
        }
        if (from > 0) {
            var tmp = document.getElementById('mark'); 
            if (typeof tmp === 'undefined' || tmp === null) {
                create_mark(from);
            }
            var done = (tasks[from].visited && process[from].reviewed_at != null);
            if (done) {
                var to = tasks[from].next_task;
                if (to > 0) {
                    var tmp = document.getElementById('spot' + to); 
                    if (typeof tmp === 'undefined' || tmp === null) {
                        create_dot(to);
                    } else {
                        document.body.removeChild(tmp);
                        create_dot(to);
                    }
                }
            }
        }
    }

    function move_mark(id) {
        var rect = canvas.getBoundingClientRect();
        var tmp = document.getElementById('mark');
        var x = tasks[id].coordinate_x - 12;
        var y = tasks[id].coordinate_y - 32;
        tmp.setAttribute('data-id', id);
        tmp.setAttribute('data-x', x);
        tmp.setAttribute('data-y', y);
        tmp.style.top = y + parseInt(rect.top) + 'px';
        tmp.style.left = x + parseInt(rect.left) + 'px';
    }

    function view_task(img) {
        tid = img.getAttribute('data-id');
        open_view(tid);
    }

    function open_view(tno) {
        if (tno == 0) {
            introModal.show();
        } else {
            tid = tno;
            document.getElementById('title').innerHTML = tasks[tno].title;
            document.getElementById('story').innerHTML = tasks[tno].story;
            document.getElementById('task').innerHTML = tasks[tno].task;
            var msg = '';
            if (tasks[tno].reward_xp) {
                msg += '　經驗值：' + tasks[tno].reward_xp;
            }
            if (tasks[tno].reward_gp) {
                msg += '　金幣：' + tasks[tno].reward_gp;
            }
            var myid = tasks[tno].reward_item;
            if (myid) {
                msg += '　道具：' + items[myid].name;
            }
            document.getElementById('reward').innerHTML = msg;
            if (tasks[tno].visited && process[tno].reviewed_at != null) {
                document.getElementById('done').setAttribute('checked', true);
                document.getElementById('done').setAttribute('disabled', true);
            } else {
                document.getElementById('done').removeAttribute('disabled');
            }
            document.getElementById('completed').innerHTML = process[tno].completed_at;
            if (process[tno].comments) {
                document.getElementById('comments').innerHTML = process[tno].comments;
            } else {
                document.getElementById('comments').innerHTML = '無';
            }
            if (process[tno].reviewed_at) {
                document.getElementById('reviewed').innerHTML = '已通過！';
            } else {
                document.getElementById('reviewed').innerHTML = '尚未通過！';
            }
            taskModal.show();
        }
    }

    function task_done() {
        window.axios.post('{{ route('game.task_done') }}', {
            uuid: character.uuid,
            aid: adventure.id,
            tid: tid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            var result = response.data.result;
            var pro = response.data.process;
            process[tid] = pro;
            var node = document.getElementById('list' + tid);
            if (result == 'success') {
                tasks[tid].visited = true;
                node.classList.replace('text-blue-500', 'text-teal-500');
                var myid = tasks[tid].next_task;
                var node = document.getElementById('list' + myid);
                node.classList.replace('text-gray-300', 'text-blue-500');
                node.setAttribute('onclick', 'open_view(' + myid + ')');
            }
            redraw_lines();
            taskModal.hide();
        });
    }

    function task_comments(json_str) {
        var pro = JSON.parse(json_str);
        process[pro.task_id] = pro;
    }

    function task_notice(json_str) {
        var pro = JSON.parse(json_str);
        tid = pro.task_id;
        process[tid] = pro;
        open_view(tid);
    }

    function task_pass(json_str) {
        var pro = JSON.parse(json_str);
        tid = pro.task_id;
        process[tid] = pro;
        var node = document.getElementById('list' + tid);
        node.classList.replace('text-blue-500', 'text-teal-500');
        var myid = tasks[tid].next_task;
        var node = document.getElementById('list' + myid);
        node.classList.replace('text-gray-300', 'text-blue-500');
        node.setAttribute('onclick', 'open_view(' + myid + ')');
        redraw_lines();
        open_view(tid);
    }
</script>
@endsection