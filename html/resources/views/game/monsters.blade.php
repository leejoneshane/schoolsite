@extends('layouts.game')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    怪物種族一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.monster_add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增怪物種族
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            怪物種族
        </th>
        <th scope="col" class="p-2">
            等級範圍
        </th>
        <th scope="col" class="p-2">
            爆擊命中率
        </th>
        <th scope="col" class="p-2">
            基礎健康值
        </th>
        <th scope="col" class="p-2">
            基礎攻擊力
        </th>
        <th scope="col" class="p-2">
            基礎防禦力
        </th>
        <th scope="col" class="p-2">
            基礎敏捷力
        </th>
        <th scope="col" class="p-2">
            贏得經驗值
        </th>
        <th scope="col" class="p-2">
            贏得金幣
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($monsters as $m)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $m->name }}</td>
        <td class="p-2">{{ $m->min_level }}～{{ $m->max_level }}</td>
        <td class="p-2">{{ $m->crit_rate }}</td>
        <td class="p-2">{{ $m->hp }}</td>
        <td class="p-2">{{ $m->ap }}</td>
        <td class="p-2">{{ $m->dp }}</td>
        <td class="p-2">{{ $m->sp }}</td>
        <td class="p-2">{{ $m->xp }}</td>
        <td class="p-2">{{ $m->gp }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.monster_edit', ['monster_id' => $m->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.monster_images', ['monster_id' => $m->id]) }}" title="角色圖片">
                <i class="fa-solid fa-images"></i>
            </a>
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.monster_faces', ['monster_id' => $m->id]) }}" title="頭像">
                <i class="fa-solid fa-image-portrait"></i>
            </a>
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.monster_skills', ['monster_id' => $m->id]) }}" title="技能">
                <i class="fa-solid fa-book-skull"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.monster_remove', ['monster_id' => $m->id]) }}';
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
