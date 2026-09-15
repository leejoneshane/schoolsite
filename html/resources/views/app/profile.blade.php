@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">個人資料維護
    <a class="text-sm py-2 px-6 rounded text-blue-500 hover:text-blue-700 dark:text-blue-400" href="{{ $referer }}">
        <i class="fa-solid fa-arrow-left"></i> 返回上一頁
    </a>
</div>
<form id="edit-profile" action="{{ route('profile.update') }}" method="POST">
    @csrf
    <input type="hidden" name="referer" value="{{ urlencode($referer) }}">
    <label for="idno" class="inline p-2">身分證字號：</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="idno" value="{{ $teacher->idno }}">
    <label for="sn" class="inline p-2">姓氏：</label>
    <input class="inline w-20 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="sn" value="{{ $teacher->sn }}">
    <label for="gn" class="inline p-2">名字：</label>
    <input class="inline w-20 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="gn" value="{{ $teacher->gn }}">
    <label class="inline p-2">性別</label>
    <select class="inline rounded w-24 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        name="gender">
        <option value="0"{{ $teacher->gender == 0 ? ' selected' : '' }}>未知</option>
        <option value="1"{{ $teacher->gender == 1 ? ' selected' : '' }}>男</option>
        <option value="2"{{ $teacher->gender == 2 ? ' selected' : '' }}>女</option>
        <option value="9"{{ $teacher->gender == 9 ? ' selected' : '' }}>其它</option>
    </select>
    <label class="inline p-2">出生日期</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="birth" value="{{ $teacher->birthdate ? (is_string($teacher->birthdate) ? $teacher->birthdate : $teacher->birthdate->format('Y-m-d')) : '' }}">
    
    <p class="p-2"><label for="domain" class="inline">隸屬領域：</label>
        <select id="domain" name="domain" class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        <option value="">無</option>
        @foreach ($domains as $dom)
            <option value="{{ $dom->id }}"{{ $teacher->domain && $teacher->domain->id == $dom->id  ? ' selected' : '' }}>{{ $dom->name }}</option>
        @endforeach
        </select>
    </p>
    <p class="p-2"><label for="roles" class="inline">配課資訊：</label>
        @foreach ($assignment as $assign)
        <select class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="classes[]">
            @foreach ($classes as $cls)
            <option value="{{ $cls->id }}"{{ $assign->class_id == $cls->id  ? ' selected' : '' }}>{{ $cls->name }}</option>
            @endforeach
        </select>
        <select class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="subjects[]">
            @foreach ($subjects as $subj)
            <option value="{{ $subj->id }}"{{ $assign->subject_id == $subj->id  ? ' selected' : '' }}>{{ $subj->name }}</option>
            @endforeach
        </select>
        @if (!$loop->first)
        <button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_assign(this);"><i class="fa-solid fa-circle-minus"></i></button>
        @endif
        @endforeach
        <button id="nassign" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="add_assign()"><i class="fa-solid fa-circle-plus"></i>
        </button>
    </p>
    <label class="inline p-2">電子郵件</label>
    <input class="inline w-60 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="email" value="{{ $teacher->email }}">
    <label class="inline p-2">行動電話</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="mobile" value="{{ $teacher->mobile }}">
    <label class="inline p-2">聯絡電話</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="telephone" value="{{ $teacher->telephone }}">
    <p class="p-2">
    <label class="inline">通訊地址</label>
    <input class="inline w-72 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="address" value="{{ $teacher->address }}">
    <label class="inline p-2">個人網址</label>
    <input class="inline w-72 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="www" value="{{ $teacher->www }}">
    </p>
    <p class="py-4 px-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            修改
        </button>
    </p>
</form>
<script nonce="selfhost">
function remove_assign(elem) {
    const parent = elem.parentNode;
    const brother = elem.previousElementSibling;
    const big_brother = brother.previousElementSibling;
    parent.removeChild(big_brother);
    parent.removeChild(brother);
    parent.removeChild(elem);
}

function add_assign() {
    var target = document.getElementById('nassign');
    var my_cls = '<select class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200" name="classes[]">';
	@foreach ($classes as $cls)
	my_cls += '<option value="{{ $cls->id }}">{{ $cls->name }}</option>';
	@endforeach
	my_cls += '</select>';
    const elemc = document.createElement('select');
    target.parentNode.insertBefore(elemc, target);
    elemc.outerHTML = my_cls;
    var my_subj = '<select class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200" name="subjects[]">';
	@foreach ($subjects as $subj)
	my_subj += '<option value="{{ $subj->id }}">{{ $subj->name }}</option>';
	@endforeach
	my_subj += '</select>';
    const elems = document.createElement('select');
    target.parentNode.insertBefore(elems, target);
    elems.outerHTML = my_subj;
	my_btn = '<button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_assign(this);"><i class="fa-solid fa-circle-minus"></i></button>';
    const elemb = document.createElement('button');
    target.parentNode.insertBefore(elemb, target);
    elemb.outerHTML = my_btn;
}
</script>
@endsection
