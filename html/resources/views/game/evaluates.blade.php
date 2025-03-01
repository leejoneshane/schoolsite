@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    評量一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.evaluate_add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增評量
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            試卷名稱
        </th>
        <th scope="col" class="p-2">
            出題教師
        </th>
        <th scope="col" class="p-2">
            科目名稱
        </th>
        <th scope="col" class="p-2">
            評量範圍
        </th>
        <th scope="col" class="p-2">
            適用年級
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @forelse ($evaluates as $e)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $e->title }}</td>
        <td class="p-2">{{ $e->teacher_name }}</td>
        <td class="p-2">{{ $e->subject }}</td>
        <td class="p-2">{{ $e->range }}</td>
        <td class="p-2">{{ $e->grade->name }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.evaluate_assign', ['evaluate_id' => $e->id]) }}" title="指派地下城">
                <i class="fa-solid fa-dungeon"></i>
            </a>
            @if ($e->uuid == Auth::user()->uuid)
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.evaluate_manage', ['evaluate_id' => $e->id]) }}" title="試題管理">
                <i class="fa-solid fa-table-list"></i>
            </a>
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.evaluate_edit', ['evaluate_id' => $e->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.evaluate_remove', ['evaluate_id' => $e->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @else
            <button class="py-2 pr-6 text-blue-500 hover:text-blue-600" title="複製評量"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.evaluate_duplicate', ['evaluate_id' => $e->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-clone"></i>
            </button>
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.evaluate_view', ['evaluate_id' => $e->id]) }}" title="試題瀏覽">
                <i class="fa-solid fa-eye"></i>
            </a>
            @endif
        </td>
    </tr>
    @empty
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2" colspan="5">找不到您出題的任何試卷！</td>
    </tr>
    @endforelse
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
