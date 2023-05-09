@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    社團分類
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.addkind') }}">
        <i class="fa-solid fa-circle-plus"></i>新增社團分類
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        不重複報名：指該類別的社團只允許一個學生報名一個社團。<br>
        人工審核：由管理員錄取報名學生，若要讓系統自動錄取學生，請勿勾選。<br>
        暫停報名：開啟此選項將讓所有該類社團全部無法報名。<br>
        報名和截止日期將統一在社團分類設置，報名時間與休息時間是指在報名期間系統每天開啟報名功能的時段。<br>
    </p>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            類別名稱
        </th>
        <th scope="col" class="p-2">
            不重複報名
        </th>
        <th scope="col" class="p-2">
            人工審核
        </th>
        <th scope="col" class="p-2">
            暫停報名
        </th>
        <th scope="col" class="p-2">
            報名日期
        </th>
        <th scope="col" class="p-2">
            截止日期
        </th>
        <th scope="col" class="p-2">
            報名時間
        </th>
        <th scope="col" class="p-2">
            休息時間
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($kinds as $k)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2 {{ $k->style }}">{{ $k->name }}</td>
        <td class="p-2">{{ ($k->single) ? '是' : '否' }}</td>
        <td class="p-2">{{ ($k->manual_auditing) ? '是' : '否' }}</td>
        <td class="p-2">{{ ($k->stop_enroll) ? '是' : '否' }}</td>
        <td class="p-2">{{ $k->enrollDate->format('Y-m-d') }}</td>
        <td class="p-2">{{ $k->expireDate->format('Y-m-d') }}</td>
        <td class="p-2">{{ $k->workTime }}</td>
        <td class="p-2">{{ $k->restTime }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('clubs.editkind', ['kid' => $k->id]) }}">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('clubs.removekind', ['kid' => $k->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            <a class="py-2 pr-6 text-red-300 hover:text-red-600"
                href="{{ route('clubs.upkind', ['kid' => $k->id]) }}">
                <i class="fa-solid fa-angles-up"></i>
            </a>
            <a class="py-2 pr-6 text-red-300 hover:text-red-600"
                href="{{ route('clubs.downkind', ['kid' => $k->id]) }}">
                <i class="fa-solid fa-angles-down"></i>
            </a>
        </td>
    </tr>
    @endforeach
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
