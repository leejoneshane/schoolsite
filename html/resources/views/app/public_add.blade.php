@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增公開課
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public', ['section' => $section]) }}?date={{ $mydate }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-club" action="{{ route('public.add') }}" method="POST" enctype="multipart/form-data">
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
        @foreach ($teacher_list as $t)
        @php
            $gap = '';
            $rname = '';
            if ($t->domain) $rname = $t->domain->name;
            for ($i=0;$i<6-mb_strlen($rname);$i++) {
                $gap .= '　';
            }
            $display = $rname . $gap . $t->realname;
            @endphp
            <option {{ ($teacher->uuid == $t->uuid) ? 'selected' : ''}} value="{{ $t->uuid }}">{{ $display }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="classroom" class="inline">授課班級：</label>
        <select id="classroom" name="classroom" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200" onchange="choise_target()">
            @foreach ($classes as $cls)
            <option value="{{ $cls->id }}"{{ ($cls->id == $teacher->tutor_class) ? ' selected' : '' }}>{{ $cls->name }}</option>
            @endforeach
            <option value="none">特殊需求</option>
        </select>
    </div></p>
    <p><div id="grade" class="p-3 hidden">
        <label for="target" class="inline">教學對象：</label>
        <select id="target" name="target" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($grades as $g)
            <option value="{{ $g->id }}">{{ $g->name . '學生' }}</option>
            @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="domain" class="inline">教學領域：</label>
        <select id="domain" name="domain" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($domains as $d)
            <option value="{{ $d->id }}"{{ ($domain && $d->id == $domain->id) ? ' selected' : '' }}>{{ $d->name }}</option>
            @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="unit" class="inline">單元名稱：</label>
        <input id="unit" class="inline w-96 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="unit" value="" required>
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
        <label class="inline">臺北市國語實驗國民小學公開觀課素養導向教案：</label>
        <input type="file" name="eduplan" accept=".docx" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請上傳 docx 格式檔案！只支援「標楷體、新細明體、微軟正黑體」</span>
    </div></p>
    <p><div class="p-3">
        <label class="inline">臺北市國語實驗國民小學公開課摘要及觀課後會談紀錄：</label>
        <input type="file" name="discuss" accept=".docx" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請上傳 docx 格式檔案！只支援「標楷體、新細明體、微軟正黑體」</span>
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
function choise_target() {
    var grade = document.getElementById('grade');
    var target = document.getElementById('target');
    var elem = document.getElementById('classroom');
    myclass = elem.value;
    if (myclass == 'none') {
        grade.classList.remove('hidden');
    } else {
        target.value = myclass.substr(0,1);
        grade.classList.add('hidden');
    }
}

function remove_teacher(elem) {
    const parent = elem.parentNode;
    const brother = elem.previousElementSibling;
    parent.removeChild(brother);
    parent.removeChild(elem);
}

function add_teacher() {
    var target = document.getElementById('nassign');
    var my_cls = '<select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200" name="teachers[]">';
	@foreach ($teacher_list as $t)
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
</script>
@endsection
