@extends('layouts.game')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    家具一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('game.furniture_add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增家具
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            家具名稱
        </th>
        <th scope="col" class="p-2">
            健康值效果
        </th>
        <th scope="col" class="p-2">
            行動力效果
        </th>
        <th scope="col" class="p-2">
            攻擊力效果
        </th>
        <th scope="col" class="p-2">
            防禦力效果
        </th>
        <th scope="col" class="p-2">
            敏捷力效果
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($furnitures as $sk)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $sk->name }}</td>
        <td class="p-2">{{ $sk->hp }}</td>
        <td class="p-2">{{ $sk->mp }}</td>
        <td class="p-2">{{ $sk->ap }}</td>
        <td class="p-2">{{ $sk->dp }}</td>
        <td class="p-2">{{ $sk->sp }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('game.furniture_edit', ['furniture_id' => $sk->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.furniture_remove', ['furniture_id' => $sk->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
    @endforeach
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
