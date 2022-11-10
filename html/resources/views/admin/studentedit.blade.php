@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    學生
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ $referer }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="edit-student" action="{{ route('students.edit', ['uuid' => $student->uuid]) }}" method="POST">
    @csrf
    <input type="hidden" name="referer" value="{{ urlencode($referer) }}">
    <label for="idno" class="inline p-2">身分證字號：</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="idno" value="{{ $student->idno }}">
    <label for="sn" class="inline p-2">姓氏：</label>
    <input class="inline w-20 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="sn" value="{{ $student->sn }}">
    <label for="gn" class="inline p-2">名字：</label>
    <input class="inline w-20 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="gn" value="{{ $student->gn }}">
    <label class="inline p-2">性別</label>
    <select class="inline rounded w-24 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        name="gender">
        <option value="0"{{ $student->gender == 0 ? ' selected' : '' }}>未知</option>
        <option value="1"{{ $student->gender == 1 ? ' selected' : '' }}>男</option>
        <option value="2"{{ $student->gender == 2 ? ' selected' : '' }}>女</option>
        <option value="9"{{ $student->gender == 9 ? ' selected' : '' }}>其它</option>
    </select>
    <label class="inline p-2">出生日期</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="birth" value="{{ $student->birthdate }}">
    <p class="p-2"><label for="myclass" class="inline">就讀班級：</label>
    <select class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        name="myclass">
        @foreach ($classes as $cls)
        <option value="{{ $cls->id }}"{{ $student->class_id == $cls->id  ? ' selected' : '' }}>{{ $cls->name }}</option>
        @endforeach
    </select>
    <label for="seat" class="inline px-3">座號：</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
      type="text" name="seat" value="{{ $student->seat }}">
    <label for="character" class="inline px-3">特殊身分註記：</label>
        @php
            $characters = explode(',', $student->character);    
        @endphp
        @foreach ($characters as $cht)
        <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="text" name="character[]" value="{{ $cht }}">
        <button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_character(this);"><i class="fa-solid fa-circle-minus"></i></button>
        @endforeach
        <button id="ncharacter" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="add_character()"><i class="fa-solid fa-circle-plus"></i>
        </button>
    </p>
    <label class="inline p-2">電子郵件</label>
    <input class="inline w-60 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="email" value="{{ $student->email }}">
    <label class="inline p-2">行動電話</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="mobile" value="{{ $student->mobile }}">
    <label class="inline p-2">聯絡電話</label>
    <input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="telephone" value="{{ $student->telephone }}">
    <p class="p-2">
    <label class="inline">通訊地址</label>
    <input class="inline w-72 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="address" value="{{ $student->address }}">
    <label class="inline p-2">個人網址</label>
    <input class="inline w-72 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="www" value="{{ $student->www }}">
    </p>
    <p class="py-4 px-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            修改
        </button>
    </p>
</form>
<script>
function remove_character(elem) {
    const parent = elem.parentNode;
    const child = elem.previousElementSibling;
    parent.removeChild(child);
    parent.removeChild(elem);
}

function add_character() {
    var target = document.getElementById('ncharacter');
    var my_input = '<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" type="text" name="character[]">';
    const elem = document.createElement('input');
    target.parentNode.insertBefore(elem, target);
    elem.outerHTML = my_input;
	my_btn = '<button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_character(this);"><i class="fa-solid fa-circle-minus"></i></button>';
    const elemb = document.createElement('button');
    target.parentNode.insertBefore(elemb, target);
    elemb.outerHTML = my_btn;
}
</script>
@endsection
