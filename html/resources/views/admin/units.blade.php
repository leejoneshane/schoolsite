@extends('layouts.admin')

@section('content')
<div class="h-full grid grid-cols-2 grid-flow-col gap-3">
    <div class="col-span-1">
        <div class="text-2xl font-bold leading-normal pb-5">
            行政單位
            <a class="text-sm py-2 px-6 rounded text-blue-300 btn bg-white hover:text-blue-600" href="{{ route('units.add') }}">
                <i class="fa-solid fa-circle-plus"></i>新增
            </a>
        </div>
        <form id="edit-unit" action="{{ route('units') }}" method="POST">
        @csrf
            <table class="text-sm text-left">
                @foreach ($units as $u)
                <tr class="hover:bg-gray-200 dark:hover:bg-gray-600"
                    onmouseover="
                        myp = document.getElementById('role_list');
                        myroles = myp.children;
                        for (var i = 0; i < myroles.length; i++) {
                            if (myroles[i].id == 'role_{{ $u->id }}') {
                                myroles[i].classList.remove('hidden');
                            } else {
                                myroles[i].classList.add('hidden');
                            }
                        }
                    ">
                    <td class="p-2">
                        <input class="w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                            type="text" name="uid[{{ $u->id }}]" value="{{ $u->unit_no }}">
                    </td>
                    <td class="p-2">
                        <input class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                            type="text" name="units[{{ $u->id }}]" value="{{ $u->name }}">
                    </td>
                </tr>
                @endforeach
            </table>
            <td class="py-4 px-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    修改
                </button>
            </td>
        </form>
    </div>
    <div class="col-span-1">
        <div class="fixed">
            <div class="text-2xl font-bold leading-normal pb-5">
                職稱
                <a class="text-sm py-2 px-6 rounded text-blue-300 btn bg-white hover:text-blue-600" href="{{ route('roles.add') }}">
                    <i class="fa-solid fa-circle-plus"></i>新增
                </a>
            </div>
            <div id="role_list">
            @foreach ($units as $u)
                <div class="hidden" id="role_{{ $u->id }}">
                    <form id="edit-role-{{ $u->id }}" action="{{ route('units') }}" method="POST">
                    @csrf
                        <table class="w-full text-sm text-left">
                        @foreach ($u->roles as $r)
                            <tr>
                                <td class="py-2 px-6">
                                    <input class="w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                                        type="text" name="rid[{{ $r->id }}]" value="{{ $r->role_no }}">
                                </td>
                                <td class="py-2 px-6">
                                    <label for="organize[{{ $r->id }}]" class="inline-flex relative items-center cursor-pointer">
                                    <input type="checkbox" id="organize[{{ $r->id }}]" name="organize[{{ $r->id }}]" value="yes" class="sr-only peer"{{ ($r->organize) ? ' checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        納入職編
                                    </label>
                                </td>
                                <td class="py-2 px-6">
                                    <input class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                                        type="text" name="roles[{{ $r->id }}]" value="{{ $r->name }}">
                                </td>
                            </tr>
                        @endforeach
                        </table>
                        <td class="py-4 px-6">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                修改
                            </button>
                        </td>
                    </form>
                </div>
            @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
