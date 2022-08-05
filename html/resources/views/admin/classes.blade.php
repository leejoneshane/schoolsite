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
        <div class="text-2xl font-bold leading-normal pb-5">年級與班級</div>
        <select id="grades" onchange="
            chosen = this.selectedIndex;
            myp = document.getElementById('class_list');
            myroles = myp.children;
            for (var i = 0; i < myroles.length; i++) {
                if (myroles[i].id == 'grade_' + chosen) {
                    myroles[i].classList.remove('hidden');
                } else {
                    myroles[i].classList.add('hidden');
                }
            }
        ">
            <option value="">請選擇</option>
            @foreach ($grades as $g)
            <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
        </select>
        <form id="edit-unit" action="{{ route('units') }}" method="POST">
            @csrf
            <div id="class_list">
            @foreach ($grades as $g)
            <table id="grade_{{ $g->id }}" class="hidden w-full text-sm text-left">
                @foreach ($classes->where('grade_id', $g->id)->all() as $c)
                @php
                    $test = (array) $c->tutor;
                    $tutor = array_shift($test);
                @endphp
                <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                    <td class="p-2">{{ $c->id }}</td>
                    <td class="p-2">
                        <input class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                            type="text" name="name['{{ $c->id }}']" value="{{ $c->name }}">
                        <select class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                            name="tutor['{{ $c->id }}']">
                        @foreach ($teachers as $t)
                            <option {{ ($tutor == $t->uuid) ? 'selected' : ''}} value="{{ $t->uuid }}">{{ $t->role_name }}{{ $t->realname }}</option>
                        @endforeach
                        </select>
                    </td>
                </tr>
                @endforeach
            </table>
            @endforeach
            </div>
            <div class="py-2 px-6 mb-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">修改</button>
            </div>
        </form>
    </div>
</div>
@endsection
