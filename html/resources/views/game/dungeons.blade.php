@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    地下城一覽表
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            地下城名稱
        </th>
        <th scope="col" class="p-2">
            指派教師
        </th>
        <th scope="col" class="p-2">
            評量名稱
        </th>
        <th scope="col" class="p-2">
            配置怪物
        </th>
        <th scope="col" class="p-2">
            挑戰次數
        </th>
        <th scope="col" class="p-2">
            開啟日期
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
        <td class="p-2">{{ $d->teacher->realname }}</td>
        <td class="p-2">{{ $d->evaluate->title }}</td>
        <td class="p-2">{{ $d->monster->name }}</td>
        <td class="p-2">{{ $d->times > 0 ?: '無限制' }}</td>
        <td class="p-2">{{ $d->opened_at->format('Y-m-d') }}</td>
        <td class="p-2">{{ $d->closed_at->format('Y-m-d') }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.answers', ['dungeon_id' => $d->id]) }}" title="評量成績">
                <i class="fa-solid fa-medal"></i>
            </a>
        </td>
    </tr>
    @empty
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2" colspan="8">找不到已指派的地下城，請從「教室規則」選單建立評量並指派給班級！</td>
    </tr>
    @endforelse
</table>
@endsection
