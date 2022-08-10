@extends('layouts.admin')

@section('content')
<div class="relative m-5">
    <div class="p-10">
        @if (session('error'))
        <div class="border border-red-500 bg-red-100 dark:bg-red-700 border-b-2" role="alert">
            {{ session('error') }}
        </div>
        @endif
        @if (session('success'))
        <div class="border border-green-500 bg-green-100 dark:bg-green-700 border-b-2" role="alert">
            {{ session('success') }}
        </div>
        @endif
        <div class="text-2xl font-bold leading-normal pb-5">
            授權給使用者
            <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('permission') }}">
                <i class="fa-solid fa-eject"></i>返回上一頁
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
                    權限代碼
                </th>
                <th scope="col" class="p-2">
                    權限描述
                </th>
                <th></th>
            </tr>
            <tr>
                <td class="p-2 sm:w-auto w-36">
                    {{ $permission->group }}.{{ $permission->permission }}
                </td>
                <td class="p-2 sm:w-auto w-64">
                    {{ $permission->description }}
                </td>
            </tr>
        </table>
        <form id="edit-teacher" action="{{ route('teachers.edit', ['uuid' => $teacher->uuid]) }}" method="POST">
            @csrf
            <p class="p-2"><label for="roles" class="inline">已授權人員：</label>
                @foreach ($already as $u)
                <select class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    name="classes[]">
                    @foreach ($classes as $cls)
                    <option value="{{ $cls->id }}"{{ $assign->class_id == $cls->id  ? ' selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
                <select class="inline rounded w-40 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    name="subjects[]">
                    @foreach ($subjects as $subj)
                    <option value="{{ $subj->id }}"{{ $assign->subject_id == $subj->id  ? ' selected' : '' }}>{{ $subj->name }}</option>
                    @endforeach
                </select>
                <button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_assign(this);"><i class="fa-solid fa-circle-minus"></i></button>
                @endforeach
                <button id="nassign" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
                    onclick="add_assign()"><i class="fa-solid fa-circle-plus"></i>
                </button>
            </p>
            <p class="py-4 px-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    修改
                </button>
            </p>
        </form>
    </div>
</div>
@endsection
