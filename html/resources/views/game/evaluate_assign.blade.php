@extends('layouts.game')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    指派地下城
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.evaluates') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.dungeon_add', [ 'evaluate_id' => $evaluate->id ]) }}">
        <i class="fa-solid fa-circle-plus"></i>新增地下城
    </a>
</div>
<table class="w-full px-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            試卷名稱
        </th>
        <th scope="col" class="p-2">
            科目名稱
        </th>
        <th scope="col" class="p-2">
            出題範圍
        </th>
        <th scope="col" class="p-2">
            適用年級
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $evaluate->title }}</td>
        <td class="p-2">{{ $evaluate->subject }}</td>
        <td class="p-2">{{ $evaluate->range }}</td>
        <td class="p-2">{{ $evaluate->grade->name }}</td>
    </tr>
</table>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            地下城名稱
        </th>
        <th scope="col" class="p-2">
            指派班級
        </th>
        <th scope="col" class="p-2">
            配置怪物
        </th>
        <th scope="col" class="p-2">
            挑戰次數
        </th>
        <th scope="col" class="p-2">
            開放日期
        </th>
        <th scope="col" class="p-2">
            關閉日期
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @forelse ($dungeons as $d)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $d->title }}</td>
        <td class="p-2">{{ $d->classroom->name }}</td>
        <td class="p-2">{{ $d->monster->name }}</td>
        <td class="p-2">{{ $d->times == 0 ? '無限制' : $d->times }}</td>
        <td class="p-2">{{ $d->opened_at->format('Y-m-d') }}</td>
        <td class="p-2">{{ $d->closed_at->format('Y-m-d') }}</td>
        <td class="p-2">
            @if ($d->uuid == $teacher->uuid)
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.dungeon_edit', ['dungeon_id' => $d->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.dungeon_remove', ['dungeon_id' => $d->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @endif
        </td>
    </tr>
    @empty
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2" colspan="7">找不到您指派的地下城！</td>
    </tr>
    @endforelse
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
