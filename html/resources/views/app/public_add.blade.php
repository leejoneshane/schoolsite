@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增公開課
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public', ['section' => $section]) }}?date={{ $mydate }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-club" action="{{ route('public.add') }}" method="POST">
    @csrf
    <input type="hidden" name="section" value="{{ $section }}">
    <input type="hidden" name="date" value="{{ $mydate }}">
    <input type="hidden" name="weekday" value="{{ $weekday }}">
    <input type="hidden" name="session" value="{{ $session }}">
    <p><div class="p-3">
        <label class="inline">上課時間：{{ $mydate }}</label>
    </div></p>
    <p><div class="p-3">
        <label class="inline">節次：週{{ $weekday }}{{ $sessions[$session]  }}</label>
    </div></p>
    <p><div class="p-3">
        <label for="uuid" class="inline">授課教師：</label>
        <select id="uuid" name="uuid" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        @foreach ($teachers as $t)
        @php
            $gap = '';
            $rname = '';
            if ($t->name) $rname = $t->name;
            for ($i=0;$i<6-mb_strlen($rname);$i++) {
                $gap .= '　';
            }
            $display = $t->name . $gap . $t->realname;
            @endphp
            <option {{ ($teacher->uuid == $t->uuid) ? 'selected' : ''}} value="{{ $t->uuid }}">{{ $display }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="classroom" class="inline">授課班級：</label>
        <select id="classroom" name="classroom" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        @foreach ($classes as $cls)
        <option value="{{ $cls->id }}"{{ ($cls->id == $teacher->tutor_class) ? ' selected' : '' }}>{{ $cls->name }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="domain" class="inline">教學領域：</label>
        <select id="domain" name="domain" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($domains as $d)
            <option value="{{ $d->id }}"{{ ($d->id == $domain->id) ? ' selected' : '' }}>{{ $d->name }}</option>
            @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="unit" class="inline">單元名稱：</label>
        <input id="unit" class="inline w-96 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="unit" value="">
    </div></p>
    <p><div class="p-3">
        <label for="location" class="inline">上課地點：</label>
        <input id="location" class="inline w-96 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="location" value="">
    </div></p>
    <p><div class="p-3">
        <label for="nassign" class="inline">觀課夥伴：</label>
        <div id="nassign">
        </div>
        <button id="nassign" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="add_teacher()"><i class="fa-solid fa-circle-plus"></i>
        </button>
    </div></p>
    <p><div class="p-3">
        <span class="sr-only">請上傳教案：</span>
        <input type="file" name="eduplan" accept=".docx" class="block text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" required>
    </div></p>
    <p><div class="p-3">
        <span class="sr-only">請上傳觀課後會談紀錄：</span>
        <input type="file" name="discuss" accept=".docx" class="block text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" required>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
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
        if ($t->name) $rname = $t->name;
        for ($i=0;$i<6-mb_strlen($rname);$i++) {
            $gap .= '　';
        }
        $display = $t->name . $gap . $t->realname;
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
</script>
@endsection
