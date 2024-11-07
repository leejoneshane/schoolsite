@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    指派地圖探險
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.worksheets') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.adventure_add', [ 'worksheet_id' => $worksheet->id ]) }}">
        <i class="fa-solid fa-circle-plus"></i>新增地圖探險
    </a>
</div>
<table class="w-full px-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            學習單標題
        </th>
        <th scope="col" class="p-2">
            設計者
        </th>
        <th scope="col" class="p-2">
            科目名稱
        </th>
        <th scope="col" class="p-2">
            學習目標
        </th>
        <th scope="col" class="p-2">
            適用年級
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $worksheet->title }}</td>
        <td class="p-2">{{ $worksheet->teacher_name }}</td>
        <td class="p-2">{{ $worksheet->subject }}</td>
        <td class="p-2">{{ $worksheet->description }}</td>
        <td class="p-2">{{ $worksheet->grade->name }}</td>
    </tr>
</table>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            指派者
        </th>
        <th scope="col" class="p-2">
            指派班級
        </th>
        <th scope="col" class="p-2">
            是否開放
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @forelse ($adventures as $d)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $d->teacher->realname }}</td>
        <td class="p-2">{{ $d->classroom->name }}</td>
        <td class="p-2">
            <label for="open{{ $d->id}}" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="open{{ $d->id}}" value="{{ $d->id}}" class="sr-only peer" onchange="adventure_switch({{ $d->id }})"{{ $d->open ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-gray-900 dark:text-gray-300">開放</span>
            </label>
        </td>
        <td class="p-2">
            @if ($d->uuid == $teacher->uuid)
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.adventure_remove', ['adventure_id' => $d->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @endif
        </td>
    </tr>
    @empty
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2" colspan="4">找不到您指派的地下城！</td>
    </tr>
    @endforelse
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
        <input type="hidden" id="open" name="open" value="">
    </form>
</table>
<script nonce="selfhost">
    function adventure_switch(myid) {
        var node = document.getElementById('open' + myid);
        if (node.checked) {
            const myform = document.getElementById('remove');
            myform.action = '{{ route('game.adventure_switch') }}/' + myid;
            document.getElementById('open').value = 1;
            myform.submit();
        } else {
            const myform = document.getElementById('remove');
            myform.action = '{{ route('game.adventure_switch') }}/' + myid;
            document.getElementById('open').value = 0;
            myform.submit();
        }
    }
</script>
@endsection
