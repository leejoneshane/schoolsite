@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    學生社團報名
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.enroll') }}">
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
        <td class="p-2">{{ $club->section()->teacher }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        <td class="p-2">{{ $club->section()->studytime }}</td>
        <td class="p-2">{{ $club->section()->location }}</td>
        <td class="p-2">{{ $club->section()->total }}</td>
        <td class="p-2">{{ $club->section()->maximum }}</td>
        <td class="p-2">{{ $club->count_enrolls() }}</td>
    </tr>
</table>
<div class="flex flex-col gap-3 justify-center items-center">
    <div class="bg-white rounded p-10">
        <form method="POST" action="{{ route('clubs.addenroll', ['club_id' => $club->id]) }}">
            @csrf
            <div class="p-3">
                <label for="parent" class="inline">聯絡人：</label>
                <input class="inline w-48 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" name="parent">
            </div>
            <div class="p-3">
                <label for="email" class="inline">電子郵件地址：</label>
                <input class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" name="email">
            </div>
            <div class="p-3">
                <label for="mobile" class="inline">行動電話號碼：</label>
                <input class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" name="mobile">
            </div>
            <div class="p-3">
                <label for="identity" class="inline">特殊身份註記：</label>
                <select name="identity" class="inline w-48 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                    <option value="0">一般學生</option>
                    <option value="1">臺北市安心就學補助</option>
                    <option value="2">領有身心障礙手冊</option>
                </select>
            </div>
            @if ($club->has_lunch)
            <div class="p-3">
                <label for="lunch" class="inline">午餐選項：</label>
                <select name="lunch" class="inline w-48 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                    <option value="0">自理</option>
                    <option value="1">葷食</option>
                    <option value="2">素食</option>
                </select>
            </div>
            @endif
            @if ($club->section()->self_defined)
            <div class="p-3">
                <label class="inline">自選上課日：每週</label>
                <div id="weekdays" class="inline">
                    <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="checkbox" name="weekdays[]" value="1"><span class="text-sm">一　</span>
                    <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="checkbox" name="weekdays[]" value="2"><span class="text-sm">二　</span>
                    <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="checkbox" name="weekdays[]" value="3"><span class="text-sm">三　</span>
                    <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="checkbox" name="weekdays[]" value="4"><span class="text-sm">四　</span>
                    <input class="pl-3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="checkbox" name="weekdays[]" value="5"><span class="text-sm">五　</span>
                </div>
            </div>
            @endif
            @if ($club->self_remove)
            <div class="p-3 text-red-500">
                此社團開放學生家長可以自行取消報名！
            </div>
            @else
            <div class="p-3 text-red-500">
                此社團不開放取消報名功能，如要取消報名請以電話聯絡<span class="text-blue-700">{{ $club->unit->name }}</span>承辦人！</label>
            </div>
            @endif
            <p class="p-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    送出報名表
                </button>
            </p>
        </form>
    </div>
</div>
@endsection
