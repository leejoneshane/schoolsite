@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    教學領域
    <a class="text-sm py-2 px-6 rounded text-blue-300 btn bg-white hover:text-blue-600" href="{{ route('domains.add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增
    </a>
</div>
<form id="edit-domain" action="{{ route('domains') }}" method="POST">
    @csrf
    <div id="domain_list">
    <table class="w-full text-sm text-left">
        @foreach ($domains as $d)
        <tr>
            <td class="p-2">{{ $d->id }}</td>
            <td class="p-2">
                <input class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" name="domains[{{ $d->id }}]" value="{{ $d->name }}">
            </td>
        </tr>
        @endforeach
    </table>
    </div>
    <div class="py-2 px-6 mb-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">修改</button>
    </div>
</form>
@endsection
