@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    {{ $kind->name }}修繕登記
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('repair') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('repair.report', ['kind' => $kind->id]) }}">
        <i class="fa-solid fa-circle-plus"></i>我要登記報修
    </a>
</div>
{{ $jobs->links('pagination::tailwind') }}
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="px-2">
            申請日期
        </th>
        <th scope="col" class="px-2">
            問題主旨
        </th>
        <th scope="col" class="px-2">
            維修地點
        </th>
        <th scope="col" class="px-2">
            報修者
        </th>
        <th scope="col" class="px-2">
            修繕進度
        </th>
        <th scope="col" class="px-2">
            處理日期
        </th>
        <th scope="col" class="px-2">
            處理人員
        </th>
        <th scope="col" class="px-2">
        </th>
    </tr>
    @foreach ($jobs as $job)
    @if ($job->reporter)
    <tr class="bg-white hover:bg-green-100 dark:bg-gray-700">
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ substr($job->created_at, 0, 10) }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ $job->summary }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ $job->place }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ $job->reporter_name ?: $job->reporter->realname }}</td>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ ($job->reply) ? $job->reply->status : ''}}</td>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ ($job->reply) ? substr($job->reply->created_at, 0, 10) : ''}}</td>
        <td class="border-b px-2 cursor-pointer" onclick="show('{{ $job->id }}');">{{ ($job->reply) ? ($job->reply->manager_name ?: $job->reply->maintener->realname) : ''}}</td>
        <td class="border-b px-2">
            @if (Auth::user()->is_admin ||  $kind->is_manager(Auth::user()->uuid))
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('repair.reply', ['job' => $job->id]) }}" title="管理修繕進度">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </a>
            @endif
            @if (Auth::user()->is_admin ||  $job->reporter->uuid == Auth::user()->uuid)
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('repair.removejob', ['job' => $job->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @endif
        </td>
    </tr>
    <tr id="detail_{{ $job->id }}" class="hidden bg-gray-100 dark:bg-gray-600">
        <td colspan="8" class="border-b px-2">
            【問題描述】<br>
            {{ $job->description }}<br>
            【修繕結果】<br>
            {{ ($job->reply) ? $job->reply->comment : ''}}<br>
        </td>
    </tr>
    @endif
    @endforeach
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
{{ $jobs->links('pagination::tailwind') }}
<script nonce="selfhost">
    function show(job) {
        var tr=document.getElementById('detail_' + job);
        if (tr.classList.contains('hidden')) {
            tr.classList.remove('hidden');
        } else {
            tr.classList.add('hidden');
        }
    }
</script>
@endsection
