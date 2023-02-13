@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    學生名單填報系統
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('roster.add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增表單
    </a>
</div>
<label for="section">請選擇學期：</label>
<select id="section" class="inline w-32 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
    onchange="
    var section = this.value;
    window.location.replace('{{ route('rosters') }}' + '/' + section);
    ">
    @foreach ($sections as $s)
    <option value="{{ $s }}"{{ ($s == $section) ? ' selected' : '' }}>{{ substr($s, 0, -1) . '學年第' . substr($s, -1) . '學期' }}</option>
    @endforeach
</select>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            表單名稱
        </th>
        <th scope="col" class="p-2">
            已填班級數
        </th>
        <th scope="col" class="p-2">
            目前人數
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @forelse ($rosters as $roster)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            <span class="text-sm">{{ $roster->name }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $roster->count_classes($section) }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $roster->count($section) }}</span>
        </td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('roster.summary', ['id' => $roster->id, 'section' => $section]) }}">
                <i class="fa-solid fa-clipboard"></i>填報情形總覽
            </a>
        @if ($manager)
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('roster.edit', ['id' => $roster->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('roster.remove', ['id' => $roster->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @if ($section == current_section())
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="名單歸零"
            onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('roster.reset', ['id' => $roster->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-recycle"></i>
            </button>
            @endif
            <label for="classes" class="inline p-2">修改名單：</label>
            <select id="classes" class="inline rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                onchange="
                var cls = this.value;
                window.location.replace('{{ route('roster.enroll', ['id' => $roster->id]) }}' + '/' + cls);
                ">
                <option></option>
                @foreach ($classes as $cls)
                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                @endforeach
            </select>
        @elseif ($roster->opened())
            @if ($teacher->tutor_class)
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" title="名單填報"
                href="{{ route('roster.enroll', ['id' => $roster->id]) }}">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
            @elseif (in_array($teacher->domain->id, $roster->domains))
            <label for="classes" class="inline p-2">修改名單：</label>
            <select id="classes" class="inline rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                onchange="
                var cls = this.value;
                window.location.replace('{{ route('roster.enroll', ['id' => $roster->id]) }}' + '/' + cls);
                ">
                <option></option>
                @foreach ($classes as $cls)
                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                @endforeach
            </select>
            @endif
        @endif
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" title="名單瀏覽"
                href="{{ route('roster.show', ['id' => $roster->id, 'section' => $section]) }}">
                <i class="fa-solid fa-eye"></i>
            </a>
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" title="名單下載"
                href="{{ route('roster.download', ['id' => $roster->id, 'section => $section']) }}">
                <i class="fa-solid fa-file-arrow-down"></i>
            </a>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="4" class="text-xl font-bold">目前還沒有表單需要填報！</td>
    </tr>
    @endforelse
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
