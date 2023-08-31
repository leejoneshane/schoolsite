@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal">
    公開課
@if ($manager)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public.permission') }}">
        <i class="fa-solid fa-unlock-keyhole"></i>管理權限
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public.export', ['section' => $section]) }}">
        <i class="fa-solid fa-file-export"></i>PDF 下載
    </a>
@endif
<button class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" onclick="
    navigator.clipboard.writeText('{{ $calendar->url() }}').then(
        result => alert('行事曆分享連結已經複製到剪貼簿！請在 Google 日曆左側選單選取「新增其它日曆」->「加入日曆網址」，然後貼上連結就完成了！')
    );
    ">
    <i class="fa-solid fa-cloud-arrow-up"></i>取得日曆網址
</button>
</div>
<div class="p-3 font-bold">
    <label for="sections">請選擇學期：</label>
    <select id="sections" class="inline w-48 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
        onchange="
        var section = this.value;
        window.location.replace('{{ route('public') }}' + '/' + section);
        ">
        @foreach ($sections as $s)
        <option value="{{ $s->section }}"{{ ($s->section == $section) ? ' selected' : '' }}>{{ $s->name }}</option>
        @endforeach
    </select>
</div>
<div class="p-3 font-bold">
    <label class="inline align-top">已登錄公開課：</label>
    <table class="border-collapse text-sm text-center">
        <thead>
            <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
                <th scope="col" class="p-2">
                    授課時間
                </th>
                <th scope="col" class="p-2">
                    週節次
                </th>
                @if ($manager)
                <th scope="col" class="p-2">
                    教學領域
                </th>
                @endif
                @if ($manager || $domain_manager)
                <th scope="col" class="p-2">
                    授課教師
                </th>
                @endif
                <th scope="col" class="p-2">
                    單元名稱
                </th>
                <th scope="col" class="p-2">
                    授課班級
                </th>
                <th scope="col" class="p-2">
                    授課地點
                </th>
                <th colspan="2" scope="col" class="p-2">
                    下載文件
                </th>
                <th scope="col" class="p-2">
                    管理
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($publics as $data)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td class="p-2">{{ $data->timeperiod }}</td>
                <td class="p-2">{{ $data->week_session }}</td>
                @if ($manager)
                <td class="p-2">{{ $data->domain->name }}</td>
                @endif
                @if ($manager || $domain_manager)
                <td class="p-2">{{ $data->teacher->realname }}</td>
                @endif
                <td class="p-2">{{ $data->teach_unit }}</td>
                <td class="p-2">{{ $data->classroom->name }}</td>
                <td class="p-2">{{ $data->location }}</td>
                <td class="p-2">
                    @if (!empty($data->eduplan))
                    <a href="{{ asset('public_class/' . $data->eduplan) }}">教案</a>
                    @endif
                </td>
                <td class="p-2">
                    @if (!empty($data->discuss))
                    <a href="{{ asset('public_class/' . $data->discuss) }}">觀課後會談</a>
                    @endif
                </td>
                <td class="p-2">
                    <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                        href="{{ route('public.edit', ['id' => $data->id]) }}" title="編輯">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                        onclick="
                            const myform = document.getElementById('remove');
                            myform.action = '{{ route('public.remove', ['id' => $data->id]) }}';
                            myform.submit();
                    ">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="p-3">
    <label class="inline align-top">我要預約：</label>
    <div class="p-3 inline-block rounded-lg border shadow-2xl">
    <table class="border-collapse text-sm text-left">
        @php
        $dates[1] = $schedule->start->copy(); 
        $dates[2] = $dates[1]->copy()->addDay();
        $dates[3] = $dates[2]->copy()->addDay();
        $dates[4] = $dates[3]->copy()->addDay();
        $dates[5] = $dates[4]->copy()->addDay();
        @endphp
        <thead>
            <tr class="font-semibold text-lg">
                <th colspan="2" class="w-1/3 text-left">
                    <button onclick="
                        window.location.replace('{{ route('public', [ 'section' => $section ]) }}?date={{ $mydate->copy()->subWeek()->format('Y-m-d') }}');
                    "><i class="fa-solid fa-backward-step"></i>前一週</button>
                </th>
                <th colspan="2" class="w-1/3 text-center"><input type="date" value="{{ ($mydate) ? $mydate->format('Y-m-d') : today()->format('Y-m-d') }}" min="{{ current_between_date()->mindate }}"  max="{{ current_between_date()->maxdate }}" onchange="
                    window.location.replace('{{ route('public', [ 'section' => $section ]) }}?date=' + this.value );
                "></th>
                <th colspan="2" class="w-1/3 text-right">
                    <button onclick="
                        window.location.replace('{{ route('public', [ 'section' => $section ]) }}?date={{ $mydate->copy()->addWeek()->format('Y-m-d') }}');
                    ">下一週<i class="fa-solid fa-forward-step"></i></button>
                </th>
            </tr>
            <tr class="font-semibold text-lg">
                <th class="border-r bg-gray-200">日期</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[1]->format('Y-m-d') }}</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[2]->format('Y-m-d') }}</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[3]->format('Y-m-d') }}</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[4]->format('Y-m-d') }}</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[5]->format('Y-m-d') }}</th>
            </tr>
            <tr class="font-semibold text-lg">
                <th class="border-b border-r border-slate-300">星期</th>
                <th class="border-b border-l border-slate-300 text-center">一</th>
                <th class="border-b border-l border-slate-300 text-center">二</th>
                <th class="border-b border-l border-slate-300 text-center">三</th>
                <th class="border-b border-l border-slate-300 text-center">四</th>
                <th class="border-b border-l border-slate-300 text-center">五</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sessions as $key => $se)
            <tr class="h-6">
                <th class="border-t border-r border-slate-300 font-semibold text-lg">{{ $se }}</th>
                @php
                    $sdate = $schedule->start->copy();
                @endphp
                @for ($i=1; $i<6; $i++)
                <td class="w-48 border-t border-l border-slate-300 bg-green-200">
                    @if (count($schedule->map[$i][$key]) > 0)
                        @foreach ($schedule->map[$i][$key] as $data)
                    <button id="{{ $data->id }}" class="viewit w-full py-2 bg-blue-200 text-sm text-center" data-modal-toggle="defaultModal" onclick="showReserve(this)">
                        {{ $data->teacher->realname . $data->teach_class . $data->domain->name }}
                    </button>
                        @endforeach
                    @endif
                    @if ($manager || ($domain_manager && $sdate > $schedule->today))
                    <button class="w-full py-2 bg-green-200 hover:bg-green-300 focus:ring-4 focus:ring-green-400 text-sm text-center"
                        onclick="booking('{{ $dates[$i]->format('Y-m-d') }}',{{ $i }},{{ $key }})">
                        我要預約
                    </button>
                    @endif
                </td>
                @php
                    $sdate->addDay();
                @endphp
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
<div id="defaultModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 id="modalHeader" class="text-xl font-semibold text-gray-900 dark:text-white">
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="defaultModal">
                    <i class="fa-solid fa-xmark"></i>
                    <span class="sr-only">關閉視窗</span>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <p id="modalBody" class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                </p>
            </div>
        </div>
    </div>
</div>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
<form class="hidden" id="act" action="{{ route('public.reserve') }}" method="POST">
    @csrf
    <input type="hidden" id="section" name="section">
    <input type="hidden" id="date" name="date">
    <input type="hidden" id="weekday" name="weekday">
    <input type="hidden" id="session" name="session">
</form>
<script>
function booking(date, weekday, session) {
    var myform = document.getElementById('act');
    var section = document.getElementById('section');
    section.value = '{{ $section }}';
    var mydate = document.getElementById('date');
    mydate.value = date;
    var mywk = document.getElementById('weekday');
    mywk.value = weekday;
    var myse = document.getElementById('session');
    myse.value = session;
    myform.submit();
}

function showReserve(event) {
    window.axios.post('{{ route('public.view') }}', {
        id: event.id,
    }, {
        headers: {
            'Content-Type': 'application/json;charset=utf-8',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(function (response) {
        document.getElementById('modalHeader').innerHTML = response.data.header;
        document.getElementById('modalBody').innerHTML = response.data.body;
    });
}
</script>
@endsection
