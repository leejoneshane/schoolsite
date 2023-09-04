@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    場地/設備一覽表
@if ($manager)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('venue.add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增場地或設備
    </a>
@endif
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        請從下方選單挑選要預約的場地或設備。<br>
    </p>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            名稱
        </th>
        <th scope="col" class="p-2">
            管理員
        </th>
        <th scope="col" class="p-2 w-1/5">
            場地或設備描述
        </th>
        <th scope="col" class="p-2">
            不出借時段
        </th>
        <!--th scope="col" class="p-2 w-1/5">
            不出借節次
        </th-->
        <th scope="col" class="p-2">
            可預約期限
        </th>
        <th scope="col" class="p-2">
            開放預約
        </th>
    @if (Auth::user()->is_admin || $manager)
        <th scope="col" class="p-2">
            管理
        </th>
    @endif
    </tr>
    @forelse ($venues as $venue)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 hover:bg-green-100">
        <td class="p-2 cursor-pointer" onclick="
        window.location.replace('{{ route('venue.reserve', ['id' => $venue->id]) }}');
        ">{{ $venue->name }}</td>
        <td class="p-2 cursor-pointer" onclick="
        window.location.replace('{{ route('venue.reserve', ['id' => $venue->id]) }}');
        ">{{ $venue->manager->realname }}</td>
        <td class="p-2 cursor-pointer" onclick="
        window.location.replace('{{ route('venue.reserve', ['id' => $venue->id]) }}');
        ">{{ $venue->description }}</td>
        <td class="p-2 cursor-pointer" onclick="
        window.location.replace('{{ route('venue.reserve', ['id' => $venue->id]) }}');
        ">{{ $venue->denytime }}</td>
        <!--td class="p-2 cursor-pointer" onclick="
        window.location.replace('{{ route('venue.reserve', ['id' => $venue->id]) }}');
        ">{{ $venue->denysession }}</td-->
        <td class="p-2 cursor-pointer" onclick="
        window.location.replace('{{ route('venue.reserve', ['id' => $venue->id]) }}');
        ">{{ ($venue->schedule_limit > 0) ? $venue->schedule_limit.'天內' : '未設定' }}</td>
    @if (Auth::user()->is_admin || $manager)
        <td class="p-2">
            <label for="open[{{ $venue->id }}]" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="open[{{ $venue->id }}]" name="open[{{ $venue->id }}]" value="yes" class="sr-only peer"{{ ($venue->open) ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            </label>
        </td>
    @else
        <td class="p-2 cursor-pointer" onclick="
        window.location.replace('{{ route('venue.reserve', ['id' => $venue->id]) }}');
        ">
        @if ($venue->open)
            <i class="fa-solid fa-check"></i>
        @else
            <i class="fa-solid fa-xmark"></i>
        @endif
        </td>
    @endif
    @if (Auth::user()->is_admin || $manager || $venue->manager->uuid == Auth::user()->uuid)
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('venue.edit', ['id' => $venue->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('venue.remove', ['id' => $venue->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    @endif
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-xl font-bold">目前還沒有場地或設備可以預約！</td>
    </tr>
    @endforelse
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
