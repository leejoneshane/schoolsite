@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    快速輸入報名資訊
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.enrolls', ['club_id' => $club->id, 'section' => $section]) }}">
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
        <td class="p-2">{{ $club->section($section)->teacher }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        <td class="p-2">{{ $club->section($section)->studytime }}</td>
        <td class="p-2">{{ $club->section($section)->location }}</td>
        <td class="p-2">{{ $club->section($section)->total }}</td>
        <td class="p-2">{{ $club->section($section)->maximum }}</td>
        <td class="p-2">{{ $club->count_enrolls($section) }}</td>
    </tr>
</table>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mt-5" role="alert">
    <p>
        注意事項：<br>
        　　1. 以快速輸入方式報名，將會直接錄取！<br>
        　　2. 所有缺省欄位,例如：聯絡方式、午餐選項、自選上課日、身份註記...等，必須由學生或家長登入系統後，自行使用「修改報名資訊」填寫。<br>
        　　3. 若要輸入多位學生，請用空白或分行隔開。例如：30301 30421。<br>
    </p>
</div>
<div class="w-full flex flex-col gap-3 justify-center items-center">
    <form method="POST" action="{{ route('clubs.fastappend', ['club_id' => $club->id, 'section' => $section]) }}">
        @csrf
        <p class="p-6">
            <label for="stdno" class="block text-2xl font-bold">請輸入報名學生的班級座號：</label>
            <textarea class="inline w-2/3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                name="stdno" rows="5" cols="180"></textarea>
        </p>
        <p class="p-6">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                儲存
            </button>
        </p>
    </form>
</div>
@endsection
