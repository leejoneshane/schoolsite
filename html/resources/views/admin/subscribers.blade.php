@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    電子報訂閱戶管理
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('news') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('subscriber.add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增電子報
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="font-semibold text-lg">
        <th scope="col" class="p-2">
            名稱
        </th>
        <th scope="col" class="p-2">
            資料物件模型
        </th>
        <th scope="col" class="p-2">
            派報排程
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $news->name }}</td>
        <td class="p-2">{{ $news->model }}</td>
        <td class="p-2">{{ $news->job }}</td>
    </tr>
</table>
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
    @if ($subscribers->isEmpty())
    <tr>
        <td colspan="8" class="text-xl font-bold">目前還沒有人訂閱！</td>
    </tr>
    @endif
    @foreach ($subscribers as $email)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            <span class="text-sm">{{ $email->id }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $email->email }}</span>
        </td>
        <td class="p-2">
            @if ($email->verified)
            <i class="fa-solid fa-check"></i>
            @endif
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $email->subscription->created_at }}</span>
        </td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('subscriber.edit', ['id' => $email->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
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
    @endforeach
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
