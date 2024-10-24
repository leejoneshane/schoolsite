@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    技能一覽表
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.skill_add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增技能
    </a>
</div>
<div class="text-xl font-bold">選擇職業：
    <select class="form-select w-48 m-0 px-3 py-2 text-xl font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    name="profession" onchange="
        var section = this.value;
        window.location.replace('{{ route('game.skills') }}/class/' + section );
    ">
        <option value="all"></option>
        @foreach ($classes as $c)
        <option {{ ($type == 'class' && $c->id == $id) ? 'selected' : ''}} value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
    </select>
    <label class="inline pl-10 text-xl font-bold">選擇怪物：</label>
    <select class="form-select w-48 m-0 px-3 py-2 text-xl font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    name="monster" onchange="
        var section = this.value;
        window.location.replace('{{ route('game.skills') }}/monster/' + section );
    ">
        <option value="all"></option>
        @foreach ($monsters as $m)
        <option {{ ($type == 'monster' && $m->id == $id) ? 'selected' : ''}} value="{{ $m->id }}">{{ $m->name }}</option>
        @endforeach
    </select>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            技能名稱
        </th>
        <th scope="col" class="p-2">
            作用對象
        </th>
        <th scope="col" class="p-2">
            命中率
        </th>
        <th scope="col" class="p-2">
            消耗MP
        </th>
        <th scope="col" class="p-2">
            AP
        </th>
        <th scope="col" class="p-2">
            HP效果
        </th>
        <th scope="col" class="p-2">
            MP效果
        </th>
        <th scope="col" class="p-2">
            AP效果
        </th>
        <th scope="col" class="p-2">
            DP效果
        </th>
        <th scope="col" class="p-2">
            SP效果
        </th>
        <th scope="col" class="p-2">
            解除狀態
        </th>
        <th scope="col" class="p-2">
            賦予狀態
        </th>
        <th scope="col" class="p-2">
            持續時間
        </th>
        <th scope="col" class="p-2">
            獲得經驗值
        </th>
        <th scope="col" class="p-2">
            獲得金幣
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($skills as $sk)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $sk->name }}</td>
        @if ($sk->object == 'self')
        <td class="p-2">自己</td>
        @elseif ($sk->object == 'partner')
        <td class="p-2">隊友</td>
        @elseif ($sk->object == 'party')
        <td class="p-2">全隊</td>
        @elseif ($sk->object == 'target')
        <td class="p-2">對手</td>
        @elseif ($sk->object == 'all')
        <td class="p-2">所有對手</td>
        @elseif ($sk->object == 'any')
        <td class="p-2">不限對象</td>
        @endif
        <td class="p-2">{{ $sk->hit_rate }}</td>
        <td class="p-2">{{ $sk->cost_mp }}</td>
        <td class="p-2">{{ $sk->ap }}</td>
        <td class="p-2">{{ $sk->effect_hp }}</td>
        <td class="p-2">{{ $sk->effect_mp }}</td>
        <td class="p-2">{{ $sk->effect_ap }}</td>
        <td class="p-2">{{ $sk->effect_dp }}</td>
        <td class="p-2">{{ $sk->effect_sp }}</td>
        <td class="p-2">{{ $sk->status_str }}</td>
        <td class="p-2">{{ $sk->inspire_str }}</td>
        <td class="p-2">{{ $sk->effect_times }}</td>
        <td class="p-2">{{ $sk->earn_xp }}</td>
        <td class="p-2">{{ $sk->earn_gp }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-500 hover:text-blue-600"
                href="{{ route('game.skill_edit', ['skill_id' => $sk->id]) }}" title="編輯">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('game.skill_remove', ['skill_id' => $sk->id]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
    @endforeach
    <tr class="h-12">
        <td></td>
    </tr>
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
@endsection
