@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    瀏覽學生名單
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ $referer }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="flex justify-center">
<table class="py-4 text-left font-normal">
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
</div>
<div class="my-10 flex justify-center">
    <table class="w-auto py-4 text-left font-normal">
        <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
            <th scope="col" class="p-2">
                班級
            </th>
            <th scope="col" class="p-2">
                座號
            </th>
            <th scope="col" class="p-2">
                姓名
            </th>
            @foreach ($fields as $field)
            <th scope="col" class="p-2">
                {{ $field['name'] }}
            </th>
            @endforeach
        </tr>
        @foreach ($students as $stu)
        <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
            <td class="p-2">
                {{ $stu->class_id }}
            </td>
            <td class="p-2">
                {{ $stu->seat }}
            </td>
            <td class="p-2">
                {{ $stu->realname }}
            </td>
            @foreach ($fields as $field)
            <td class="p-2">
                {{ $stu->{$field['id']} }}
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>
</div>
@endsection
