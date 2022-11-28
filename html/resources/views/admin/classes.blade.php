@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">年級與班級</div>
<label for="grades">請選擇年級：</label>
<select id="grades" class="inline w-24 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
    onchange="
    var chosen = this.selectedIndex + 1;
    var myp = document.getElementById('class_list');
    var myroles = myp.children;
    for (var i = 0; i < myroles.length; i++) {
        if (myroles[i].id == 'grade_' + chosen) {
            myroles[i].classList.remove('hidden');
        } else {
            myroles[i].classList.add('hidden');
        }
    }
    ">
    @foreach ($grades as $g)
    <option value="{{ $g->id }}">{{ $g->name }}</option>
    @endforeach
</select>
<form id="edit-class" action="{{ route('classes') }}" method="POST">
    @csrf
    <div id="class_list">
    @foreach ($grades as $g)
    <table id="grade_{{ $g->id }}" class="{{ ($g->id == 1) ?: 'hidden' }} w-full text-sm text-left">
        @foreach ($classes->where('grade_id', $g->id)->all() as $c)
        @php
            $test = (array) $c->tutor;
            $tutor = array_shift($test);
        @endphp
        <tr>
            <td class="p-2">{{ $c->id }}</td>
            <td class="p-2">
                <input class="rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" name="name['{{ $c->id }}']" value="{{ $c->name }}">
                <select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    name="tutor['{{ $c->id }}']">
                @foreach ($teachers as $t)
                    @php
                        $gap = '';
                        for ($i=0;$i<6-mb_strlen($t->role_name);$i++) {
                            $gap .= '　';
                        }
                    @endphp
                    <option {{ ($tutor == $t->uuid) ? 'selected' : ''}} value="{{ $t->uuid }}">{{ $t->role_name }}{{ $gap }}{{ $t->realname }}</option>
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
@endsection
