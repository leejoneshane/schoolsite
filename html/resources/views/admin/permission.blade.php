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
    <tr class="odd:bg-white even:bg-gray-100 hover:bg-green-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 dark:hover:bg-green-600"
        onclick="window.location.replace('{{ route('permission.grant', ['id' => $p->id]) }}');">
        <td class="p-2 sm:w-auto w-24">
            {{ $p->group }}
        </td>
        <td class="p-2 sm:w-auto w-36">
            {{ $p->permission }}
        </td>
        <td class="p-2 sm:w-auto w-64">
            {{ $p->description }}
        </td>
        <td class="p-2 sm:w-auto w-32">
            <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('permission.edit', ['id' => $p->id]) }}">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('permission.remove', ['id' => $p->id]) }}">    
                <i class="fa-solid fa-trash"></i>
            </a>
        </td>
    </tr>
    @endforeach
</table>
@endsection
