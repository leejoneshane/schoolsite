@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    公假單
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff.permission') }}">
        <i class="fa-solid fa-unlock-keyhole"></i>管理權限
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff.add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增公假單
    </a>
</div>
{{ $reports->links('pagination::tailwind') }}
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="px-2">
            建立日期
        </th>
        <th scope="col" class="px-2">
            公假事由
        </th>
        <th scope="col" class="px-2">
            公假時間
        </th>
        <th scope="col" class="px-2">
            公假地點
        </th>
        <th scope="col" class="px-2">
            總人數
        </th>
        <th scope="col" class="px-2">
            業務單位
        </th>
        <th scope="col" class="px-2">
        </th>
    </tr>
    @forelse ($reports as $report)
    <tr class="bg-white hover:bg-green-100 dark:bg-gray-700">
        <td class="border-b px-2 cursor-pointer" onclick="window.location.replace('{{ route('dayoff.students', ['id' => $report->id]) }}');">{{ substr($report->created_at, 0, 10) }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="window.location.replace('{{ route('dayoff.students', ['id' => $report->id]) }}');">{{ $report->reason }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="window.location.replace('{{ route('dayoff.students', ['id' => $report->id]) }}');">{{ $report->datetime }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="window.location.replace('{{ route('dayoff.students', ['id' => $report->id]) }}');">{{ $report->location }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="window.location.replace('{{ route('dayoff.students', ['id' => $report->id]) }}');">{{ $report->count_students() }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="window.location.replace('{{ route('dayoff.students', ['id' => $report->id]) }}');">{{ is_null($report->creater) ? '管理員' : $report->creater->unit_name . $report->creater->realname }}</td>
        <td class="border-b px-2">
            @if (Auth::user()->is_admin ||  $report->is_creater(Auth::user()->uuid))
            <a class="py-2 pr-6 text-green-300 hover:text-green-600"
                href="{{ route('dayoff.students', ['id' => $report->id]) }}" title="名單管理">
                <i class="fa-solid fa-user-group"></i>
            </a>
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('dayoff.edit', ['id' => $report->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('dayoff.download', ['id' => $report->id]) }}" title="下載公假單">
                <i class="fa-regular fa-file-word"></i>
            </a>
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('dayoff.print', ['id' => $report->id]) }}" title="列印">
                <i class="fa-solid fa-print"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('dayoff.remove', ['id' => $report->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td>目前尚未新增任何公假單！</td>
    </tr>
    @endforelse
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
{{ $reports->links('pagination::tailwind') }}
@endsection
