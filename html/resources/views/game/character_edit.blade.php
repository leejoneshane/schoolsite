@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯角色
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ url()->previous() }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.character_edit', [ 'uuid' => $character->uuid ]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label class="text-base">學生：{{ $character->seat }}{{ $character->name }}</label>
    </div></p>
    <p><div class="p-3">
        <label for="party" class="text-base">隸屬公會：</label>
        <select id="party" name="party" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($parties as $p)
            <option value="{{ $p->id }}"{{ ($character->party_id == $p->id) ? ' selected' : '' }}>{{ $p->group_no }} {{ $p->name }}</option>
            @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="title" class="text-base">榮譽稱號：</label>
        <input id="title" class="w-1/3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="text" name="title" value="{{ $character->title }}">
    </div></p>
    <p><div class="p-3">
        <label for="profession" class="text-base">職業：</label>
        <select id="profession" name="profession" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($classes as $c)
            <option value="{{ $c->id }}"{{ ($character->class_id == $c->id) ? ' selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div></p>
    <p class="p-6">
        <div class="text-xl">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
@endsection
