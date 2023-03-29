@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    勾選公假名單
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff.students', ['id' => $report->id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="flex justify-center">
    <table class="py-4 text-left font-normal">
        <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
            <th scope="col" class="p-2">
                公假事由
            </th>
            <th scope="col" class="p-2">
                公假時間
            </th>
            <th scope="col" class="p-2">
                公假地點
            </th>
            <th scope="col" class="p-2">
                備註
            </th>
        </tr>
        <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
            <td class="p-2">{{ $report->reason }}</td>
            <td class="p-2">{{ $report->datetime }}</td>
            <td class="p-2">{{ $report->location }}</td>
            <td class="p-2">{{ $report->memo }}</td>
        </tr>
    </table>
</div>
<form id="edit-log" action="{{ route('dayoff.classadd', ['id' => $report->id, 'class' => $classroom->id]) }}" method="POST">
    @csrf
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
                    學生姓名
                </th>
                <th scope="col" class="p-2">
                    性別
                </th>
                <th scope="col" class="p-2">
                    年齡
                </th>
                <th scope="col" class="p-2">
                    選取
                </th>
            </tr>
            @foreach ($classroom->students as $stu)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td class="p-2">
                    {{ $classroom->name }}
                </td>
                <td class="p-2">
                    {{ $stu->seat }}
                </td>
                <td class="p-2">
                    {{ $stu->realname }}
                </td>
                <td class="p-2">
                    {{ ($stu->gender == 1) ? '男' : '女' }}
                </td>
                <td class="p-2">
                    {{ $stu->age }}
                </td>
                @php
                    $uuid = $stu->uuid;
                    $flag = $report->students->contains(function ($item) use ($uuid) {
                        return $item->uuid == $uuid;
                    });
                @endphp
                <td class="p-2">
                    <label for="stu{{ $stu->id }}" class="inline-flex relative items-center cursor-pointer">
                        <input type="checkbox" id="stu{{ $stu->id }}" name="students[]" value="{{ $stu->uuid }}" class="sr-only peer"{{ $flag ? ' checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="flex justify-center">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            送出學生名單
        </button>
    </div>
</form>
@endsection
