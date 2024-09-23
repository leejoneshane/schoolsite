@extends('layouts.game')

@section('content')
<div class="inline text-3xl">{{ $room->name }}</div>
<p><div class="pb-3">
    <div class="inline text-xl drop-shadow-md">公會一覽</div>
    <table class="w-full h-full z-20 text-left font-normal pb-6">
        <tr>
            <th scope="col" class="p-2">
                公會名稱
            </th>
            <th scope="col" class="p-2">
                公會口號
            </th>
            <th scope="col" class="p-2">
                公會長
            </th>
            <th scope="col" class="p-2">
                據點
            </th>
            <th scope="col" class="p-2">
                家具
            </th>
            <th scope="col" class="p-2">
                人數
            </th>
            <th scope="col" class="p-2">
                金庫
            </th>
            <th scope="col" class="p-2">
                HP回復
            </th>
            <th scope="col" class="p-2">
                MP回復
            </th>
            <th scope="col" class="p-2">
                AP增益
            </th>
            <th scope="col" class="p-2">
                DP增益
            </th>
            <th scope="col" class="p-2">
                SP增益
            </th>
        </tr>
        @foreach ($parties as $p)
        <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
            <td class="p-2">{{ $p->name }}</td>
            <td class="p-2">{{ $p->description }}</td>
            <td class="p-2">{{ $p->leader ? $p->leader->name : '尚未設定' }}</td>
            @if ($p->base_id && $p->fundation)
            <td class="p-2">{{ $p->fundation->name }}</td>
            @else
            <td class="p-2">
                <a class="text-blue-500 hover:text-blue-600" href="{{ route('game.party_edit', ['party_id' => $p->id]) }}">設定據點</a>
            </td>
            @endif
            <td class="p-2">{{ $p->furnitures ? $p->furnitures->count() : '0' }}</td>
            <td class="p-2">{{ $p->withAbsent ? $p->withAbsent->count() : '0'}}</td>
            <td class="p-2">{{ $p->treasury }}</td>
            <td class="p-2">{{ $p->effect_hp }}</td>
            <td class="p-2">{{ $p->effect_mp }}</td>
            <td class="p-2">{{ $p->effect_ap }}</td>
            <td class="p-2">{{ $p->effect_dp }}</td>
            <td class="p-2">{{ $p->effect_sp }}</td>
        </tr>
        @endforeach
    </table>
</div></p>
<p><div class="pb-3">
<div class="inline text-xl">角色一覽</div>
    <table class="w-full h-full z-20 text-left font-normal">
        <tr>
            <th scope="col" class="p-2">
                座號
            </th>
            <th scope="col" class="p-2">
                姓名
            </th>
            <th scope="col" class="p-2">
                組別
            </th>
            <th scope="col" class="p-2">
                職業
            </th>
            <th scope="col" class="p-2">
                Level
            </th>
            <th scope="col" class="p-2">
                HP
            </th>
            <th scope="col" class="p-2">
                MP
            </th>
            <th scope="col" class="p-2">
                AP
            </th>
            <th scope="col" class="p-2">
                DP
            </th>
            <th scope="col" class="p-2">
                SP
            </th>
            <th scope="col" class="p-2">
                XP
            </th>
            <th scope="col" class="p-2">
                GP
            </th>
            <th scope="col" class="p-2">
                效果
            </th>
            <th scope="col" class="p-2">
                增益
            </th>
            <th scope="col" class="p-2">
                結束
            </th>
            <th scope="col" class="p-2">
                狀態
            </th>
        </tr>    
        @foreach ($characters as $s)
        <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
            <td class="p-2">{{ $s->seat }}</td>
            <td class="p-2">{{ $s->name }}</td>
            <td class="p-2">{{ $s->party ? $s->party->group_no : '無' }}</td>
            <td class="p-2">
                @if ($s->profession)  
                <a href="{{ route('game.profession_setup', [ 'uuid' => $s->uuid ]) }}" class="text-blue-500 hover:text-blue-600">{{ $s->profession->name }}</a>
                    @if (!$s->image)
                    <a href="{{ route('game.image_setup', [ 'uuid' => $s->uuid ]) }}" class="text-blue-500 hover:text-blue-600">設定形象</a>
                    @endif
                @else
                <a href="{{ route('game.profession_setup', [ 'uuid' => $s->uuid ]) }}" class="text-blue-500 hover:text-blue-600">設定職業形象</a>
                @endif
            </td>
            <td class="p-2">{{ $s->level }}</td>
            <td class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
            <td class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
            <td class="p-2">{{ $s->ap }}</td>
            <td class="p-2">{{ $s->dp }}</td>
            <td class="p-2">{{ $s->sp }}</td>
            <td class="p-2">{{ $s->xp }}</td>
            <td class="p-2">{{ $s->gp }}</td>
            <td class="p-2">{{ $s->temp_effect }}</td>
            <td class="p-2">{{ $s->effect_value }}</td>
            <td class="p-2">{{ $s->effect_timeout ? date('m/d/Y H:i:s', $s->effect_timeout) : ''}}</td>
            <td class="p-2">{{ $s->status == DEAD ? '死亡' : '' }}{{ $s->status == COMA ? '昏迷' : '' }}{{ $s->status == NORMAL ? '正常' : '' }}</td>
        </tr>
        @endforeach
    </table>
</div>
</div></p>
@endsection
