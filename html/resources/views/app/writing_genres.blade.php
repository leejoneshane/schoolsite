@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    明日小作家欄目管理
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('writing') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('writing.addgenre') }}">
        <i class="fa-solid fa-circle-plus"></i>新增專欄
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            編號
        </th>
        <th scope="col" class="p-2">
            專欄名稱
        </th>
        <th scope="col" class="p-2">
            徵稿說明
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($genres as $k)
    <tr class="odd:bg-white even:bg-gray-100 hover:bg-green-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $k->id }}</td>
        <td class="p-2">{{ $k->name }}</td>
        <td class="p-2">{{ $k->description }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('writing.editgenre', ['genre' => $k->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('writing.removegenre', ['genre' => $k->id]) }}';
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
