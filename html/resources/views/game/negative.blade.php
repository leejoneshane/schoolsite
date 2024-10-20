@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    懲罰條款
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.negative_add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增條款
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            條款內容
        </th>
        <th scope="col" class="p-2">
            健康值懲罰
        </th>
        <th scope="col" class="p-2">
            行動力懲罰
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($rules as $sk)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $sk->description }}</td>
        <td class="p-2">{{ $sk->effect_hp }}</td>
        <td class="p-2">{{ $sk->effect_mp }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.rule_edit', ['rule_id' => $sk->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.rule_remove', ['rule_id' => $sk->id]) }}';
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
