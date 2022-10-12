@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    報名資訊管理
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $club->kind_id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
    @if ($current == $year)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.appendenroll', ['club_id' => $club->id]) }}">
        <i class="fa-solid fa-circle-plus"></i>新增報名資訊
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.fastappend', ['club_id' => $club->id]) }}">
        <i class="fa-solid fa-truck-fast"></i>快速輸入
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.importold', ['club_id' => $club->id]) }}">
        <i class="fa-solid fa-file-import"></i>匯入舊生
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.notify', ['club_id' => $club->id]) }}">
        <i class="fa-regular fa-envelope"></i>寄送錄取通知
    </a>
    <div class="inline text-sm py-2 pl-6 rounded"><i class="fa-solid fa-download"></i>下載
        <a class="text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $club->kind_id]) }}">
            錄取名冊
        </a>、
        <a class="text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $club->kind_id]) }}">
            時間序列表
        </a>、
        <a class="text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $club->kind_id]) }}">
            點名表
        </a>
    </div>
    @endif
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            學生社團全名
        </th>
        <th scope="col" class="p-2">
            招生年級
        </th>
        <th scope="col" class="p-2">
            指導教師
        </th>
        <th scope="col" class="p-2">
            上課時間
        </th>
        <th scope="col" class="p-2">
            授課地點
        </th>
        <th scope="col" class="p-2">
            費用
        </th>
        <th scope="col" class="p-2">
            招生人數
        </th>
        <th scope="col" class="p-2">
            已報名
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 {{ $club->style }}">
        <td class="p-2">{{ $club->name }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        <td class="p-2">{{ $club->teacher }}</td>
        <td class="p-2">{{ $club->studytime }}</td>
        <td class="p-2">{{ $club->location }}</td>
        <td class="p-2">{{ $club->cash }}</td>
        <td class="p-2">{{ $club->total }}</td>
        <td class="p-2">{{ $club->count_enrolls() }}</td>
    </tr>
</table>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mt-5" role="alert">
    <p>
        注意事項：<br>
        　　1. 本社團{{ ($club->kind->manual_auditing) ? '採用人工審核，請透過管理介面進行錄取作業！' : '將會由系統依照報名順序自動錄取！' }}<br>
        　　2. 錄取作業不會自動郵寄通知，待錄取作業完成後，請透過上方連結統一寄發錄取通知。<br>
    </p>
</div>
<label for="years">請選擇學年度：</label>
<select id="years" class="inline w-24 py-2.5 px-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
    onchange="
    var year = this.value;
    window.location.replace('{{ route('clubs.enrolls', ['club_id' => $club->id]) }}' + '/' + year);
    ">
    @foreach ($years as $y)
    <option value="{{ $y }}"{{ ($y == $year) ? ' selected' : '' }}>{{ $y }}</option>
    @endforeach
</select>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            排序
        </th>
        <th scope="col" class="p-2">
            年班座號
        </th>
        <th scope="col" class="p-2">
            學生姓名
        </th>
        @if ($club->self_defined)
        <th scope="col" class="p-2">
            自選上課日
        </th>
        @endif
        <th scope="col" class="p-2">
            聯絡人
        </th>
        <th scope="col" class="p-2">
            聯絡信箱
        </th>
        <th scope="col" class="p-2">
            聯絡電話
        </th>
        <th scope="col" class="p-2">
            報名時間
        </th>
        <th scope="col" class="p-2">
            身份註記
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @if ($enrolls->isEmpty())
    <tr>
        <td colspan="8" class="text-xl font-bold">目前還沒有人報名！</td>
    </tr>
    @endif
    @foreach ($enrolls as $order => $enroll)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            <span class="text-sm">{{ $order + 1 }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $enroll->student->class_id }}{{ ($enroll->student->seat < 10) ? '0'.$enroll->student->seat : $enroll->student->seat }}</span>
        </td>
        @if ($club->self_defined)
        <td class="p-2">
            <span class="text-sm">{{ $enroll->weekday }}</span>
        </td>
        @endif
        <td class="p-2">
            <span class="text-sm">{{ $enroll->student->realname }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $enroll->parent }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $enroll->email }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $enroll->mobile }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $enroll->created_at }}</span>
        </td>
        <td class="p-2">
            <span class="text-sm">{{ $enroll->mark }}</span>
        </td>
        <td class="p-2">
        @if ($current == $year)
        @if ($enroll->accepted)
            <a class="py-2 pr-6 text-fuchsia-300 hover:text-fuchsia-600" href="#" 
                onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('clubs.deny', ['enroll_id' => $enroll->id]) }}';
                myform.submit();
            ">
                除名
            </a>
        @else
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" href="#" 
                onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('clubs.valid', ['enroll_id' => $enroll->id]) }}';
                myform.submit();
            ">
                錄取
            </a>
        @endif
            <a class="py-2 pr-6 text-red-300 hover:text-red-600"
            onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('clubs.delenroll', ['enroll_id' => $enroll->id]) }}';
                myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </a>
        @endif
        </td>
    </tr>
    @endforeach
</table>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
