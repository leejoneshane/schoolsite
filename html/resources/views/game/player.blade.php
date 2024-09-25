@extends('layouts.game')

@section('content')
<div class="w-full min-h-screen flex flex-row justify-center">
    <img src="{{ $character->image->url() }}" id="big" class="absolute w-auto h-screen bottom-0 z-50" />
    <div class="w-1/4 flex flex-col bg-white">
        <table class="text-left font-normal pb-6">
            <tr>
                <td colspan="2" class="bg-gray-500 text-white">基本資料</td>
            </tr>
            <tr>
                <td colspan="2">{{ $character->seat }} {{ $character->name }}</td>
            </tr>
            <tr>
                <td class="w-16">職業</td><td>{{ $character->profession->name }}</td>
            </tr>
            <tr>
                <td class="w-16">等級</td><td>{{ $character->level }}</td>
            </tr>
            <tr>
                <td class="w-16">HP</td>
                <td>
                    <div class="w-full h-4 bg-gray-200 rounded-full dark:bg-gray-700 text-right leading-none text-xs font-medium">
                        <div id="hp" class="h-4 bg-green-600 text-xs font-medium text-green-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($character->hp / $character->max_hp * 100) }}%">{{ $character->hp }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-16">MP</td>
                <td>
                    <div class="w-full h-4 bg-gray-200 rounded-full dark:bg-gray-700 text-right leading-none text-xs font-medium">
                        <div id="hp" class="h-4 bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{ intval($character->mp / $character->max_mp * 100) }}%">{{ $character->mp }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-16">AP</td><td><span class="text-red-500">{{ $character->final_ap }}</span>[<span class="text-blue-500">{{ $character->ap }}</span>]</td>
            </tr>
            <tr>
                <td class="w-16">DP</td><td><span class="text-red-500">{{ $character->final_dp }}</span>[<span class="text-blue-500">{{ $character->dp }}</span>]</td>
            </tr>
            <tr>
                <td class="w-16">SP</td><td><span class="text-red-500">{{ $character->final_sp }}</span>[<span class="text-blue-500">{{ $character->sp }}</span>]</td>
            </tr>
            <tr>
                <td class="w-16">狀態</td><td><span class="text-blue-500">{{ $character->status_desc }}</span></td>
            </tr>
            <tr>
                <td class="w-16">XP</td><td><span class="text-lime-500">{{ $character->xp }}</span></td>
            </tr>
            <tr>
                <td class="w-16">GP</td><td><span class="text-lime-500">{{ $character->gp }}</span></td>
            </tr>
        </table>
        <table class="w-full">
            <thead>
                <tr>
                    <td colspan="5" class="bg-gray-500 text-white">背包</td>
                </tr>    
            </thead>
            <tbody class="bg-game-bag bg-contain bg-no-repeat">
                <tr>
                    <td colspan="5" class="h-8"></td>
                </tr>
                @for ($i=0; $i<4; $i++)
                <tr class="h-24">
                    <td class="w-10"></td>
                    @for ($j=1; $j<4; $j++)
                    <td id="bag{{ $j + $i * 3 }}" class="w-20"></td>
                    @endfor
                    <td class="w-10"></td>
                </tr>
                @endfor
                <tr>
                    <td colspan="5" class="h-10"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="w-1/2 relative">
    </div>
    <div class="w-1/4 bg-white">
        <table class="w-full h-[50rem]text-left font-normal">
        <thead>
            <tr>
                <td colspan="3" class="bg-gray-500 text-white">技能書</td>
            </tr>
        </thead>
        <tbody class=" bg-game-book bg-cover bg-opacity-50">
            <tr>
                <td colspan="3" class="h-8"></td>
            </tr>
            @foreach ($character->profession->skills as $skill)
            <tr class="text-lg p-8">
                <td class="w-8">{{ $skill->pivot->level }}</td><td class="w-32">{{ $skill->name }}</td><td class="w-auto text-xs">{{ $skill->description }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="h-64"></td>
            </tr>
        </tbody>
        </table>
    </div>
</div>
<script nonce="selfhost">

</script>
@endsection
