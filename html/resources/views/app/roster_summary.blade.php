@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    填報情形總覽
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('rosters') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            名稱
        </th>
        <th scope="col" class="p-2">
            填報年級
        </th>
        <th scope="col" class="p-2">
            顯示欄位
        </th>
        <th scope="col" class="p-2">
            填報教師
        </th>
        <th scope="col" class="p-2">
            填報日期
        </th>
        <th scope="col" class="p-2">
            人數限制
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $roster->name }}</td>
        <td class="p-2">{{ $roster->grade }}</td>
        <td class="p-2">{{ $roster->field }}</td>
        <td class="p-2">{{ $roster->domain }}</td>
        <td class="p-2">{{ $roster->started_at->format('Y-m-d') }}～{{ $roster->ended_at->format('Y-m-d') }}</td>
        <td class="p-2">{{ $roster->min }}～{{ $roster->max }}</td>
    </tr>
</table>
<p>
<table class="w-full py-4 text-left font-normal">
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
    @foreach ($classes as $cls)
        <td class="p-2">
            <a href="{{ route('roster.show', ['id' => $roster->id, 'section' => $section, 'class' => $cls->id]) }}" class="text-blue-700 dark:text-blue-300 underline">{{ $cls->name }}</a>
        </td>
        <td class="p-2">
            {{ isset($summary[$cls->id]) ? $summary[$cls->id] : '0' }}
        </td>
    @if ($loop->iteration % 9 == 0)
    </tr>
    <tr>
    @endif
    @endforeach
    </tr>
</table>
</p>
@endsection
