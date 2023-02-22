@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    明日小作家
    @if ($manager)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('writing.genres') }}">
        <i class="fa-solid fa-bookmark"></i>管理專欄
    </a>
    @endif
    @student
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('writing.add') }}">
        <i class="fa-solid fa-circle-plus"></i>我要投稿
    </a>
    @endstudent
    <label for="sort" class="py-2 pl-6 text-sm">排序：</label>
    <select id="sort" class="inline w-48 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
        onchange="
        var order = this.value;
        window.location.replace('{{ route('writing') }}' + '?genre=' + {{ ($genre) ? $genre->id : '0' }} + '&order=' + order);
    ">
        <option value="updated_at" {{ ($order == 'updated_at') ? 'selected' : '' }}>發表時間</option>
        <option value="author" {{ ($order == 'author') ? 'selected' : '' }}>作者</option>
        <option value="hits" {{ ($order == 'hits') ? 'selected' : '' }}>閱讀次數</option>
    </select>
    <label for="genre" class="py-2 pl-6 text-sm">專欄：</label>
    <select id="genre" class="inline w-48 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
        onchange="
        var genre = this.value;
        window.location.replace('{{ route('writing') }}' + '?genre=' + genre + '&order=' + {{ $order }});
    ">
    @foreach ($genres as $k)
        <option value="{{ $k->id }}" {{ ($genre->id == $k->id) ? 'selected' : '' }}>{{ $k->name }}</option>
    @endforeach
    </select>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        <div class="font-semibold text-2xl">{{ ($genre) ? $genre->name : '' }}</div>
        <div class="text-lg">{{ ($genre) ? $genre->description : '' }}</div>
    </p>
</div>
{{ ($contexts) ? $contexts->links('pagination::tailwind') : '' }}
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            作品標題
        </th>
        <th scope="col" class="p-2">
            作者
        </th>
        <th scope="col" class="p-2">
            最後更新
        </th>
        <th scope="col" class="p-2">
            閱讀次數
        </th>
    </tr>
    @foreach ($contexts as $ctx)
    <tr class="odd:bg-white even:bg-gray-100 hover:bg-green-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 cursor-pointer" onclick="
        window.location.replace('{{ route('writing.view', ['id' => $ctx->id]) }}');
    ">
        <td class="p-2">{{ $ctx->title }}</td>
        <td class="p-2">{{ $ctx->classname . $ctx->realname }}</td>
        <td class="p-2">{{ $ctx->updated_at }}</td>
        <td class="p-2">{{ $ctx->hits }}</td>
    </tr>
    @endforeach
</table>
{{ ($contexts) ? $contexts->links('pagination::tailwind') : '' }}
@endsection
