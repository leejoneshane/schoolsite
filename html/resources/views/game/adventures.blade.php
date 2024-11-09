@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    探險進度管理
</div>
<div class="w-full flex justify-center gap-1">
    <div class="w-64 h-full">
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
            <tr class="bg-gray-300 dark:bg-gray-500">
                <th scope="row" colspan="2" class="font-semibold text-lg p-2">任務列表：</th>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td colspan="2" class="p-2">
                    <button type="button" id="list0" onclick="task_process(0)" class="text-gray-500 hover:bg-teal-100" role="list">介紹</button>
                </td>
            </tr>
            @foreach ($adventure->worksheet->tasks as $t)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td colspan="2" class="p-2">
                    <button type="button" id="list{{ $t->id }}" onclick="task_process({{ $t->id }})" class="text-gray-500 hover:text-teal-500" role="list">{{ $t->title }}</button>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="p-2 w-96 h-full rounded-lg bg-white inline-flex flex-col">
        <label class="text-lg text-black font-bold">標題：</label>
        <div id="title" class="inline">冒險地圖介紹</div>
        <label class="text-lg text-black font-bold">故事：</label>
        <div id="story" class="block w-full max-h-60 overflow-y-auto">{!! $adventure->worksheet->intro !!}</div>
        <label class="text-lg text-black font-bold">任務：</label>
        <div id="task" class="block w-full max-h-60 overflow-y-auto">{!! $adventure->worksheet->description !!}</div>
        <label class="text-lg text-black font-bold">獎勵：<span id="reward" class="text-base font-normal"></span></label>
    </div>
    <div id="progress" class="w-fit h-[32rem] overflow-clip flex flex-col">
        <table class="w-full">
            <thead class="sticky top-0">
                <tr class="bg-gray-300">
                    <th scope="col" class="p-2 w-16 text-left">
                        座號
                    </th>
                    <th scope="col" class="p-2 w-32 text-left">
                        姓名
                    </th>
                    <th scope="col" class="p-2 w-[28rem] text-left">
                        進度
                    </th>
                </tr>    
            </thead>
        </table>
        <div class="flex-1 overflow-y-scroll">
            <table class="w-full">
                @foreach ($characters as $c)
                <tr class="bg-white border-b">
                    <td class="p-2 w-16">{{ $c->seat }}</td>
                    <td class="p-2 w-32">{{ $c->name }}</td>
                    <td class="p-2 w-[26rem]">
                        <ol class="flex items-center w-full">
                        @foreach ($adventure->worksheet->tasks as $t)
                            @if (!($loop->last))
                                @if ($c->visited($adventure->id, $t->id))
                            <li id="line{{ $c->seat }}_{{ $t->id }}" data-seat="{{ $c->seat }}" data-task="{{ $t->id }}" role="line" class="flex w-full items-center text-blue-600 after:content-[''] after:w-full after:h-1 after:border-b after:border-blue-300 after:border-4 after:inline-block">
                                <span id="node{{ $c->seat }}_{{ $t->id }}" data-seat="{{ $c->seat }}" data-task="{{ $t->id }}" role="node" class="flex items-center justify-center w-4 h-4 bg-blue-300 rounded-full shrink-0" title="{{ $t->title }}">
                                    <svg class="w-3 h-3 text-blue-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                    </svg>
                                </span>
                            </li>
                                @else
                            <li id="line{{ $c->seat }}_{{ $t->id }}" data-seat="{{ $c->seat }}" data-task="{{ $t->id }}" role="line" class="flex w-full items-center text-gray-600 after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block">
                                <span id="node{{ $c->seat }}_{{ $t->id }}" data-seat="{{ $c->seat }}" data-task="{{ $t->id }}" role="node" class="flex items-center justify-center w-4 h-4 bg-gray-100 rounded-full shrink-0" title="{{ $t->title }}"></span>
                            </li>
                                @endif
                            @else
                            <li id="line{{ $c->seat }}_{{ $t->id }}" data-seat="{{ $c->seat }}" data-task="{{ $t->id }}" role="line" class="flex items-center w-full text-gray-600">
                                <span id="node{{ $c->seat }}_{{ $t->id }}" data-seat="{{ $c->seat }}" data-task="{{ $t->id }}" role="node" class="flex items-center justify-center w-4 h-4 bg-gray-100 rounded-full shrink-0" title="{{ $t->title }}"></span>
                            </li>
                            @endif
                        @endforeach
                        </ol>                                         
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div id="process" class="hidden w-fit h-[32rem] overflow-clip flex flex-col">
        <table class="w-[40rem] table-fixed">
            <thead class="sticky top-0">
                <tr class="bg-gray-300">
                    <th scope="col" class="p-2 w-16 text-left">
                        座號
                    </th>
                    <th scope="col" class="p-2 w-32 text-left">
                        姓名
                    </th>
                    <th scope="col" class="p-2 w-32 text-left">
                        完成
                    </th>
                    <th scope="col" class="p-2 w-60 text-left">
                        評語
                    </th>
                    <th scope="col" class="p-2 w-20 text-left">
                        審核
                    </th>
                </tr>
            </thead>
        </table>
        <div class="flex-1 overflow-y-auto">
            <table class="w-[40rem] table-fixed">
                @foreach ($characters as $c)
                <tr id="char{{ $c->seat }}" class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                    <td class="p-2 w-16">{{ $c->seat }}</td>
                    <td class="p-2 w-32">{{ $c->name }}</td>
                    <td class="p-2 w-32"><span id="done{{ $c->seat }}"></span></td>
                    <td class="p-2 w-60"><input type="text" id="comment{{ $c->seat }}" name="comment{{ $c->seat }}" value="" onchange="task_comments({{ $c->seat }})" class="w-56 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"></td>
                    <td class="p-2 w-20 flex gap-4">
                        <label for="ok{{ $c->seat }}" class="inline-flex relative items-center cursor-pointer">
                            <input type="checkbox" id="ok{{ $c->seat }}" name="ok{{ $c->seat }}" value="yes" class="sr-only peer" onchange="task_review({{ $c->seat }})">
                            <div class="text-gray-300 rounded-full peer peer-checked:text-blue-600"><i class="text-2xl fa-regular fa-circle-check"></i></div>
                        </label>
                        <label for="notice{{ $c->seat }}" class="inline-flex relative items-center cursor-pointer">
                            <input type="checkbox" id="notice{{ $c->seat }}" name="notice{{ $c->seat }}" value="yes" class="sr-only peer" onchange="task_notice({{ $c->seat }})">
                            <div class="text-gray-300 rounded-full peer peer-checked:text-blue-600"><i class="text-2xl fa-solid fa-triangle-exclamation"></i></div>
                        </label>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="sr-only">
    <svg id="check_mark" class="w-3 h-3 text-blue-600 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
    </svg>
