@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal">
    預約場地或設備
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('venues') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="p-3">
    <label class="inline pr-6">名稱：{{ $venue->name }}</label>
    <label class="inline pr-6">管理員：{{ $venue->manager->realname }}</label>
    <label class="inline pr-6">借用須知：{{ $venue->description }}</label>
</div>
<div class="p-3">
    <label class="inline align-top">我要預約：</label>
    <div class="p-3 inline-block rounded-lg border shadow-2xl">
    <table class="border-collapse text-sm text-left">
        @php
        $dates[0] = $result->start->copy(); 
        $dates[1] = $dates[0]->copy()->addDay();
        $dates[2] = $dates[1]->copy()->addDay();
        $dates[3] = $dates[2]->copy()->addDay();
        $dates[4] = $dates[3]->copy()->addDay();
        @endphp
        <thead>
            <tr class="font-semibold text-lg">
                <th colspan="2" class="w-1/3 text-left">
                    <button onclick="
                        window.location.replace('{{ route('venue.reserve', [ 'id' => $venue->id ]) }}/{{ $date->copy()->subWeek()->format('Y-m-d') }}');
                    "><i class="fa-solid fa-backward-step"></i>前一週</button>
                </th>
                <th colspan="2" class="w-1/3 text-center"><input type="date" value="{{ ($date) ? $date->format('Y-m-d') : today()->format('Y-m-d') }}" min="{{ current_between_date()->mindate }}"  max="{{ current_between_date()->maxdate }}" onchange="
                    window.location.replace('{{ route('venue.reserve', [ 'id' => $venue->id ]) }}/' + this.value );
                "></th>
                <th colspan="2" class="w-1/3 text-right">
                    <button onclick="
                        window.location.replace('{{ route('venue.reserve', [ 'id' => $venue->id ]) }}/{{ $date->copy()->addWeek()->format('Y-m-d') }}');
                    ">下一週<i class="fa-solid fa-forward-step"></i></button>
                </th>
            </tr>
            <tr class="font-semibold text-lg">
                <th class="border-r bg-gray-200">日期</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[0]->format('Y-m-d') }}</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[1]->format('Y-m-d') }}</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[2]->format('Y-m-d') }}</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[3]->format('Y-m-d') }}</th>
                <th class="w-32 border-l bg-gray-200 text-center">{{ $dates[4]->format('Y-m-d') }}</th>
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
            @foreach ($venue->sessions as $key => $se)
            <tr class="h-6">
                <th class="border-t border-r border-slate-300 font-semibold text-lg">{{ $se }}</th>
                @for ($i=0; $i<5; $i++)
                    @if ($result->map[$i][$key] === true)
                <td class="w-32 border-t border-l border-slate-300 bg-green-200">
                    @php
                        $length = 0;
                        for ($j=$key; $j<10; $j++) {
                            if ($result->map[$i][$j] === true) {
                                $length++;
                            } else {
                                break;
                            }
                        }
                    @endphp
                    <button class="w-full py-2 bg-green-200 hover:bg-green-300 focus:ring-4 focus:ring-green-400 text-sm text-center"
                        onclick="booking({{$venue->id}},'{{ $dates[$i]->format('Y-m-d') }}',{{ $i }},{{ $key }},{{ $length }})">
                        我要預約
                    </button>
                </td>
                    @elseif ($result->map[$i][$key] === false)
                <td class="w-32 border-t border-l border-slate-300 bg-pink-200 text-center">暫不出借</td>
                    @elseif ($result->map[$i][$key] == 'Z')
                <td class="w-32 border-t border-l border-slate-300 bg-gray-200 text-center">已經過期</td>
                    @elseif ($result->map[$i][$key] == 'X')
                <td class="w-32 border-t border-l border-slate-300 bg-gray-200 text-center">尚未開放</td>
                    @elseif ($result->map[$i][$key] != '-')
                    @php
                        $reserve = $result->map[$i][$key];
                    @endphp
                <td class="w-32 border-t border-l border-slate-300 bg-blue-200 text-center"{{ ($reserve['length'] > 1) ? ' rowspan='.$reserve['length'] : ''}}>
                    @if ($reserve->subscriber->uuid == Auth::user()->uuid)
                    <button id="{{ $reserve->id }}" class="viewit w-full py-2 bg-blue-200 text-sm text-center test-blue-700" onclick="editReserve(this)">
                        {{ $reserve->teacher_name ?: $reserve->subscriber->realname }}
                    </button>
                    @else
                    <button id="{{ $reserve->id }}" class="viewit w-full py-2 bg-blue-200 text-sm text-center" data-modal-toggle="defaultModal" onclick="showReserve(this)">
                        {{ $reserve->teacher_name ?: $reserve->subscriber->realname }}
                    </button>
                    @endif
                </td>
                    @endif
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @if ($venue->reserved_info)
    <div class="p-3 inline-block">
        <img width="400" src="{{ asset('venue/' . $venue->reserved_info) }}">
    </div>
    @endif
</div>
<div id="defaultModal" data-modal-target="defaultModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
<form class="hidden" id="act" action="{{ route('venue.reserve.add') }}" method="POST">
    @csrf
    <input type="hidden" id="venue" name="id">
    <input type="hidden" id="date" name="date">
    <input type="hidden" id="weekday" name="weekday">
    <input type="hidden" id="session" name="session">
    <input type="hidden" id="max" name="max">
</form>
<form class="hidden" id="edit" action="{{ route('venue.reserve.edit') }}" method="POST">
    @csrf
    <input type="hidden" id="reserve" name="id">
</form>
<script nonce="selfhost">
function booking(id, date, weekday, session, length) {
    var myform = document.getElementById('act');
    var venue = document.getElementById('venue');
    venue.value = id;
    var mydate = document.getElementById('date');
    mydate.value = date;
    var mywk = document.getElementById('weekday');
    mywk.value = weekday;
    var myse = document.getElementById('session');
    myse.value = session;
    var mymax = document.getElementById('max');
    mymax.value = length;
    myform.submit();
}

function showReserve(event) {
    window.axios.post('{{ route('venue.reserve.view') }}', {
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

function editReserve(event) {
    var myform = document.getElementById('edit');
    var reserve = document.getElementById('reserve');
    reserve.value = event.id;
    myform.submit();
}
</script>
@endsection
