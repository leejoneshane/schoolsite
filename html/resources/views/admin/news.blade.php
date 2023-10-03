@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    電子報管理
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('news.add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增電子報
    </a>
</div>
<div class="border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 m-5" role="alert">
    <p>
        電子報系統是由：排程工作（Jobs\SendNewsLetters）、郵件通知（Notifications\NewsLetter）、資料物件模型、電子報樣板、訂閱戶物件，組合而成。<br>
        其中資料物件模型、電子報樣板必須透過物件導向程式設計實作。在新增電子報之前，請先確認滿足以上需求的程式碼已經設計完成！
    </p>
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
        <th scope="col" class="p-2">
            僅供教職員訂閱
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($news as $i)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $i->name }}</td>
        <td class="p-2">{{ $i->model }}</td>
        <td class="p-2">{{ $i->job }}</td>
        <td class="p-2">{{ ($i->inside) ? '是' : '否' }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('subscribers', ['news' => $i->id]) }}" title="訂閱戶">
                <i class="fa-solid fa-user-group"></i>
            </a>
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('news.edit', ['news' => $i->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('news.remove', ['news' => $i->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
    @endforeach
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
