@extends('layouts.game')

@section('content')
<p><div class="pb-3">
    @locked($room->id)
    <p class="w-full text-center">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" onclick="positive();">
            <i class="fa-solid fa-plus"></i>獎勵
        </button>
        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full" onclick="negative();">
            <i class="fa-solid fa-minus"></i>懲罰
        </button>
    </p>
    <input type="checkbox" id="all" onchange="select_all();" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
    @endlocked
    <label class="inline text-3xl">{{ $room->name }}</label>
</div></p>
@foreach ($parties as $p)
<p><div class="pb-3">
    @locked($room->id)
    <input type="checkbox" id="group{{ $p->id }}" onchange="select_party({{ $p->id }});" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
    @endlocked
    <label class="inline text-2xl">{{ $p->name }}</label>
    <br><span class="text-sm">{{ $p->description }}</span>
</div></p>
<div class="relative w-full py-4 rounded-md border border-teal-300 mb-6">
    <div class="absolute inset-0 bg-[url('{{ $p->foundation && $p->foundation->avaliable() ? $p->foundation->url : '' }}')] bg-cover bg-center opacity-70 z-0"></div>
    <table class="relative z-10 w-full text-left font-normal">
    <tr class="font-semibold text-lg">
        <th scope="col" class="w-4">
        </th>
        <th scope="col" class="p-2">
            座號
        </th>
        <th scope="col" class="p-2">
            姓名
        </th>
        <th scope="col" class="p-2">
            缺席
        </th>
        <th scope="col" class="p-2">
            職業
        </th>
        <th scope="col" class="p-2">
            XP
        </th>
        <th scope="col" class="p-2">
            等級
        </th>
        <th scope="col" class="p-2">
            HP
        </th>
        <th scope="col" class="p-2">
            MP
        </th>
        <th scope="col" class="p-2">
            GP
        </th>
        <th scope="col" class="p-2">
            編輯
        </th>
    </tr>
    @foreach ($p->members as $s)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td>
            @locked($room->id)
            <input type="checkbox" id="{{ $s->uuid }}" data-group="{{ $p->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
            @endlocked
        </td>
        <td class="p-2">{{ $s->seat }}</td>
        <td class="p-2">{{ $s->name }}</td>
        <td class="p-2">
            @locked($room->id)
            <input type="checkbox" id="absent{{ $s->uuid }}" name="absent" value="yes"{{ ($s->absent) ? ' checked' : '' }} onchange="absent('{{ $s->uuid }}');" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
            @endlocked
        </td>
        <td class="p-2">{{ ($s->profession) ? $s->profession->name : '無'}}</td>
        <td class="p-2">{{ $s->xp }}</td>
        <td class="p-2">{{ $s->level }}</td>
        <td class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
        <td class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
        <td class="p-2">{{ $s->gp }}</td>
        <td class="p-2">
            @locked($room->id)
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" href="{{ route('game.character_edit', ['uuid' => $s->uuid]) }}">
                <i class="fa-solid fa-user-pen"></i>
            </a>
            @endlocked
        </td>
    </tr>
    @endforeach
    </table>
</div>
@endforeach
@if ($partyless->count() > 0)
<p><div class="pb-3">
    @locked($room->id)
    <input type="checkbox" id="nogroup" onchange="select_no();" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
    @endlocked
    <label class="inline text-2xl">未分組</label>
</div></p>
<table class="w-full py-4 text-left font-normal">
    <tr class="font-semibold text-lg">
        <th scope="col" class="w-4">
        </th>
        <th scope="col" class="p-2">
            座號
        </th>
        <th scope="col" class="p-2">
            姓名
        </th>
        <th scope="col" class="p-2">
            缺席
        </th>
        <th scope="col" class="p-2">
            職業
        </th>
        <th scope="col" class="p-2">
            XP
        </th>
        <th scope="col" class="p-2">
            等級
        </th>
        <th scope="col" class="p-2">
            HP
        </th>
        <th scope="col" class="p-2">
            MP
        </th>
        <th scope="col" class="p-2">
            GP
        </th>
        <th scope="col" class="p-2">
            編輯
        </th>
    </tr>
    @foreach ($partyless as $s)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            @locked($room->id)
            <input type="checkbox" id="{{ $s->uuid }}" data-group="no" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
            @endlocked
        </td>
        <td class="p-2">{{ $s->student->seat }}</td>
        <td class="p-2">{{ $s->name }}</td>
        <td class="p-2">
            @locked($room->id)
            <input type="checkbox" id="absent{{ $s->uuid }}" name="absent" value="yes"{{ ($s->absent) ? ' checked' : '' }} onchange="absent('{{ $s->uuid }}');" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
            @endlocked
        </td>
        <td class="p-2">{{ ($s->profession) ? $s->profession->name : '無'}}</td>
        <td class="p-2">{{ $s->xp }}</td>
        <td class="p-2">{{ $s->level }}</td>
        <td class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
        <td class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
        <td class="p-2">{{ $s->gp }}</td>
        <td class="p-2">
            @locked($room->id)
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" href="{{ route('game.character_edit', ['uuid' => $s->uuid]) }}">
                <i class="fa-solid fa-user-pen"></i>
            </a>
            @endlocked
        </td>
    </tr>
    @endforeach
</table>
@endif
<script nonce="selfhost">
    function absent(uuid) {
        var node = document.getElementById(uuid);
        if (document.getElementById('absent' + uuid).checked) {
            var value = 'yes';
            node.removeAttribute('checked');
            node.setAttribute('disabled', true);
        } else {
            var value = 'no';
            node.removeAttribute('disabled');
        }
        window.axios.post('{{ route('game.absent') }}', {
            uuid: uuid,
            absent: value,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).catch( (response) => {
            console.log(response.data);
        });
    }
    function select_all() {
        if (document.getElementById('all').checked) {
            var value = 'yes';
        } else {
            var value = 'no';
        }
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]:not([data-group=""])');
        nodes.forEach( (node) => {
            if (value == 'yes') {
                node.setAttribute('checked', true);
            } else {
                node.removeAttribute('checked');
            }
        });
    }
    function select_party(pid) {
        if (document.getElementById('group' + pid).checked) {
            var value = 'yes';
        } else {
            var value = 'no';
        }
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group="' + pid + '"]');
        nodes.forEach( (node) => {
            if (value == 'yes') {
                node.setAttribute('checked', true);
            } else {
                node.removeAttribute('checked');
            }
        });
    }
    function select_no() {
        if (document.getElementById('nogroup').checked) {
            var value = 'yes';
        } else {
            var value = 'no';
        }
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group="no"]');
        nodes.forEach( (node) => {
            if (value == 'yes') {
                node.setAttribute('checked', true);
            } else {
                node.removeAttribute('checked');
            }
        });
    }
</script>
@endsection
