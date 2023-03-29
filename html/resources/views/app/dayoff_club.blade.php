@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    匯入社團錄取名單
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('dayoff.students', ['id' => $report->id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="flex flex-col justify-center">
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
    <form method="POST" action="{{ route('dayoff.importclub', ['id' => $report->id]) }}">
        @csrf
        <p class="p-6">
            <label for="club" class="block text-2xl font-bold">請選擇要匯入的學生課外社團：</label>
            <select id="club" name="club" class="inline rounded py-2 mr-6 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($clubs as $club)
                <option value="{{ $club->id }}">{{ $club->name }}</option>
            @endforeach
            </select>
        </p>
        <p class="p-6">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                匯入
            </button>
        </p>
    </form>
</div>
@endsection
