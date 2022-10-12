@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    匯入舊生
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.enrolls', ['club_id' => $club->id]) }}">
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
            招生人數
        </th>
        <th scope="col" class="p-2">
            報名限制
        </th>
		<th scope="col" class="p-2">
            已報名
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600 {{ $club->style }}">
        <td class="p-2">{{ $club->name }}</td>
        <td class="p-2">{{ $club->teacher }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        <td class="p-2">{{ $club->studytime }}</td>
        <td class="p-2">{{ $club->location }}</td>
        <td class="p-2">{{ $club->total }}</td>
        <td class="p-2">{{ $club->maximum }}</td>
        <td class="p-2">{{ $club->count_enrolls() }}</td>
    </tr>
</table>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mt-5" role="alert">
    <p>
        注意事項：<br>
        　　1. 只有還未報名的舊生，才會匯入！匯入之後不會自動錄取，請務必進行人工審核手動錄取！<br>
        　　2. 所有缺省欄位,例如：聯絡方式、午餐選項、自選上課日、身份註記...等，必須由學生或家長登入系統後，自行使用「修改報名資訊」填寫。<br>
    </p>
</div>
<div class="flex flex-col gap-3 justify-center items-center">
    <div class="bg-white rounded p-10">
    @if (empty($years))
        <label>很抱歉，找不到可以匯入的舊生！</label>
    @else
        <form method="POST" action="{{ route('clubs.importold', ['club_id' => $club->id]) }}">
            @csrf
            <label for="year">請選擇要匯入的學年度：</label>
            <select id="year" class="inline w-24 py-2.5 px-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700">
                @foreach ($years as $y)
                <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <p class="p-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    開始匯入
                </button>
            </p>
        </form>
    @endif
    </div>
</div>
@endsection
