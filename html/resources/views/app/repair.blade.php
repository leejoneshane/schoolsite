@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    修繕登記
    @admin
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('repair.addkind') }}">
        <i class="fa-solid fa-circle-plus"></i>新增修繕項目
    </a>
    @endadmin
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mt-5" role="alert">
    <p>
        親愛的老師：<br>
        　　請根據您要報修的品項，點選正確的分類：<br>
    </p>
</div>
<p></p>
<div class="pt-10">
    <table class="w-full border-collapse">
        @foreach ($kinds as $kind)
            <tr class="border p-3 bg-green-200">
                <th class="font-semibold text-left">
                    <a href="{{ route('repair.list') }}/{{ $kind->id}}" class="text-lg text-blue-700 dark:text-blue-500 hover:text-blue-600 hover:dark:text-blue-400 underline">
                        {{ $kind->name }}
                    </a>
                    @admin
                    <a class="py-2 px-6 text-blue-300 hover:text-blue-600"
                        href="{{ route('repair.editkind', ['kind' => $kind->id]) }}" title="編輯">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <button class="py-2 text-red-300 hover:text-red-600" title="刪除"
                        onclick="
                            const myform = document.getElementById('remove');
                            myform.action = '{{ route('repair.removekind', ['kind' => $kind->id]) }}';
                            myform.submit();
                    ">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    @endadmin
                </th>
            </tr>
            <tr class="border p-3 bg-green-100">
                <td class="text-left">
                    {!! $kind->description !!}<br>
                    負責人員：
                    @foreach ($kind->managers as $teacher)
                    {{ $teacher->role_name . $teacher->realname }}
                    @endforeach
                    <br>
                    如您的報修紀錄尚無人處理，請逕行聯絡負責人員。
                </td>
            </tr>
        @endforeach
    </table>
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</div>
@endsection
