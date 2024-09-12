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
            管理
        </th>
    </tr>
    @foreach ($p->members as $s)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $s->seat }}</td>
        <td class="p-2">{{ $s->name }}</td>
        <td class="p-2">
            <form action="{{ route('game.absent', [ 'uuid' => $s->uuid ]) }}">
                <input type="checkbox" id="absent{{ $s->id }}" name="absent" value="yes" class="peer"{{ ($s->absent) ? ' checked' : '' }} onchange="this.form.submit();">
            </form>
        </td>
        <td class="p-2">{{ ($s->profession) ? $s->profession->name : '無'}}</td>
        <td class="p-2">{{ $s->xp }}</td>
        <td class="p-2">{{ $s->level }}</td>
        <td class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
        <td class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
        <td class="p-2">{{ $s->gp }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" title="編輯"
                href="{{ route('students.edit', ['uuid' => $s->uuid]) }}">
                <i class="fa-solid fa-user-pen"></i>
            </a>
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
            管理
        </th>
    </tr>
    @foreach ($partyless as $s)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $s->student->seat }}</td>
        <td class="p-2">{{ $s->name }}</td>
        <td class="p-2">
            <form action="{{ route('game.absent', [ 'uuid' => $s->uuid ]) }}">
                <input type="checkbox" id="absent{{ $s->id }}" name="absent" value="yes" class="peer"{{ ($s->absent) ? ' checked' : '' }} onchange="this.form.submit();">
            </form>
        </td>
        <td class="p-2">{{ ($s->profession) ? $s->profession->name : '無'}}</td>
        <td class="p-2">{{ $s->xp }}</td>
        <td class="p-2">{{ $s->level }}</td>
        <td class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
        <td class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
        <td class="p-2">{{ $s->gp }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" title="編輯"
                href="{{ route('students.edit', ['uuid' => $s->uuid]) }}">
                <i class="fa-solid fa-user-pen"></i>
            </a>
        </td>
    </tr>
    @endforeach
</table>
@endif
@endsection
