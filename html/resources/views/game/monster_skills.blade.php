@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    設定技能
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.monsters') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<h1 class="text-xl">怪物種族：
    <select class="form-select w-48 m-0 px-3 py-2 text-xl font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    name="profession" onchange="
        var selection = this.value;
        window.location.replace('{{ route('game.monster_skills') }}/' + selection );
    ">
        @foreach ($monsters as $m)
        <option{{ ($m->id == $monster->id) ? ' selected' : '' }} value="{{ $m->id }}">{{ $m->name }}</option>
        @endforeach
    </select>
</h1>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        請先到<a href="{{ route('game.skills') }}">技能頁面</a>新增技能，然後再將技能分配給怪物。
    </p>
</div>
<form id="edit-teacher" action="{{ route('game.monster_skills', ['monster_id' => $monster->id]) }}" method="POST">
    @csrf
    <p class="p-2">
        <label class="inline">技能列表：</label>
        <div id="nassign">
        @foreach ($monster->skills as $already)
        <label>等級
            <input type="number" min="1" max="30" step="1" id="level" name="level[]" value="{{ $already->pivot->level }}" class="inline w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
        </label>
        <select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        name="skills[]">
            @foreach ($skills as $new)
            <option {{ ($already->id == $new->id) ? 'selected' : ''}} value="{{ $new->id }}">{{ $new->name }}</option>
            @endforeach
        </select>
        <button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_skill(this);"><i class="fa-solid fa-circle-minus"></i></button>
        @endforeach
        </div>
        <button id="nassign" type="button" class="py-2 px-6 rounded text-blue-500 hover:text-blue-600"
            onclick="add_skill()"><i class="fa-solid fa-circle-plus"></i>
        </button>
    </p>
    <p class="py-4 px-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            修改
        </button>
    </p>
</form>
<script nonce="selfhost">
    function remove_skill(elem) {
    const parent = elem.parentNode;
    const brother = elem.previousElementSibling;
    const big = brother.previousElementSibling;
    parent.removeChild(big);
    parent.removeChild(brother);
    parent.removeChild(elem);
}

function add_skill() {
    var target = document.getElementById('nassign');
    var my_cls = '<label>等級<input type="number" min="1" max="30" step="1" id="level" name="level[]" value="" class="inline w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200" required></label>';
    my_cls += '<select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200" name="skills[]">';
	@foreach ($skills as $new)
	my_cls += '<option value="{{ $new->id }}">{{ $new->name }}</option>';
	@endforeach
	my_cls += '</select>';
    const elemc = document.createElement('select');
    target.parentNode.insertBefore(elemc, target);
    elemc.outerHTML = my_cls;
	my_btn = '<button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_skill(this);"><i class="fa-solid fa-circle-minus"></i></button>';
    const elemb = document.createElement('button');
    target.parentNode.insertBefore(elemb, target);
    elemb.outerHTML = my_btn;
}
</script>
@endsection
