@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯公開課
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public', ['section' => $public->section]) }}?date={{ $public->reserved_at->format('Y-m-d') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="edit-club" action="{{ route('public.edit', ['id' => $public->id]) }}" method="POST">
    @csrf
    <input type="hidden" id="del_eduplan" name="del_eduplan" value="no">
    <input type="hidden" id="del_discuss" name="del_discuss" value="no">
    <p><div class="p-3">
        <label for="date" class="inline">上課時間：</label>
        <input id="date" class="inline rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="date" value="{{ $public->reserved_at->format('Y-m-d') }}" required>
    </div></p>
    <p><div class="p-3">
        <label for="weekday" class="inline">週節次：週</label>
        <select id="weekday" name="weekday" class="form-select w-16 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @for ($i=1; $i<6; $i++)
            <option value="{{ $i }}"{{ ($i == $public->weekday) ? ' selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
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
        </div>
        <button id="nassign" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="add_teacher()"><i class="fa-solid fa-circle-plus"></i>
        </button>
    </div></p>
    <p><div class="p-3">
        <label class="inline">{{ ($public->eduplan) ? '教案已上傳' : '尚未上傳教案' }}</label>
        @if ($public->eduplan)
        <button id="btn_del1" type="button" class="inline py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="del_eduplan()">刪除
        </button>
        @endif
        <button type="button" class="inline py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="upload_eduplan()">重新上傳
        </button>
        <div id="show_eduplan" class="hidden">
            <span class="sr-only">請上傳檔案：</span>
            <input type="file" id="eduplan" name="eduplan" accept=".docx" class="block text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
        </div>
    </div></p>
    <p><div class="p-3">
        <label class="inline">{{ ($public->eduplan) ? '教案已上傳' : '尚未上傳教案' }}</label>
        @if ($public->discuss)
        <button id="btn_del1" type="button" class="inline py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="del_discuss()">刪除
        </button>
        @endif
        <button type="button" class="inline py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="upload_discuss()">重新上傳
        </button>
        <div id="show_discuss" class="hidden">
            <span class="sr-only">請上傳觀課後會談紀錄：</span>
            <input type="file" name="discuss" accept=".docx" class="block text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
        </div>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
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

function upload_eduplan() {
    var target = document.getElementById('show_eduplan');
    target.classList.remove("hidden");
    var btn = document.getElementById('btn_del1');
    btn.setAttribute('disabled', true);
    btn.innerHTML = '舊檔案將刪除';
}

function del_eduplan() {
    var target = document.getElementById('del_eduplan');
    target.value = 'yes';
    var btn = document.getElementById('btn_del1');
    btn.setAttribute('disabled', true);
    btn.innerHTML = '舊檔案將刪除';
}

function upload_discuss() {
    var target = document.getElementById('show_discuss');
    target.classList.remove("hidden");
    var btn = document.getElementById('btn_del2');
    btn.setAttribute('disabled', true);
    btn.innerHTML = '舊檔案將刪除';
}

function del_discuss() {
    var target = document.getElementById('del_discuss');
    target.value = 'yes';
    var btn = document.getElementById('btn_del2');
    btn.setAttribute('disabled', true);
    btn.innerHTML = '舊檔案將刪除';
}

</script>
@endsection
