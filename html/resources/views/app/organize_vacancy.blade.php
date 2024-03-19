@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    職缺設定
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize') }}">
        <i class="fa-solid fa-eject"></i>回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.setting') }}">
        <i class="fa-regular fa-calendar-days"></i>流程控制
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.arrange') }}">
        <i class="fa-solid fa-puzzle-piece"></i>職務編排
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listvacancy') }}">
        <i class="fa-solid fa-square-poll-horizontal"></i>職缺一覽表
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listresult') }}">
        <i class="fa-solid fa-user-check"></i>職編結果一覽表
    </a>
</div>
<div class="w-full text-red-500 border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        職務缺額已經比照前一年度計算完畢，如有員額增加或裁減，<a class="text-sm rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.reset') }}">請按這裡重新計算</a>，所有已編排之職務將會完全清除，但不影響已經填交之意願調查表。
    </p>
</div>
<table class="w-full p-4 text-left font-normal">
    <caption class="font-semibold text-xl">行政職缺</caption>
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="w-32 p-2">
            職務
        </th>
        <th scope="col" class="w-12 p-2">
            選填階段
        </th>
        <th scope="col" class="w-12 p-2">
            特殊任務
        </th>
        <th scope="col" class="w-12 p-2">
            員額編制
        </th>
        <th scope="col" class="p-2">
            任職人員
        </th>
    </tr>
    @foreach ($admins as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2">
            <input class="inline w-12 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="stage{{ $v->id }}" value="{{ $v->stage }}" required>
        </td>
        <td class="p-2">
            <label for="special{{ $v->id }}" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="special{{ $v->id }}" name="special{{ $v->id }}" value="yes" class="sr-only peer"{{ ($v->special) ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            </label>
        </td>
        <td class="p-2">
            <input class="inline w-12 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="shortfall{{ $v->id }}" value="{{ $v->shortfall }}" required>
        </td>
        <td class="p-2">
            @foreach ($v->original as $t)
            <span class="pl-4">{{ $t->realname }}</span>
            @if ($v->reserved->contains($t))
            <button id="{{ $t->uuid }}" name="swap{{ $v->id }}" value="release" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                開缺
            </button>
            @else
            <button id="{{ $t->uuid }}" name="swap{{ $v->id }}" value="reserve" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                保留
            </button>
            @endif
            @endforeach
            @if ($v->reserved->count() > 1)
            <input type="button" id="all{{ $v->id }}" value="全部開缺" class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">
            @endif
        </td>
    </tr>
    @endforeach
</table>
<table class="w-full p-4 text-left font-normal">
    <caption class="font-semibold text-xl">級任導師</caption>
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="w-32 p-2">
            年級
        </th>
        <th scope="col" class="w-12 p-2">
            選填階段
        </th>
        <th scope="col" class="w-12 p-2">
            特殊任務
        </th>
        <th scope="col" class="w-12 p-2">
            員額編制
        </th>
        <th scope="col" class="p-2">
            任職人員（系統已自動進行原班升級並將一、三、五年級導師開缺）
        </th>
    </tr>
    @foreach ($tutors as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2">
            <input class="inline w-12 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="stage{{ $v->id }}" value="{{ $v->stage }}" required>
        </td>
        <td class="p-2">
            <label for="special{{ $v->id }}" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="special{{ $v->id }}" name="special{{ $v->id }}" value="yes" class="sr-only peer"{{ ($v->special) ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            </label>
        </td>
        <td class="p-2">
            <input class="inline w-12 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="shortfall{{ $v->id }}" value="{{ $v->shortfall }}" required>
        </td>
        <td class="p-2">
            @foreach ($v->original as $t)
            <span class="pl-4">{{ $t->realname }}</span>
            @if ($v->reserved->contains($t))
            <button id="{{ $t->uuid }}" name="swap{{ $v->id }}" value="release" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                開缺
            </button>
            @else
            <button id="{{ $t->uuid }}" name="swap{{ $v->id }}" value="reserve" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                保留
            </button>
            @endif
            @endforeach
            @if ($v->reserved->count() > 1)
            <input type="button" id="all{{ $v->id }}" value="全部開缺" class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">
            @endif
        </td>
    </tr>
    @endforeach
</table>
<table class="w-full p-4 text-left font-normal">
    <caption class="font-semibold text-xl">領域教師</caption>
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="w-32 p-2">
            領域
        </th>
        <th scope="col" class="w-12 p-2">
            選填階段
        </th>
        <th scope="col" class="w-12 p-2">
            特殊任務
        </th>
        <th scope="col" class="w-12 p-2">
            員額編制
        </th>
        <th scope="col" class="p-2">
            任職人員
        </th>
    </tr>
    @foreach ($domains as $v)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            {{ $v->name }}
        </td>
        <td class="p-2">
            <input class="inline w-12 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="stage{{ $v->id }}" value="{{ $v->stage }}" required>
        </td>
        <td class="p-2">
            <label for="special{{ $v->id }}" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="special{{ $v->id }}" name="special{{ $v->id }}" value="yes" class="sr-only peer"{{ ($v->special) ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            </label>
        </td>
        <td class="p-2">
            <input class="inline w-12 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="shortfall{{ $v->id }}" value="{{ $v->shortfall }}" required>
        </td>
        <td class="p-2">
            @foreach ($v->original as $t)
            <span class="pl-4">{{ $t->realname }}</span>
            @if ($v->reserved->contains($t))
            <button id="{{ $t->uuid }}" name="swap{{ $v->id }}" value="release" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                開缺
            </button>
            @else
            <button id="{{ $t->uuid }}" name="swap{{ $v->id }}" value="reserve" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                保留
            </button>
            @endif
            @endforeach
            @if ($v->reserved->count() > 1)
            <input type="button" id="all{{ $v->id }}" value="全部開缺" class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">
            @endif
        </td>
    </tr>
    @endforeach
</table>
<script>
    window.onload = function () {
        var elm = document.querySelectorAll("select");
        for (var i = 0; i < elm.length; i++) {
            elm[i].addEventListener("change", stage);
        }
        var elm = document.querySelectorAll("input[type=checkbox]");
        for (var i = 0; i < elm.length; i++) {
            elm[i].addEventListener("change", special);
        }
        var elm = document.querySelectorAll("input[type=text]");
        for (var i = 0; i < elm.length; i++) {
            elm[i].addEventListener("change", shortfall);
        }
        var elm = document.querySelectorAll("button");
        for (var i = 0; i < elm.length; i++) {
            elm[i].addEventListener("click", swap);
        }
        var elm = document.querySelectorAll("input[type=button]");
        for (var i = 0; i < elm.length; i++) {
            elm[i].addEventListener("click", all);
        }
    };

    function stage(event) {
        window.axios.post('{{ route('organize.stage') }}', {
            vid: event.target.name.substr(5),
            stage: event.target.value,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
    function special(event) {
        if (event.target.checked) {
            window.axios.post('{{ route('organize.special') }}', {
                vid: event.target.name.substr(7),
                special: 'yes',
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        } else {
            window.axios.post('{{ route('organize.special') }}', {
                vid: event.target.name.substr(7),
                special: 'no',
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }
    function shortfall(event) {
        if (event.target.value !== '') {
            window.axios.post('{{ route('organize.shortfall') }}', {
                vid: event.target.name.substr(9),
                shortfall: event.target.value,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }
    function swap(event) {
        if (event.target.value == 'release') {
            event.target.value = 'reserve';
            event.target.innerText = '保留';
            window.axios.post('{{ route('organize.release') }}', {
                vid: event.target.name.substr(4),
                uuid: event.target.id,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        } else {
            event.target.value = 'release';
            event.target.innerText = '開缺';
            window.axios.post('{{ route('organize.reserve') }}', {
                vid: event.target.name.substr(4),
                uuid: event.target.id,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }
    function all(event) {
        var myvid = event.target.id.substr(3);
        var elm = document.querySelectorAll("button[name='swap" + myvid + "']");
        for (var i = 0; i < elm.length; i++) {
            elm[i].value = 'reserve';
            elm[i].innerText = '保留';
        }
        window.axios.post('{{ route('organize.releaseall') }}', {
            vid: myvid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
</script>
@endsection
