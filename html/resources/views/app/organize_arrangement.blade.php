@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    職務編排
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize') }}">
        <i class="fa-solid fa-eject"></i>回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.vacancy') }}">
        <i class="fa-solid fa-chair"></i>職缺設定
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.setting') }}">
        <i class="fa-regular fa-calendar-days"></i>流程控制
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listvacancy') }}">
        <i class="fa-solid fa-square-poll-horizontal"></i>職缺一覽表
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listresult') }}">
        <i class="fa-solid fa-user-check"></i>職編結果一覽表
    </a>
</div>
<div class="w-full">
    <span class="p-2">
        @if (!$flow)
        <span class="text-red-700">尚未設定時程，請洽教務處詢問！</span>
        @elseif (!$seniority)
        <span class="text-red-700">尚未統計年資，請洽教務處詢問！</span>
        @else
        目前進度：
        <span class="text-green-700">
        {{ ($flow->notStart()) ? '意願調查尚未開始！' : '' }}
        {{ ($flow->onSurvey()) ? '填寫學經歷資料、年資積分' : '' }}
        {{ ($flow->onFirstStage()) ? '行政與特殊任務意願調查（第一階段）' : '' }}
        {{ ($flow->onPause()) ? '第一階段意願調查已經結束，請等候第二階段意願調查！' : '' }}
        {{ ($flow->onSecondStage()) ? '級科任意願調查（第二階段）' : '' }}
        {{ ($flow->onFinish()) ? '意願調查已經結束！' : '' }}
        </span>
        @endif
    </span>
    <span class="p-2">
        編排完成率：{{ $completeness->completeness }}％
    </span>
</div>
@if ($flow && $seniority && ($flow->onPeriod() || $flow->onFinish()))
<form method="POST" action="{{ route('organize.arrange') }}">
    @csrf
    <div class="w-full p-2">
        正在進行：
        <select name="display" onchange="
            var display = this.value;
            window.location.replace('{{ route('organize.arrange') }}' + '/' + display);
        ">
            @if ($flow->onPause() || $flow->onFinish())
            <option value="first"{{ ($display == 'first') ? ' selected' : '' }}>第一志願錄取作業</option>
            <option value="second"{{ ($display == 'second') ? ' selected' : '' }}>第二志願錄取作業</option>
            <option value="third"{{ ($display == 'third') ? ' selected' : '' }}>第三志願錄取作業</option>
            @endif
            @if ($flow->onFinish())
            <option value="four"{{ ($display == 'four') ? ' selected' : '' }}>第四志願錄取作業</option>
            <option value="five"{{ ($display == 'five') ? ' selected' : '' }}>第五志願錄取作業</option>
            <option value="six"{{ ($display == 'six') ? ' selected' : '' }}>第六志願錄取作業</option>
            @endif
            <option value="seven"{{ ($display == 'seven') ? ' selected' : '' }}>預排或缺額補滿</option>
        </select>
    </div>
</form>
<table class="w-full p-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="w-32 p-2">
            職務
        </th>
        <th scope="col" class="w-24 p-2">
            缺額
        </th>
        <th scope="col" class="w-24 p-2">
            已編排
        </th>
        <th scope="col" class="p-2">
            意願選填結果（<span class="bg-red-200">　</span>為第一志願，<span class="bg-orange-200">　</span>為第二志願，<span class="bg-yellow-200">　</span>為第三志願，<span class="bg-green-200">　</span>為第四志願，<span class="bg-blue-200">　</span>為第五志願，<span class="bg-purple-200">　</span>為第六志願）
        </th>
    </tr>
    @if ($flow->onPause() && $display != 'seven')
    @foreach ($stage1->general as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall - $v->filled }}
        </td>
        <td class="p-2 text-center">
            {{ $v->assigned }}
        </td>
        <td class="p-2">
        @foreach ($v->reserved() as $t)
            <span class="pl-4 text-gray-500">{{ $t->realname }}</span>
        @endforeach
        @foreach ($teachers[$v->id] as $t)
            @if ($t->admin1 == $v->id)
            <span class="pl-4 bg-red-200">
            @elseif ($t->admin2 == $v->id)
            <span class="pl-4 bg-orange-200">
            @elseif ($t->admin3 == $v->id)
            <span class="pl-4 bg-yellow-200">
            @endif
                <input id='s{{ $v->id }}_{{ $t->uuid }}' type='checkbox'{{ ($t->assign == $v->id) ? ' checked' : '' }}>
                <button id='{{ $t->uuid }}' data-modal-toggle="defaultModal">
                    {{ $t->teacher->realname }}
                </button>
            </span>
        @endforeach
        </td>
    </tr>
    @endforeach
    @foreach ($stage1->special as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall - $v->filled }}
        </td>
        <td class="p-2 text-center">
            {{ $v->assigned }}
        </td>
        <td class="p-2">
        @foreach ($v->reserved() as $t)
            <span class="pl-4 text-gray-500">{{ $t->realname }}</span>
        @endforeach
        @foreach ($teachers[$v->id] as $t)
            <span class="pl-4 bg-red-200">
                <input id='s{{ $v->id }}_{{ $t->uuid }}' type='checkbox'{{ ($t->assign == $v->id) ? ' checked' : '' }}>
                <button id='{{ $t->uuid }}' data-modal-toggle="defaultModal">
                    {{ $t->teacher->realname }}
                </button>
            </span>
        @endforeach
        </td>
    </tr>
    @endforeach
    @elseif ($display == 'seven')
    @foreach ($stage1->general as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall - $v->filled }}
        </td>
        <td class="p-2 text-center">
            {{ $v->assigned }}
        </td>
        <td class="p-2">
        @foreach ($v->reserved() as $t)
            <span class="pl-4 text-gray-500">{{ $t->realname }}</span>
        @endforeach
        @foreach ($teachers[$v->id] as $t)
            <span class="pl-4 text-gray-500">
                {{ $t->teacher->realname }}
            </span>
        @endforeach
        @if ($v->assigned < $v->shortfall - $v->filled)
        @for ($z=0;$z<($v->shortfall - $v->filled - $v->assigned);$z++)
            <select id="t{{ $v->id }}_{{ $z }}">
                <option value="">未指派</option>
                @foreach ($rest_teachers as $t)
                <option value="{{ $t->uuid }}">{{ $t->teacher->realname }}</option>
                @endforeach
            </select>
        @endfor
        @endif
    @endforeach
    @foreach ($stage1->special as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall - $v->filled }}
        </td>
        <td class="p-2 text-center">
            {{ $v->assigned }}
        </td>
        <td class="p-2">
        @foreach ($v->reserved() as $t)
            <span class="pl-4 text-gray-500">{{ $t->realname }}</span>
        @endforeach
        @foreach ($teachers[$v->id] as $t)
            <span class="pl-4 text-gray-500">
                {{ $t->teacher->realname }}
            </span>
        @endforeach
        @if ($v->assigned < $v->shortfall - $v->filled)
        @for ($z=0;$z<($v->shortfall - $v->filled - $v->assigned);$z++)
            <select id="t{{ $v->id }}_{{ $z }}">
                <option value="">未指派</option>
                @foreach ($rest_teachers as $t)
                <option value="{{ $t->uuid }}">{{ $t->teacher->realname }}</option>
                @endforeach
            </select>
        @endfor
        @endif
        </td>
    </tr>
    @endforeach
    @endif

    @if ($flow->onFinish() && $display != 'seven')
    @foreach ($stage2->special as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall - $v->filled }}
        </td>
        <td class="p-2 text-center">
            {{ $v->assigned }}
        </td>
        <td class="p-2">
        @foreach ($v->reserved() as $t)
            <span class="pl-4 text-gray-500">{{ $t->realname }}</span>
        @endforeach
        @foreach ($teachers[$v->id] as $t)
            <span class="pl-4 bg-red-200">
                <input id='s{{ $v->id }}_{{ $t->uuid }}' type='checkbox'{{ ($t->assign == $v->id) ? ' checked' : '' }}>
                <button id='{{ $t->uuid }}' data-modal-toggle="defaultModal">
                    {{ $t->teacher->realname }}
                </button>
            </span>
        @endforeach
        </td>
    </tr>
    @endforeach
    @foreach ($stage2->general as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall - $v->filled }}
        </td>
        <td class="p-2 text-center">
            {{ $v->assigned }}
        </td>
        <td class="p-2">
        @foreach ($v->reserved() as $t)
            <span class="pl-4 text-gray-500">{{ $t->realname }}</span>
        @endforeach
        @foreach ($teachers[$v->id] as $t)
            @if ($t->teach1 == $v->id)
            <span class="pl-4 bg-red-200">
            @elseif ($t->teach2 == $v->id)
            <span class="pl-4 bg-orange-200">
            @elseif ($t->teach3 == $v->id)
            <span class="pl-4 bg-yellow-200">
            @elseif ($t->teach4 == $v->id)
            <span class="pl-4 bg-green-200">
            @elseif ($t->teach5 == $v->id)
            <span class="pl-4 bg-blue-200">
            @elseif ($t->teach6 == $v->id)
            <span class="pl-4 bg-purple-200">
            @endif
                <input id='s{{ $v->id }}_{{ $t->uuid }}' type='checkbox'{{ ($t->assign == $v->id) ? ' checked' : '' }}>
                <button id='{{ $t->uuid }}' data-modal-toggle="defaultModal">
                    {{ $t->teacher->realname }}
                </button>
            </span>
        @endforeach
        </td>
    </tr>
    @endforeach
    @elseif ($display == 'seven')
    @foreach ($stage2->general as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall - $v->filled }}
        </td>
        <td class="p-2 text-center">
            {{ $v->assigned }}
        </td>
        <td class="p-2">
        @foreach ($v->reserved() as $t)
            <span class="pl-4 text-gray-500">{{ $t->realname }}</span>
        @endforeach
        @foreach ($teachers[$v->id] as $t)
            <span class="pl-4 text-gray-500">
                {{ $t->teacher->realname }}
            </span>
        @endforeach
        @if ($v->assigned < $v->shortfall - $v->filled)
        @for ($z=0;$z<($v->shortfall - $v->filled - $v->assigned);$z++)
            <select id="t{{ $v->id }}_{{ $z }}">
                <option value="">未指派</option>
                @foreach ($rest_teachers as $t)
                <option value="{{ $t->uuid }}">{{ $t->teacher->realname }}</option>
                @endforeach
            </select>
        @endfor
        @endif
    @endforeach
    @foreach ($stage2->special as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2 text-center">
            {{ $v->shortfall - $v->filled }}
        </td>
        <td class="p-2 text-center">
            {{ $v->assigned }}
        </td>
        <td class="p-2">
        @foreach ($v->reserved() as $t)
            <span class="pl-4 text-gray-500">{{ $t->realname }}</span>
        @endforeach
        @foreach ($teachers[$v->id] as $t)
            <span class="pl-4 text-gray-500">
                {{ $t->teacher->realname }}
            </span>
        @endforeach
        @if ($v->assigned < $v->shortfall - $v->filled)
        @for ($z=0;$z<($v->shortfall - $v->filled - $v->assigned);$z++)
            <select id="t{{ $v->id }}_{{ $z }}">
                <option value="">未指派</option>
                @foreach ($rest_teachers as $t)
                <option value="{{ $t->uuid }}">{{ $t->teacher->realname }}</option>
                @endforeach
            </select>
        @endfor
        @endif
        </td>
    </tr>
    @endforeach
    @endif
</table>
<div id="defaultModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 id="modalHeader" class="text-xl font-semibold text-gray-900 dark:text-white">
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="defaultModal">
                    <i class="fa-solid fa-xmark"></i>
                    <span class="sr-only">關閉視窗</span>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <p id="modalBody" class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                </p>
            </div>
        </div>
    </div>
</div>
<script>
window.old = [];
window.onload = function () {
    var elm = document.querySelectorAll("input[type=checkbox]");
    for (var i = 0; i < elm.length; i++) {
        elm[i].addEventListener("click", arrange);
    }
    var elm = document.querySelectorAll("button");
    for (var i = 0; i < elm.length; i++) {
        elm[i].addEventListener("click", showSurvey);
    }
    var elm = document.querySelectorAll("select");
    for (var i = 0; i < elm.length; i++) {
        elm[i].addEventListener("focus", saveValue);
        elm[i].addEventListener("click", assign);
    }
};

function arrange(event) {
    var myid = event.target.id;
    var vid = myid.substring(1, myid.indexOf('_'));
    var uuid = myid.substring(myid.indexOf('_') + 1);
    if (event.target.checked) {
        window.axios.post('{{ route('organize.assign') }}', {
            vid: vid,
            uuid: uuid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    } else {
        window.axios.post('{{ route('organize.unassign') }}', {
            vid: vid,
            uuid: uuid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
}

function saveValue(event) {
    var myid = event.target.id;
    var prev = event.target.value;
    window.old[myid] = prev;
}

function assign(event) {
    var myid = event.target.id;
    var prev = window.old[myid];
    var vid = myid.substring(1, myid.indexOf('_'));
    var uuid = event.target.value;
    if (prev != uuid) {
        window.axios.post('{{ route('organize.assign') }}', {
            vid: vid,
            uuid: uuid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        window.axios.post('{{ route('organize.unassign') }}', {
            vid: vid,
            uuid: prev,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
}

function showSurvey(event) {
    window.axios.post('{{ route('organize.listsurvey') }}', {
        uuid: event.target.id,
    }, {
        headers: {
            'Content-Type': 'application/json;charset=utf-8',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(function (response) {
        document.getElementById('modalHeader').innerHTML = response.data.header;
        document.getElementById('modalBody').innerHTML = response.data.body;
    });
}
</script>
@endif
@endsection
