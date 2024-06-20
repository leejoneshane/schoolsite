@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    將公假單授權給學生社團
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full py-2 text-left font-normal">
    <span class="font-semibold">權限代碼：</span>{{ $permission->group }}.{{ $permission->permission }}　
    <span class="font-semibold">權限描述：</span>{{ $permission->description }}
</div>
<form id="edit-teacher" action="{{ route('dayoff.permission') }}" method="POST">
    @csrf
    <p class="p-2">
        <label for="roles" class="inline">已授權人員：</label>
        <div id="nassign">
        @foreach ($already as $user)
        <select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        name="teachers[]">
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
            <option {{ ($user->uuid == $t->uuid) ? 'selected' : ''}} value="{{ $t->uuid }}">{{ $display }}</option>
            @endforeach
        </select>
        <button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_teacher(this);"><i class="fa-solid fa-circle-minus"></i></button>
        @endforeach
        </div>
        <button id="nassign" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="add_teacher()"><i class="fa-solid fa-circle-plus"></i>
        </button>
    </p>
    <p class="py-4 px-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            修改
        </button>
    </p>
</form>
<script nonce="selfhost">
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
