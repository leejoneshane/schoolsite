@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    填報學生名單
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('rosters') }}">
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
<form id="edit-log" action="{{ route('roster.enroll', ['id' => $roster->id, 'class' => $classroom->id]) }}" method="POST">
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
                    姓名
                </th>
                @foreach ($fields as $field)
                <th scope="col" class="p-2">
                    {{ $field['name'] }}
                </th>
                @endforeach
                <th scope="col" class="p-2">
                    填報
                </th>
            </tr>
            @foreach ($classroom->students as $stu)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td class="p-2">
                    {{ $classroom->id }}
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
                @php
                    $uuid = $stu->uuid;
                    $flag = $students->contains(function ($item) use ($uuid) {
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
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            onclick="return checknum()">
            送出學生名單
        </button>
    </div>
</form>
<script>
    const min = {{ $roster->min }};
    const max = {{ $roster->max }};
    function checknum() {
        var num = 0;
        var stus = document.getElementsByName('students[]');
        for (let i=0; i<stus.length; i++) {
            if (stus[i].checked) num++;
        }
        if (num < min) {
            alert('尚未勾選足夠人數！');
            return false;
        }
        if (num > max) {
            alert('勾選人數超過' + (num - max) + '人！');
            return false;
        }
        return true;
    }
</script>
@endsection
