@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    電子報：<span class="text-blue-700">{{$news->name}}</span> 訂閱戶管理
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('news') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-subscriber" action="{{ route('subscriber.add', ['news' => $news->id]) }}" method="POST">
    @csrf
    <div class="block">
        <label for="email" class="inline p-2">電子郵件：</label>
        <input class="inline w-64 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="email" name="email">
        <div class="inline py-4 px-6">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增訂閱戶
            </button>
        </div>
    </div>
</form> 
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            訂閱戶編號
        </th>
        <th scope="col" class="p-2">
            電子郵件
        </th>
        <th scope="col" class="p-2">
            已驗證
        </th>
        <th scope="col" class="p-2">
            訂閱時間
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @forelse ($subscribers as $email)
    @if ($email->verified)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            <span class="text-sm">{{ $email->id }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $email->email }}</span>
        </td>
        <td class="p-2">
            <i class="fa-solid fa-check"></i>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $email->subscription->created_at }}</span>
        </td>
        <td class="p-2">
            <a class="py-2 pr-6 text-red-300 hover:text-red-600"
                onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('subscriber.remove', ['news' => $news->id, 'id' => $email->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </a>
        </td>
    </tr>
    @else
    <form id="edit-subscriber" action="{{ route('subscriber.edit', ['news' => $news->id, 'id' => $email->id]) }}" method="POST">
        @csrf
        <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
            <td class="p-2">
                <span class="text-sm">{{ $email->id }}</span>
            </td>
            <td class="p-2">
                <input class="inline w-64 text-sm rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="email" name="email" value="{{ $email->email }}">
            </td>
            <td class="p-2"></td>
            <td class="p-2">
                <span class="text-sm">{{ $email->subscription->created_at }}</span>
            </td>
            <td class="p-2">
                <button type="submit" class="pr-6 text-lg text-blue-300 hover:text-blue-600">
                    <i class="fa-solid fa-floppy-disk"></i>
                </button>
                <a class="py-2 pr-6 text-red-300 hover:text-red-600"
                    onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('subscriber.remove', ['news' => $news->id, 'id' => $email->id]) }}';
                    myform.submit();
                ">
                    <i class="fa-solid fa-trash"></i>
                </a>
            </td>
        </tr>
    </form>
    @endif
    @empty
    <tr>
        <td colspan="8" class="text-xl font-bold">目前還沒有人訂閱！</td>
    </tr>
    @endforelse
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
