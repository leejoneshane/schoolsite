@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯公會
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('game.regroup', [ 'room_id' => session('gameclass') ]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-class" action="{{ route('game.party_edit', [ 'party_id' => $party->id ]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="name" class="text-base">公會名稱：</label>
        <input type="text" id="name" name="name" value="{{ $party->name }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="text-base">公會宗旨（口號）：</label>
        <textarea id="description" class="inline w-128 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            name="description" rows="5" cols="120">{{ $party->description }}</textarea>
    </div></p>
    <td class="p-3">
        <label class="text-base">選擇據點：</label>
        <ul class="grid w-full gap-6 md:grid-cols-5">
            @foreach ($bases as $b)
            <li>
                <input type="radio" id="{{ $b->id }}" name="base" value="{{ $b->id }}" class="hidden peer" />
                <label for="{{ $b->id }}" class="inline-flex items-center justify-between w-auto p-2 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                    <img src="{{ $b->avaliable() ? $b->url() : '' }}" class="w-48 h-48 z-0" />
                    <div class="absolute w-48 h-48 z-10 opacity-50 bg-white" /></div>
                    <div class="absolute w-48 h-48 z-20 text-black">{{ $b->description }}</div>
                </label>
            </li>
            @endforeach
        </ul>
    </td>
    <p class="p-6">
        <div class="text-xl">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
@endsection
