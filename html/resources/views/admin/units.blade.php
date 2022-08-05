@extends('layouts.admin')

@section('content')
<div class="relative m-5 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
    <div class="p-10">
        @if (session('error'))
        <div class="border-red-500 bg-red-100 border-b-2" role="alert">
            {{ session('error') }}
        </div>
        @endif
        @if (session('success'))
        <div class="border-green-500 bg-green-100 border-b-2" role="alert">
            {{ session('success') }}
        </div>
        @endif
        <div class="h-full grid grid-cols-2 grid-flow-col gap-3">
        <div class="col-span-1">
            <div class="text-2xl font-bold leading-normal pb-5">行政單位</div>
            <form id="edit-unit" action="{{ route('units') }}" method="POST">
                @csrf
                <table class="w-full text-sm text-left">
                    @foreach ($units as $u)
                    <tr class="hover:bg-gray-200 dark:hover:bg-gray-700"
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
                        <td class="p-2">{{ $u->id }}</td>
                        <td class="p-2">
                            <input class="w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                                type="text" name="{{ $u->id }}" value="{{ $u->name }}">
                        </td>
                    </tr>
                    @endforeach
                </table>
                <td class="py-4 px-6">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">修改</button>
                </td>
            </form>
        </div>
        <div class="col-span-1">
            <div class="fixed">
            <div class="text-2xl font-bold leading-normal pb-5">職權</div>
            <div id="role_list">
            @foreach ($units as $u)
            <div class="hidden" id="role_{{ $u->id }}">
            <form id="edit-role-{{ $u->id }}" action="{{ route('units') }}" method="POST">
                @csrf
                <table class="w-full text-sm text-left">
                    @foreach ($u->roles as $r)
                    <tr>
                        <td class="py-4 px-6">{{ $r->role_no }}</td>
                        <td class="py-4 px-6">
                            <input class="w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                                type="text" name="{{ $u->id }}_{{ $r->id }}" value="{{ $r->name }}">
                        </td>
                    </tr>
                    @endforeach
                </table>
                <td class="py-4 px-6">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">修改</button>
                </td>
            </form>
            </div>
            @endforeach
            </div>
            </div>
        </div>
    </div>
</div>
@endsection