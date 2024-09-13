@extends('layouts.game')

@section('content')
@foreach ($parties as $p)
<p><div class="p-3">
    <label class="inline text-2xl">{{ $p->name }}</label>
    <br><span class="text-sm">{{ $p->description }}</span>
</div></p>
<div class="w-full py-4 rounded-md border border-teal-300 mb-6">
<table class="w-full text-left font-normal">
    <tr class="font-semibold text-lg">
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
        <td class="p-2">{{ $s->seat }}</td>
        <td class="p-2">{{ $s->name }}</td>
        <td class="p-2">
            @locked($room->id)
            <input type="checkbox" id="absent{{ $s->uuid }}" name="absent" value="yes" class="peer"{{ ($s->absent) ? ' checked' : '' }} onchange="absent('{{ $s->uuid }}');">
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
<p><div class="p-3">
    <label class="inline text-2xl">未分組</label>
</div></p>
<table class="w-full py-4 text-left font-normal">
    <tr class="font-semibold text-lg">
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
        <td class="p-2">{{ $s->student->seat }}</td>
        <td class="p-2">{{ $s->name }}</td>
        <td class="p-2">
            @locked($room->id)
            <input type="checkbox" id="absent{{ $s->uuid }}" name="absent" value="yes" class="peer"{{ ($s->absent) ? ' checked' : '' }} onchange="absent('{{ $s->uuid }}');">
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
        if (document.getElementById('absent_' + uuid).checked) {
            var value = 'yes';
        } else {
            var value = 'no';
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
</script>
@endsection
