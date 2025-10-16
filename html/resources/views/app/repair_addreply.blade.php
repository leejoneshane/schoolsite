@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    管理修繕進度
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('repair.list', ['kind' => $job->kind->id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="row" class="px-2 w-64">
            申請日期
        </th>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ substr($job->created_at, 0, 10) }}</td>
    </tr>
    <tr>
        <th scope="row" class="px-2">
            問題主旨
        </th>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ $job->summary }}</td>
    </tr>
    <tr>
        <th scope="row" class="px-2">
            維修地點
        </th>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ $job->place }}</td>
    </tr>
    <tr>
        <th scope="row" class="px-2">
            報修者
        </th>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ $job->reporter_name ?: $job->reporter->realname }}</td>
    </tr>
</table>
@if ($job->reply)
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="px-2">
            處理日期
        </th>
        <th scope="col" class="px-2">
            修繕進度
        </th>
        <th scope="col" class="px-2">
            修繕結果
        </th>
        <th scope="col" class="px-2">
            處理人員
        </th>
        <th scope="col" class="px-2">
        </th>
    </tr>
    @foreach ($job->replys as $reply)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="px-2">
            {{ substr($reply->created_at, 0, 10) }}
        </td>
        <td class="px-2">
            {{ $reply->status }}
        </td>
        <td class="px-2">
            {{ $reply->comment }}
        </td>
        <td class="px-2">
            {{ $reply->manager_name ?: $reply->maintener->realname }}
        </td>
        <td class="border-b px-2">
            @if (Auth::user()->is_admin ||  $job->kind->is_manager(Auth::user()->uuid))
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('repair.removereply', ['reply' => $reply->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endif
<form id="add-reply" action="{{ route('repair.reply', ['job' => $job->id]) }}" method="POST">
    @csrf
    <p class="p-3">
        <label class="inline">管理者：{{ employee()->realname }}</label>
    </p>
    <p class="p-3">
        <label for="status" class="inline">修繕進度：</label>
        <input type="radio" name="status" value="檢測中" checked>檢測中　
        <input type="radio" name="status" value="修繕中">修繕中　
        <input type="radio" name="status" value="完修">完修　
        <input type="radio" name="status" value="委外處理">委外處理　
        <input type="radio" name="status" value="報廢、採購新品">報廢、採購新品
    </p>
    <p class="p-3">
        <label for="comment" class="inline">修繕結果：</label>
        <textarea id="comment" name="comment" rows="4" class="inline p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        ></textarea>
    </p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>
    </p>
</form>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
