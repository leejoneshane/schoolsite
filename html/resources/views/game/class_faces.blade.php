@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    管理臉部特寫
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.classes') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<h1 class="text-xl">職業名稱：
    <select class="form-select w-48 m-0 px-3 py-2 text-xl font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    name="profession" onchange="
        var section = this.value;
        window.location.replace('{{ route('game.class_faces') }}/' + section );
    ">
        @foreach ($classes as $c)
        <option {{ ($c->id == $pro->id) ? 'selected' : ''}} value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
    </select>
</h1>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        請先下載角色圖片，然後使用<a class="px-2 text-blue-500" href="https://fengyuanchen.github.io/cropperjs/">切圖工具</a>剪裁臉部特寫後再上傳，圖片格式為 PNG，圖片解析度固定為 80x80。
    </p>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            編號
        </th>
        <th scope="col" class="p-2">
            去背大圖
        </th>
        <th scope="col" class="p-2">
            臉部特寫
        </th>
    </tr>
    @foreach ($pro->images as $img)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $img->id }}</td>
        <td class="p-2"><img src="{{ $img->url() }}" /></td>
        <td class="p-2">
            @if ($img->thumb_avaliable())
            <img src="{{ $img->thumb_url() }}" />
            @endif
            <form action="{{ route('game.face_upload', ['image_id' => $img->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="face" accept=".png" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                <button type="submit" class="inline py-2 px-6 rounded text-blue-500 hover:text-blue-600">重新上傳</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection
