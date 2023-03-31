@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    公假學生管理
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <label for="classes" class="inline text-sm py-2 pl-6 rounded text-blue-600">勾選班級名單：</label>
    <select id="classes" class="inline rounded w-32 py-2 mr-6 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        onchange="
        var cls = this.value;
        window.location.replace('{{ route('dayoff.classadd', ['id' => $report->id]) }}' + '/' + cls);
        ">
        <option></option>
        @foreach ($classes as $cls)
        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
        @endforeach
    </select>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff.fastadd', ['id' => $report->id]) }}">
        <i class="fa-solid fa-truck-fast"></i>快速輸入
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff.importclub', ['id' => $report->id]) }}">
        <i class="fa-solid fa-file-import"></i>匯入社團錄取名單
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff.importroster', ['id' => $report->id]) }}">
        <i class="fa-solid fa-file-import"></i>匯入已填報學生名單
    </a>
    <a class="text-sm py-2 pl-6 rounded text-red-300 hover:text-red-600" href="{{ route('dayoff.empty', ['id' => $report->id]) }}">
        <i class="fa-solid fa-recycle"></i>清空學生名單
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
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mt-5" role="alert">
    <p>
        公假學生名單來源有手動輸入、社團學員名單、學生名單填報系統，分別說明如下：<br>
        　　1. 手動輸入可使用班級名單進行勾選，或是使用快速輸入直接從 Excel 表單複製學號欄位並貼上。<br>
        　　2. 匯入社團學員名單只能匯入本學期的錄取名單，匯入後請從下方名單列表刪除不需要請公假的學生。<br>
        　　3. 匯入學生名單填報系統中的名單，該系統允許導師或科任老師自行填報名單。並可以設定填報上下限。<br>
    </p>
</div>
<table class="w-full py-4 text-left font-normal">
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
            管理
        </th>
    </tr>
    @forelse ($report->students as $stu)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            <span class="text-sm">{{ $stu->classroom->name }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $stu->seat }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $stu->realname }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ ($stu->gender == 1) ? '男' : '女' }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $stu->age }}</span>
        </td>
        <td class="p-2">
            <a class="py-2 pr-6 text-red-300 hover:text-red-600"
            onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('dayoff.delstudent', ['id' => $stu->pivot->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </a>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-xl font-bold">目前還沒有學生名單！</td>
    </tr>
    @endforelse
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
