@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    學習單一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.worksheet_add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增學習單
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            學習單標題
        </th>
        <th scope="col" class="p-2">
            設計者
        </th>
        <th scope="col" class="p-2">
            科目名稱
        </th>
        <th scope="col" class="p-2">
            學習目標
        </th>
        <th scope="col" class="p-2">
            適用年級
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @forelse ($worksheets as $e)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $e->title }}</td>
        <td class="p-2">{{ $e->teacher_name }}</td>
        <td class="p-2">{{ $e->subject }}</td>
        <td class="p-2">{{ $e->description }}</td>
        <td class="p-2">{{ $e->grade->name }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.worksheet_assign', ['worksheet_id' => $e->id]) }}" title="指派地圖探險">
                <i class="fa-solid fa-map"></i>
            </a>
            @if ($e->uuid == Auth::user()->uuid)
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.worksheet_manage', ['worksheet_id' => $e->id]) }}" title="學習任務管理">
                <i class="fa-solid fa-location-dot"></i>
            </a>
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.worksheet_edit', ['worksheet_id' => $e->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.worksheet_remove', ['worksheet_id' => $e->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @else
            <button class="py-2 pr-6 text-blue-500 hover:text-blue-600" title="複製學習單"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.worksheet_duplicate', ['worksheet_id' => $e->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-clone"></i>
            </button>
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.worksheet_view', ['worksheet_id' => $e->id]) }}" title="學習單瀏覽">
                <i class="fa-solid fa-eye"></i>
            </a>
            @endif
        </td>
    </tr>
    @empty
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2" colspan="7">找不到您設計的學習單！</td>
    </tr>
    @endforelse
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
