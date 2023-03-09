@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    分組一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seats') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="flex flex-col">
    <div class="flex flex-row justify-center">
        <label class="p-3">{{ $seats->name }}</label>
    </div>
    <div class="flex flex-row justify-center">
        <div class="p-3">
            <table class="border border-2 border-slate-300">
                <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
                    <th scope="col" class="p-2">
                        組別
                    </th>
                    <th scope="col" class="p-2">
                        組員
                    </th>
                </tr>
                @foreach ($groups as $g => $students)
                <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                    <td class="border border-2 border-slate-300">
                        @if ($g != 'none')
                        第 {{ $g }} 組
                        @else
                        未分組
                        @endif
                    </td>
                    <td class="border border-2 border-slate-300">
                    @foreach ($students as $stu)
                        @if ($stu->gender == 1)
                        <label class="text-blue-700">{{ ($stu->seat >= 10) ? $stu->seat : '0'.$stu->seat }}{{ $stu->realname }}</label>　
                        @else
                        <label class="text-red-700">{{ ($stu->seat >= 10) ? $stu->seat : '0'.$stu->seat }}{{ $stu->realname }}</label>　
                        @endif
                    @endforeach
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection