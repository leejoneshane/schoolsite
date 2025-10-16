@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    座位表版型管理
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seats') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seats.addtheme') }}">
        <i class="fa-solid fa-circle-plus"></i>新增版型
    </a>
</div>
<div class="min-h-full w-full flex bg-white dark:bg-gray-700 text-black dark:text-gray-200">
    <div class="w-1/2">
        <table class="w-full py-4 text-left font-normal">
            <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
                <th scope="col" class="p-2">
                    版型名稱
                </th>
                <th scope="col" class="p-2">
                    建立日期
                </th>
                <th scope="col" class="p-2">
                    建立者
                </th>
                <th scope="col" class="p-2">
                    管理
                </th>
            </tr>
            @forelse ($templates as $t)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 hover:bg-green-100 cursor-pointer" onmouseover="show_matrix('{{ $t->id }}')" onmouseout="hide_matrix()">
                <td class="p-2">{{ $t->name }}</td>
                <td class="p-2">{{ $t->created_at }}</td>
                <td class="p-2">{{ $t->creater->realname }}</td>
                <td class="p-2">
                @if ($manager || $t->uuid == Auth::user()->uuid)
                    <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                        href="{{ route('seats.edittheme', ['id' => $t->id]) }}">
                        <i class="fa-solid fa-pen"></i>編輯
                    </a>
                    <button class="py-2 pr-6 text-red-300 hover:text-red-600"
                        onclick="
                            const myform = document.getElementById('remove');
                            myform.action = '{{ route('seats.removetheme', ['id' => $t->id]) }}';
                            myform.submit();
                    ">
                        <i class="fa-solid fa-trash"></i>刪除
                    </button>
                @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-xl font-bold">查無任何座位表版型！</td>
            </tr>
            @endforelse
            <form class="hidden" id="remove" action="" method="POST">
                @csrf
            </form>
        </table>
    </div>
    <div class="w-1/2 pl-3">
        <div id="matrix" ></div>
    </div>
</div>
<script nonce="selfhost">
    function show_matrix(id) {
        var matrix = document.getElementById("matrix");
        window.axios.get('{{ route('seats.viewtheme') }}', {
            params: { id: id },
        }).then(function (response) {
            matrix.innerHTML = response.data.html;
        });
    }
    function hide_matrix() {
        var matrix = document.getElementById("matrix");
        matrix.innerHTML = '';
    }
</script>
@endsection
