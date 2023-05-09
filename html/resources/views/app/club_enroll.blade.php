@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    學生社團報名
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            營隊全名
        </th>
        <th scope="col" class="p-2">
            指導老師
        </th>
        <th scope="col" class="p-2">
            招生年級
        </th>
        <th scope="col" class="p-2">
            上課時段
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
            報名限制
        </th>
        <th scope="col" class="p-2">
            已報名
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @forelse ($clubs as $club)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 {{ $club->style }}">
        <td class="p-2">{{ $club->name }}</td>
        <td class="p-2">{{ $club->section()->teacher }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        <td class="p-2">{{ $club->section()->studytime }}</td>
        <td class="p-2">{{ $club->section()->location }}</td>
        <td class="p-2">{{ $club->section()->cash }}</td>
        <td class="p-2">{{ $club->section()->total }}</td>
        <td class="p-2">{{ $club->section()->maximum }}</td>
        <td class="p-2">{{ $club->count_enrolls() }}</td>
        <td class="p-2">
            @if ($enroll = $student->get_enroll($club->id))
                已報名
            @else
                @if ($club->count_enrolls() <= $club->section()->maximum)
                <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                    href="{{ route('clubs.addenroll', ['club_id' => $club->id]) }}">
                    我要報名
                </a>
                @else
                    名額已滿
                @endif
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="9" class="p-2 text-3xl font-bold">查無可報名社團！</td>
    </tr>
    @endforelse
</table>
<div class="block w-full h-12"></div>
@if ($student->section_enrolls()->isNotEmpty())
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            營隊全名
        </th>
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
            報名順位
        </th>
        <th scope="col" class="p-2">
            錄取（或候補）
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($student->section_enrolls() as $enroll)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2 {{ $enroll->club->style }}">{{ $enroll->club->name }}</td>
        <td class="p-2">{{ $enroll->parent }}</td>
        <td class="p-2">{{ $enroll->email }}</td>
        <td class="p-2">{{ $enroll->mobile }}</td>
        <td class="p-2">{{ $enroll->section_order() + 1 }}</td>
        <td class="p-2">
            @if ($enroll->accepted)
            <i class="fa-solid fa-check"></i>
            @endif
        </td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('clubs.editenroll', ['enroll_id' => $enroll->id]) }}">
                修改報名資訊
            </a>
            @if ($enroll->club->self_remove && empty($enroll->audited_at))
            <button class="py-2 pr-6 text-red-300 hover:text-red-600"
                onclick="
                const myform = document.getElementById('remove');
                myform.action = '{{ route('clubs.delenroll', ['enroll_id' => $enroll->id]) }}';
                myform.submit();
            ">
                取消報名
            </button>
            @endif
        </td>
    </tr>
    @endforeach
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endif
@endsection
