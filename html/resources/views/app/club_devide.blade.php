@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    自動分組
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $club->kind_id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<p><div class="p-3">
    <label for="limit" class="inline">每組人數上限：</label>
    <input type="number" id="limit" name="limit" value="{{ $limit }}" class="inline w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" onchange="redo()" required>
    <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>改變上限將會重新分組，您可以試試看哪一種組合最好！</span>
</div></p>
<p><div class="p-3">
    <label class="inline">{{ ($club->section($section)->self_defined) ? '統計單日最多上課人數' : '錄取總人數' }}：<span class="text-blue-700">{{ $all }}</span>，</label>
    <label class="inline">建議分成 <span id="groups" class="text-blue-700">{{ $devide_num }}</span> 組。</label>
</div></p>
<form id="save" action="{{ route('clubs.devide', [ 'club_id' => $club->id, 'section' => $section ]) }}" method="POST">
@csrf

<p><div class="p-3">
    <table class="w-full py-4 text-left font-normal">
        <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
            <th scope="col" class="p-2">
                班級
            </th>
            <th scope="col" class="p-2">
                錄取人數
            </th>
            @if ($club->section($section)->self_defined)
            <th scope="col" class="p-2">
                週一
            </th>
            <th scope="col" class="p-2">
                週二
            </th>
            <th scope="col" class="p-2">
                週三
            </th>
            <th scope="col" class="p-2">
                週四
            </th>
            <th scope="col" class="p-2">
                週五
            </th>
            @endif
            <th scope="col" class="p-2">
                手動分組
            </th>
        </tr>
        @foreach ($counter as $cls => $sum)
        <tr>
            <td class="p-2">
                {{ $cls }}
            </td>
            <td class="p-2">
                {{ $sum['total'] }}
            </td>
            @if ($club->section($section)->self_defined)
            @for ($i=1; $i<6; $i++)
            <td class="p-2">
                {{ $sum["w$i"] }}
            </td>
            @endfor
            @endif
            <td class="p-2">
                <select name="classes[{{ $cls }}]" id="classes[{{ $cls }}]">
                    @for ($i=1; $i<=$devide_num; $i++)
                    <option value="{{ $i }}"{{ ($i == $sum['group']) ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </td>
        </tr>
        @endforeach
    </table>
</div></p>
<p><div class="p-3">
    <span class="w-full py-4 text-left font-normal">自動分組結果：以實際上課最多人數統計。</span>
    <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>若您手動分組，請務必自行核算人數！</span>
    <table id="summary" class="w-full py-4 text-left font-normal">
        <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
            <th scope="col" class="p-2">
                組別
            </th>
            <th scope="col" class="p-2">
                班級
            </th>
            <th scope="col" class="p-2">
                人數
            </th>
        </tr>
        @foreach ($result as $n => $re)
        <tr>
            <td class="p-2">
                {{ $n + 1 }}
            </td>
            <td class="p-2">
                {{ implode('　', $re['classes']) }}
            </td>
            <td class="p-2">
                {{ $re['sum'] }}
            </td>
        </tr>
        @endforeach
    </table>
</div></p>
<p class="p-6">
    <div class="inline">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            分組完成，請寫入資料庫！
        </button>
    </div>
</p>
</form>
<script>
function redo() {
    var limit = document.getElementById('limit').value;
    window.location.replace("{{ route('clubs.devide', [ 'club_id' => $club->id, 'section' => $section ]) }}?limit=" + limit);
}
</script>
@endsection
