@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    權限管理
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('permission.add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增權限
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        請點擊權限所在的列，就可以進行授權！
    </p>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="font-semibold text-lg">
        <th scope="col" class="p-2">
            APP 代碼
        </th>
        <th scope="col" class="p-2">
            權限代碼
        </th>
        <th scope="col" class="p-2">
            權限描述
        </th>
        <th></th>
    </tr>
    @foreach ($permission as $p)
    <tr class="cursor-pointer odd:bg-white even:bg-gray-100 hover:bg-green-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 dark:hover:bg-green-600">
        <td class="p-2 sm:w-auto w-24" onclick="window.location.replace('{{ route('permission.grant', ['id' => $p->id]) }}');">
            {{ $p->group }}
        </td>
        <td class="p-2 sm:w-auto w-36" onclick="window.location.replace('{{ route('permission.grant', ['id' => $p->id]) }}');">
            {{ $p->permission }}
        </td>
        <td class="p-2 sm:w-auto w-64" onclick="window.location.replace('{{ route('permission.grant', ['id' => $p->id]) }}');">
            {{ $p->description }}
        </td>
        <td class="p-2 sm:w-auto w-32">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" href="{{ route('permission.edit', ['id' => $p->id]) }}">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('permission.remove', ['id' => $p->id]) }}';
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
