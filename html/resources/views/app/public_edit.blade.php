@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯公開課
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public', ['section' => $public->section]) }}?date={{ $public->reserved_at->format('Y-m-d') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="edit-club" action="{{ route('public.edit', ['id' => $public->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="del_eduplan" name="del_eduplan" value="no">
    <input type="hidden" id="del_discuss" name="del_discuss" value="no">
    <p><div class="p-3">
        <label for="date" class="inline">上課時間：</label>
        <input id="date" class="inline rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="date" min="{{ today()->addDays(7)->toDateString() }}" value="{{ $public->reserved_at->format('Y-m-d') }}" onchange="weekday(this)" required>
    </div></p>
    <p><div class="p-3">
        <label for="session" class="inline">週節次：週</label><label id="weekday" class="inline">{{ ['日','一','二','三','四','五','六'][$public->weekday] }}</label>
        <select id="session" name="session" class="form-select w-24 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($sessions as $s => $name)
            <option value="{{ $s }}"{{ ($s == $public->session) ? ' selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="classroom" class="inline">授課班級：</label>
        <select id="classroom" name="classroom" class="form-select w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        @foreach ($classes as $cls)
        <option value="{{ $cls->id }}"{{ ($cls->id == $public->teach_class) ? ' selected' : '' }}>{{ $cls->name }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="unit" class="inline">單元名稱：</label>
        <input id="unit" class="inline w-96 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="unit" value="{{ $public->teach_unit }}" required>
    </div></p>
    <p><div class="p-3">
        <label for="location" class="inline">上課地點：</label>
        <input id="location" class="inline w-96 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="location" value="{{ $public->location }}">
    </div></p>
    <p><div class="p-3">
        <label for="nassign" class="inline">觀課夥伴：</label>
        <div id="nassign">
            @if ($public->teachers())
            @foreach ($public->teachers() as $user)
            <select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="teachers[]">
                @foreach ($teachers as $t)
                @php
                    $gap = '';
                    $rname = '';
                    if ($t->domain) $rname = $t->domain->name;
                    for ($i=0;$i<6-mb_strlen($rname);$i++) {
                        $gap .= '　';
                    }
                    $display = $rname . $gap . $t->realname;
                @endphp
                <option {{ ($user->uuid == $t->uuid) ? 'selected' : ''}} value="{{ $t->uuid }}">{{ $display }}</option>
                @endforeach
            </select>
            <button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_teacher(this);"><i class="fa-solid fa-circle-minus"></i></button>
            @endforeach
            @endif
        </div>
        <button id="nassign" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="add_teacher()"><i class="fa-solid fa-circle-plus"></i>
        </button>
    </div></p>
    <p><div class="p-3">
        <label class="inline">臺北市國語實驗國民小學公開觀課素養導向教案，{{ ($public->eduplan) ? '已上傳' : '未上傳' }}</label>
        <button id="btn_eduplan" type="button" class="inline py-2 px-6 rounded text-blue-300 hover:text-blue-600{{ !($public->eduplan) ? ' hidden' : '' }}"
            onclick="del('eduplan')">刪除
        </button>
        <button type="button" class="inline py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="upload('eduplan')">重新上傳
        </button>
        <div id="show_eduplan" class="hidden">
            <input type="file" id="eduplan" name="eduplan" accept=".docx" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
            <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請上傳 docx 格式檔案！只支援「標楷體、新細明體、微軟正黑體」</span>
        </div>
    </div></p>
    <p><div class="p-3">
        <label class="inline">臺北市國語實驗國民小學公開課摘要及觀課後會談紀錄，{{ ($public->discuss) ? '已上傳' : '未上傳' }}</label>
        <button id="btn_discuss" type="button" class="inline py-2 px-6 rounded text-blue-300 hover:text-blue-600{{ !($public->discuss) ? ' hidden' : '' }}"
            onclick="del('discuss')">刪除
        </button>
        <button type="button" class="inline py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="upload('discuss')">重新上傳
        </button>
        <div id="show_discuss" class="hidden">
            <input type="file" name="discuss" accept=".docx" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
            <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請上傳 docx 格式檔案！只支援「標楷體、新細明體、微軟正黑體」</span>
        </div>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                確認
            </button>
        </div>
    </p>
</form>
<script>
    function remove_teacher(elem) {
    const parent = elem.parentNode;
    const brother = elem.previousElementSibling;
    parent.removeChild(brother);
    parent.removeChild(elem);
}

function add_teacher() {
    var target = document.getElementById('nassign');
    var my_cls = '<select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200" name="teachers[]">';
	@foreach ($teachers as $t)
    @php
        $gap = '';
        $rname = '';
        if ($t->domain) $rname = $t->domain->name;
        for ($i=0;$i<6-mb_strlen($rname);$i++) {
            $gap .= '　';
        }
        $display = $rname . $gap . $t->realname;
    @endphp
	my_cls += '<option value="{{ $t->uuid }}">{{ $display }}</option>';
	@endforeach
	my_cls += '</select>';
    const elemc = document.createElement('select');
    target.parentNode.insertBefore(elemc, target);
    elemc.outerHTML = my_cls;
	my_btn = '<button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_teacher(this);"><i class="fa-solid fa-circle-minus"></i></button>';
    const elemb = document.createElement('button');
    target.parentNode.insertBefore(elemb, target);
    elemb.outerHTML = my_btn;
}

function del(type) {
    var target = document.getElementById('del_' + type);
    target.value = 'yes';
    var btn = document.getElementById('btn_' + type);
    btn.setAttribute('disabled', true);
    btn.innerHTML = '舊檔案將刪除';
}

function upload(type) {
    var target = document.getElementById('show_' + type);
    target.classList.remove("hidden");
    var btn = document.getElementById('btn_' + type);
    btn.setAttribute('disabled', true);
    btn.innerHTML = '舊檔案將刪除';
}

function weekday(obj) {
    var target = document.getElementById('weekday');
    var mydate = obj.value;
    const dayOfWeek = new Date(mydate).getDay();
    target.innerHTML = ['日','一','二','三','四','五','六'][dayOfWeek];
}

</script>
@endsection