</div>
<script nonce="selfhost">
    var classroom = {!! $classroom->toJson(JSON_UNESCAPED_UNICODE) !!};
    var adventure = {!! $adventure->toJson(JSON_UNESCAPED_UNICODE) !!};
    var worksheet = {!! $adventure->worksheet->toJson(JSON_UNESCAPED_UNICODE) !!};
    var tid;
    var characters = new Array();
    @foreach ($characters as $c)
    characters[{{ $c->seat }}] = {!! $c->toJson(JSON_UNESCAPED_UNICODE) !!};
    @endforeach
    var tasks = new Array();
    @foreach ($adventure->worksheet->tasks as $t)
    tasks[{{ $t->id }}] = {!! $t->toJson(JSON_UNESCAPED_UNICODE); !!};
    @endforeach
    var processes = new Array();
    var items = new Array();
    @foreach ($items as $t)
    items[{{ $t->id }}] = {!! $t->toJson(JSON_UNESCAPED_UNICODE); !!};
    @endforeach
    var progress_view = document.getElementById('progress');
    var process_view = document.getElementById('process');
    var check_mark = document.getElementById('check_mark');

    function task_process(tno) {
        var nodes = document.querySelectorAll('button[role="list"]');
        nodes.forEach( (node) => {
            if (node.id == 'list' + tno) {
                node.classList.replace('text-gray-300', 'text-blue-500');
            } else {
                node.classList.replace('text-blue-500', 'text-gray-300');
            }
        });
        if (tno == 0) {
            progress_view.classList.remove('hidden');
            process_view.classList.add('hidden');
            document.getElementById('title').innerHTML = '探險地圖介紹';
            document.getElementById('story').innerHTML = worksheet.intro;
            document.getElementById('task').innerHTML = worksheet.description;
            document.getElementById('reward').innerHTML = '';
            return;
        }
        progress_view.classList.add('hidden');
        process_view.classList.remove('hidden');
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
        window.axios.post('{{ route('game.get_processes') }}', {
            room_id: classroom.id,
            aid: adventure.id,
            tid: tid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            processes = [];
            for (var k in response.data.processes) {
                var p = response.data.processes[k];
                processes[p.seat] = p;
            }
            if (adventure.begin == tno) {
                for (var k in characters) {
                    document.getElementById('char' + k).classList.remove('hidden');
                    if (processes[k]) {
                        if (processes[k].comments != null) {
                            document.getElementById('comment' + k).value = processes[k].comments;
                        } else {
                            document.getElementById('comment' + k).value = '';
                        }
                        if (processes[k].reviewed_at != null) {
                            document.getElementById('ok' + k).checked = true;
                        } else {
                            document.getElementById('ok' + k).checked = false;
                        }
                        if (processes[k].noticed) {
                            document.getElementById('notice' + k).checked = true;
                        } else {
                            document.getElementById('notice' + k).checked = false;
                        }
                    }
                }
            } else {
                for (var k in characters) {
                    document.getElementById('char' + k).classList.add('hidden');
                }
                for (var k in processes) {
                    document.getElementById('char' + k).classList.remove('hidden');
                    if (processes[k].comments != null) {
                        document.getElementById('comment' + k).value = processes[k].comments;
                    } else {
                        document.getElementById('comment' + k).value = '';
                    }
                    if (processes[k].reviewed_at != null) {
                        document.getElementById('ok' + k).checked = true;
                    } else {
                        document.getElementById('ok' + k).checked = false;
                    }
                    if (processes[k].noticed) {
                        document.getElementById('notice' + k).checked = true;
                    } else {
                        document.getElementById('notice' + k).checked = false;
                    }
                }
            }
        });
    }

    function task_comments(seat) {
        var comments = document.getElementById('comment' + seat).value;
        window.axios.post('{{ route('game.process_comments') }}', {
            pid: processes[seat].id,
            comments: comments,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            var process = response.data.process;
            processes[seat] = process;
        });
    }

    function task_notice(seat) {
        var node = document.getElementById('notice' + seat);
        var notice = 'no';
        if (node.checked) {
            notice = 'yes';
        }
        window.axios.post('{{ route('game.process_notice') }}', {
            pid: processes[seat].id,
            notice: notice,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            var process = response.data.process;
            processes[seat] = process;
        });
    }

    function task_review(seat) {
        var node = document.getElementById('ok' + seat);
        var pass = 'no';
        if (node.checked) pass='yes';
        window.axios.post('{{ route('game.process_pass') }}', {
            pid: processes[seat].id,
            pass: pass,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            var process = response.data.process;
            processes[seat] = process;
            if (process.reviewed_at != null) {
                var line = document.getElementById('line' + seat + '_' + process.task_id);
                line.classList.replace('text-gray-600', 'text-blue-600');
                line.classList.replace('after:border-gray-100', 'after:border-blue-300');
                var node = document.getElementById('node' + seat + '_' + process.task_id);
                node.classList.replace('bg-gray-100', 'bg-blue-300');
                var tmp = check_mark.cloneNode(true);
                tmp.removeAttribute('id');
                node.appendChild(tmp);
            } else {
                var line = document.getElementById('line' + seat + '_' + process.task_id);
                line.classList.replace('text-blue-600', 'text-gray-600');
                line.classList.replace('after:border-blue-300', 'after:border-gray-100');
                var node = document.getElementById('node' + seat + '_' + process.task_id);
                node.classList.replace('bg-blue-300', 'bg-gray-100');
                node.innerHTML = '';
            }
        });
    }
</script>
@endsection
