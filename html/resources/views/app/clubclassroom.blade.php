@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    各班錄取名冊
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $kind_id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.exportclass', ['kid' => $kind_id, 'class_id' => $class_id]) }}">
        <i class="fa-solid fa-calendar-plus"></i>匯出成DOCX
    </a>
</div>
<label for="classroom">請選擇班級：</label>
<select id="classroom" class="inline w-full p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
    onchange="
    var class_id = this.value;
    window.location.replace('{{ route('clubs.classroom', ['kid' => $kind_id]) }}' + '/' + class_id);
    ">
    @foreach ($classes as $class)
    <option value="{{ $class->id }}"{{ ($class->id == $class_id) ? ' selected' : '' }}>{{ $class->name }}</option>
    @endforeach
</select>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            座號
        </th>
        <th scope="col" class="p-2">
            姓名
        </th>
        <th scope="col" class="p-2">
            社團全名
        </th>
        <th scope="col" class="p-2">
            上課時間
        </th>
        <th scope="col" class="p-2">
            授課地點
        </th>
    </tr>
    @forelse ($enrolls as $students)
        @php
            $count = $students->count();
        @endphp
        @foreach ($students as $enroll)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        @if ($loop->first)
        <td rowspan="{{ $count }}" class="p-2">{{ $enroll->student->seat }}</td>
        <td rowspan="{{ $count }}" class="p-2">{{ $enroll->student->realname }}</td>
        @endif
        <td class="p-2 {{ $enroll->club->style }}">{{ $enroll->club->name }}</td>
        <td class="p-2">{{ $enroll->studytime }}</td>
        <td class="p-2">{{ $enroll->club->location }}</td>
    </tr>
        @endforeach
    @empty
    <tr>
        <td colspan="8" class="text-xl font-bold">目前查無已錄取的學生！</td>
    </tr>
    @endforelse
</table>
@endsection
