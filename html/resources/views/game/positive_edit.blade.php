@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯獎勵條款
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('game.positive') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.rule_edit', [ 'rule_id' => $rule->id ]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="description" class="text-base">條款內容：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120">{{ $rule->description }}</textarea>
    </div></p>
    <p><div class="p-3">
        <label for="earnxp" class="text-base">經驗值獎勵：</label>
        <input id="earnxp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="xp" min="0" step="10" value="{{ $rule->effect_xp }}">
    </div></p>
    <p><div class="p-3">
        <label for="earngp" class="text-base">金幣獎勵：</label>
        <input id="earngp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
            type="number" name="gp" min="0" step="10" value="{{ $rule->effect_gp }}">
    </div></p>
    <p><div class="p-3">
        <label for="item" class="text-base">道具獎勵：</label>
        <select id="item" name="item" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option value="">無</option>
            @foreach ($items as $item)
            <option value="{{ $item->id }}"{{ $rule->effect_item == $item->id ? ' selected' : '' }}>{{ $item->name }}</option>
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
