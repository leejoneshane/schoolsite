@extends('layouts.game')

@section('content')
@locked($room->id)
<div class="fixed w-full h-12 text-center z-30">
    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" onclick="openModal(1);">
        <i class="fa-solid fa-plus"></i>獎勵
    </button>
    <button class="ml-6 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full" onclick="openModal(2);">
        <i class="fa-solid fa-minus"></i>懲罰
    </button>
</div>
@endlocked
<div class="relative top-16">
<p><div class="pb-3">
    @locked($room->id)
    <input type="checkbox" id="all" onchange="select_all();" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
    @endlocked
    <div class="inline text-3xl">
        {{ $room->name }}
    </div>
    @locked($room->id)
    <button class="inline bg-teal-200 hover:bg-teal-500 text-normal font-bold py-2 px-4 rounded-full" onclick="auto_absent('{{ $room->id }}');">
        自動點名
    </button>
    @endlocked
</div></p>
@foreach ($parties as $p)
<p><div class="pb-3">
    @locked($room->id)
    <input type="checkbox" id="group{{ $p->id }}" data-party="{{ $p->id }}" onchange="select_party({{ $p->id }});" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
    @endlocked
    <div class="inline text-2xl">{{ $p->name }}</div>
    <br><span class="text-sm">{{ $p->description }}</span>
</div></p>
<div class="relative inline-flex items-center justify-between w-full py-4 rounded-md border border-teal-300 mb-6 bg-cover bg-center z-0" style="background-image: url('{{ $p->foundation && $p->foundation->avaliable() ? $p->foundation->url() : '' }}')">
    <div class="absolute w-full h-full z-10 opacity-30" /></div>
    <table class="w-full h-full z-20 text-left font-normal">
        <tr class="font-semibold text-lg">
            <th scope="col" class="w-4">
            </th>
            <th scope="col" class="p-2">
                座號
            </th>
            <th scope="col" class="w-6">
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
                等級
            </th>
            <th scope="col" class="p-2">
                狀態
            </th>
            @locked($room->id)
            <th scope="col" class="p-2">
                技能
            </th>
            <th scope="col" class="p-2">
                道具
            </th>
            @endlocked
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
                編輯
            </th>
        </tr>
        @foreach ($p->members as $s)
        <tr class="!bg-opacity-80 odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
            <td>
                @locked($room->id)
                <input type="checkbox" id="{{ $s->uuid }}" data-group="{{ $p->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
                @endlocked
            </td>
            <td class="p-2">{{ $s->seat }}</td>
            <td class="py-2 pl-2">{!! $s->title ? '<i class="fa-solid fa-crown" title="'.$s->title.'"></i>' : '' !!}</td>
            <td class="py-2 pr-2">{{ $s->name }}</td>      
            <td class="p-2">
                @locked($room->id)
                <input type="checkbox" id="absent{{ $s->uuid }}" name="absent" value="yes"{{ ($s->absent) ? ' checked' : '' }} onchange="absent('{{ $s->uuid }}');" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
                @endlocked
            </td>
            <td class="p-2">{{ ($s->profession) ? $s->profession->name : '村民'}}</td>
            <td id="level{{ $s->seat }}" class="p-2">{{ $s->level }}</td>
            <td id="status{{ $s->seat }}" class="p-2">{{ $s->status_desc }}</td>
            @locked($room->id)
            <td class="p-2">
                <button class="bg-amber-300 hover:bg-amber-500 text-white font-bold py-2 px-4 rounded-full" onclick="prepare_skill('{{ $s->uuid }}')">
                    <i class="fa-solid fa-book-open"></i>
                </button>
            </td>
            <td class="p-2">
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full" onclick="prepare_item('{{ $s->uuid }}')">
                    <i class="fa-solid fa-sack-xmark"></i>
                </button>
            </td>
            @endlocked
            <td id="hp{{ $s->seat }}" class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
            <td id="mp{{ $s->seat }}" class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
            <td id="ap{{ $s->seat }}" class="p-2">{{ $s->final_ap }}[{{ $s->ap }}]</td>
            <td id="dp{{ $s->seat }}" class="p-2">{{ $s->final_dp }}[{{ $s->dp }}]</td>
            <td id="sp{{ $s->seat }}" class="p-2">{{ $s->final_sp }}[{{ $s->sp }}]</td>
            <td id="xp{{ $s->seat }}" class="p-2">{{ $s->xp }}</td>
            <td id="gp{{ $s->seat }}" class="p-2">{{ $s->gp }}</td>
            <td class="p-2">
                @locked($room->id)
                <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-full" onclick="prepare_character('{{ $s->uuid }}')">
                    <i class="fa-solid fa-user-pen"></i>
                </button>
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
    <div class="inline text-2xl">未分組</div>
</div></p>
<table class="w-full py-4 text-left font-normal">
    <tr class="font-semibold text-lg">
        <th scope="col" class="w-4">
        </th>
        <th scope="col" class="p-2">
            座號
        </th>
        <th scope="col" class="w-6">
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
            狀態
        </th>
        @locked($room->id)
        <th scope="col" class="p-2">
            技能
        </th>
        <th scope="col" class="p-2">
            道具
        </th>
        @endlocked
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
        <td class="py-2 pl-2">{!! $s->title ? '<i class="fa-solid fa-crown" title="'.$s->title.'"></i>' : '' !!}</td>
        <td class="py-2 pr-2">{{ $s->name }}</td>
        <td class="p-2">
            @locked($room->id)
            <input type="checkbox" id="absent{{ $s->uuid }}" name="absent" value="yes"{{ ($s->absent) ? ' checked' : '' }} onchange="absent('{{ $s->uuid }}');" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
            @endlocked
        </td>
        <td class="p-2">{{ ($s->profession) ? $s->profession->name : '村民'}}</td>
        <td id="level{{ $s->seat }}" class="p-2">{{ $s->level }}</td>
        <td id="status{{ $s->seat }}" class="p-2">{{ $s->status_desc }}</td>
        @locked($room->id)
        <td class="p-2">
            <button class="ml-6 bg-amber-300 hover:bg-amber-500 text-white font-bold py-2 px-4 rounded-full" onclick="prepare_skill('{{ $s->uuid }}');">
                <i class="fa-solid fa-book-open"></i>
            </button>
        </td>
        <td class="p-2">
            <button class="ml-6 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full" onclick="prepare_item('{{ $s->uuid }}');">
                <i class="fa-solid fa-sack-xmark"></i>
            </button>
        </td>
        @endlocked
        <td id="hp{{ $s->seat }}" class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
        <td id="mp{{ $s->seat }}" class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
        <td id="ap{{ $s->seat }}" class="p-2">{{ $s->final_ap }}[{{ $s->ap }}]</td>
        <td id="dp{{ $s->seat }}" class="p-2">{{ $s->final_dp }}[{{ $s->dp }}]</td>
        <td id="sp{{ $s->seat }}" class="p-2">{{ $s->final_sp }}[{{ $s->sp }}]</td>
        <td id="xp{{ $s->seat }}" class="p-2">{{ $s->xp }}</td>
        <td id="gp{{ $s->seat }}" class="p-2">{{ $s->gp }}</td>
        <td class="p-2">
            @locked($room->id)
            <button class="ml-6 bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-full" onclick="prepare_character('{{ $s->uuid }}')">
                <i class="fa-solid fa-user-pen"></i>
            </button>
            @endlocked
        </td>
    </tr>
    @endforeach
</table>
@endif
<div class="w-full min-h-16"></div>
</div>
<div id="warnModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[80] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">警告</h3>
            </div>
            <div id="message" class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="warnModal.hide();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    我知道了
                </button>
            </div>
        </div>
    </div>
</div>
<div id="positiveModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">請選擇獎勵條款：</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul>
                    @foreach ($positive_rules as $r)
                    <li>
                    <input type="radio" id="{{ $r->id }}" name="positive" value="{{ $r->id }}" class="hidden peer" />
                    <label for="{{ $r->id }}" class="inline-block w-full p-2 text-gray-500 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-teal-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-teal-600 dark:peer-checked:text-blue-300 peer-checked:text-blue-600 hover:bg-teal-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-teal-700">
                        <span class="inline-block w-96">{{ $r->description }}</span>
                        XP:<input type="number" id="pxp{{ $r->id }}" name="xp" min="0" max="1000" value="{{ $r->effect_xp }}" class="inline w-8 border-0 border-b p-0"> 
                        GP:<input type="number" id="pgp{{ $r->id }}" name="gp" min="0" max="1000" value="{{ $r->effect_gp }}" class="inline w-8 border-0 border-b p-0"> 
                        <select id="pitem{{ $r->id }}" name="pitem" class="ms-1 inline w-12 border-0 border-b p-0">
                        <option value="0"></option>
                        @foreach ($items as $i)
                        <option value="{{ $i->id }}"{{ $i->id == $r->effect_item ? ' selected' : '' }}>{{ $i->name }}</option>
                        @endforeach
                        </select>
                    </label>
                    </li>
                    @endforeach
                    <li>
                    <input type="radio" id="p0" name="positive" value="0" class="hidden peer" />
                    <label for="p0" class="inline-block w-full  p-2 text-gray-500 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-teal-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-teal-600 dark:peer-checked:text-blue-300 peer-checked:text-blue-600 hover:bg-teal-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-teal-700">
                        <input type="text" id="p_reason" name="reason" class="inline w-96 border-0 border-b p-0" placeholder="請輸入臨時獎勵條款...">
                        XP:<input type="number" id="pxp" name="xp" min="0" max="1000" class="inline w-8 border-0 border-b p-0"> 
                        GP:<input type="number" id="pgp" name="gp" min="0" max="1000" class="inline w-8 border-0 border-b p-0"> 
                        <select name="item" id="pitem" class="ms-1 inline w-12 border-0 border-b p-0">
                        <option value="0"></option>
                        @foreach ($items as $i)
                        <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                        </select>
                    </label>
                    </li>
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="positive_act();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即執行
                </button>
                <button onclick="restore();positiveModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="negativeModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 id="modalHeader" class="text-center text-xl font-semibold text-gray-900 dark:text-white">請選擇懲罰條款：</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul>
                    @foreach ($negative_rules as $r)
                    <li>
                    <input type="radio" id="{{ $r->id }}" name="negative" value="{{ $r->id }}" class="hidden peer" />
                    <label for="{{ $r->id }}" class="inline-block w-full p-2 text-gray-500 bg-white border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <span class="inline-block w-[28rem]">{{ $r->description }}</span>
                        HP:<input type="number" id="nhp{{ $r->id }}" name="hp" min="0" max="1000" value="{{ $r->effect_hp }}" class="inline w-8 border-0 border-b p-0"> 
                        MP:<input type="number" id="nmp{{ $r->id }}" name="mp" min="0" max="1000" value="{{ $r->effect_mp }}" class="inline w-8 border-0 border-b p-0"> 
                    </label>
                    </li>
                    @endforeach
                    <li>
                    <input type="radio" id="n0" name="negative" value="0" class="hidden peer" />
                    <label for="n0" class="inline-block w-full p-2 text-gray-500 bg-white border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <input type="text" id="n_reason" name="reason" class="inline w-[28rem] border-0 border-b p-0" placeholder="請輸入臨時懲罰條款...">
                        HP:<input type="number" id="nhp" name="hp" min="0" max="1000" class="inline w-8 border-0 border-b p-0">
                        MP:<input type="number" id="nmp" name="mp" min="0" max="1000" class="inline w-8 border-0 border-b p-0">
                    </label>
                    </li>
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="negative_act();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即執行
                </button>
                <button onclick="negative_delay();" type="button" class="ms-3 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    延遲處置
                </button>
                <button onclick="restore();negativeModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="skillsModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">技能書</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul id="skillList" >
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="skill_cast();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即行動
                </button>
                <button onclick="skillsModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="itemsModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">背包</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul id="itemList" >
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="item_use();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即使用
                </button>
                <button onclick="itemsModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="teammateModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[70] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">對象</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul id="memberList" >
                </ul>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="cast();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    立即使用
                </button>
                <button onclick="teamModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="editModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[80] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">快速編輯</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <p><div class="p-3">
                    <label for="party" class="text-base">隸屬公會：</label>
                    <select id="party" name="party" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                        @foreach ($parties as $p)
                        <option value="{{ $p->id }}">{{ $p->group_no }} {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div></p>
                <p><div class="p-3">
                    <label for="title" class="text-base">榮譽稱號：</label>
                    <input id="title" class="w-1/3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="text" name="title" value="">
                </div></p>
                <p><div class="p-3">
                    <label for="profession" class="text-base">職業：</label>
                    <select id="profession" name="profession" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                        @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div></p>
            </div>
            <div class="w-full inline-flex justify-center gap-2 p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="revive();" type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    復活
                </button>
                <button onclick="fastedit();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    修改
                </button>
                <button onclick="editModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var characters = [];
    var character;
    var data_type;
    var data_skill;
    var data_item;
    var skills = [];
    var items = [];
    var members = [];
    var uuids = [];
    var positive = [];
    @foreach ($positive_rules as $rule)
    positive[{{ $rule->id }}] = {!! $rule->toJson(JSON_UNESCAPED_UNICODE) !!};
    @endforeach
    var negative = [];
    @foreach ($negative_rules as $rule)
    negative[{{ $rule->id }}] = {!! $rule->toJson(JSON_UNESCAPED_UNICODE) !!};
    @endforeach

    var $targetEl = document.getElementById('warnModal');
    const warnModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('positiveModal');
    const positiveModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('negativeModal');
    const negativeModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('skillsModal');
    const skillsModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('itemsModal');
    const itemsModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('teammateModal');
    const teamModal = new window.Modal($targetEl);
    $targetEl = document.getElementById('editModal');
    const editModal = new window.Modal($targetEl);

    function auto_absent(cls) {
        window.axios.post('{{ route('game.auto_absent') }}', {
            room_id: cls,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            var myuuids = response.data.present;
            for (var k in myuuids) {
                document.getElementById('absent' + myuuids[k]).checked = false;
            }
            var myuuids = response.data.absent;
            for (var k in myuuids) {
                document.getElementById('absent' + myuuids[k]).checked = true;
            }
        });
    }

    function absent(uuid) {
        var node = document.getElementById(uuid);
        if (document.getElementById('absent' + uuid).checked) {
            var value = 'yes';
            node.checked = false;
            node.disabled = true;
        } else {
            var value = 'no';
            node.disabled = false;
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
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]:not([data-group="no"])');
        nodes.forEach( (node) => {
            if (value == 'yes') {
                if (document.getElementById('absent' + node.id).checked) {
                    node.checked = false;
                } else {
                    node.checked = true;
                }
            } else {
                node.checked = false;
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
                if (document.getElementById('absent' + node.id).checked) {
                    node.checked = false;
                } else {
                    node.checked = true;
                }
            } else {
                node.checked = false;
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
                if (document.getElementById('absent' + node.id).checked) {
                    node.checked = false;
                } else {
                    node.checked = true;
                }
            } else {
                node.checked = false;
            }
        });
    }

    function select_none() {
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]');
        nodes.forEach( (node) => {
            node.checked = false;
        });
    }

    function openModal(type) {
        const nodes = document.querySelectorAll('input[type="checkbox"][data-group]:checked');
        if (nodes.length < 1) {
            var msg = document.getElementById('message');
            msg.innerHTML = '請先選擇對象！';
            warnModal.show();
        } else {
            if (type == 1) {
                positiveModal.show();
            } else {
                negativeModal.show();
            }
        }
    }

    function restore() {
        var nodes = document.querySelectorAll('input[name="positive"]');
        nodes.forEach( (node) => {
            if (node.id != 'p0') {
                document.getElementById('pxp' + node.id).value = positive[node.id].effect_xp;
                document.getElementById('pgp' + node.id).value = positive[node.id].effect_gp;
                document.getElementById('pitem' + node.id).value = positive[node.id].effect_item;
            }
        });
        document.getElementById('p_reason').value = '';
        document.getElementById('pxp').value = '';
        document.getElementById('pgp').value = '';
        document.getElementById('pitem').value = '';

        var nodes = document.querySelectorAll('input[name="negative"]');
        nodes.forEach( (node) => {
            if (node.id != 'n0') {
                document.getElementById('nhp' + node.id).value = negative[node.id].effect_hp;
                document.getElementById('nmp' + node.id).value = negative[node.id].effect_mp;
            }
        });
        document.getElementById('n_reason').value = '';
        document.getElementById('nhp').value = '';
        document.getElementById('nmp').value = '';
    }

    function positive_act() {
        uuids = [];
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]:checked');
        nodes.forEach( (node) => {
            uuids.push(node.id);
        });
        var rule = document.querySelector('input[name="positive"]:checked');
        if (rule == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇條款！';
            warnModal.show();
            return;
        }
        var rule_id = rule.value;
        var reason = document.getElementById('p_reason').value;
        if (rule_id == 0) {
            var xp = document.getElementById('pxp').value;
            var gp = document.getElementById('pgp').value;
            var item = document.getElementById('pitem').value;
        } else {
            var xp = document.getElementById('pxp' + rule_id).value;
            var gp = document.getElementById('pgp' + rule_id).value;
            var item = document.getElementById('pitem' + rule_id).value;
        }
        if (rule_id == 0 && reason == '') {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未輸入獎勵原因！';
            warnModal.show();
            return;
        }
        if (xp == 0 && gp == 0 && item == 0) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未輸入獎勵點數或道具！';
            warnModal.show();
            return;
        }
        positiveModal.hide();
        restore();
        window.axios.post('{{ route('game.positive_act') }}', {
            uuids: uuids.toString(),
            rule: rule_id,
            reason: reason,
            xp: xp,
            gp: gp,
            item: item,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            for (var k in response.data.characters) {
                characters[response.data.characters[k].seat] = response.data.characters[k];
            };
            characters.forEach(char => {
                var lvl = document.getElementById('level' + char.seat);
                lvl.innerHTML = char.level;
                var stat = document.getElementById('status' + char.seat);
                stat.innerHTML = char.status_desc;
                var hp = document.getElementById('hp' + char.seat);
                hp.innerHTML = char.hp + '/' + char.max_hp;
                var mp = document.getElementById('mp' + char.seat);
                mp.innerHTML = char.mp + '/' + char.max_mp;
                var ap = document.getElementById('ap' + char.seat);
                ap.innerHTML = char.final_ap + '[' + char.ap + ']';
                var dp = document.getElementById('dp' + char.seat);
                dp.innerHTML = char.final_dp + '[' + char.dp + ']';
                var sp = document.getElementById('sp' + char.seat);
                sp.innerHTML = char.final_sp + '[' + char.sp + ']';
                var xp = document.getElementById('xp' + char.seat);
                xp.innerHTML = char.xp;
                var gp = document.getElementById('gp' + char.seat);
                gp.innerHTML = char.gp;
            });
            select_none();
        });
    }

    function negative_act() {
        uuids = [];
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]:checked');
        nodes.forEach( (node) => {
            uuids.push(node.id);
        });
        var rule = document.querySelector('input[name="negative"]:checked');
        if (rule == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇條款！';
            warnModal.show();
            return;
        }
        var rule_id = rule.value;
        var reason = document.getElementById('n_reason').value;
        if (rule_id == 0) {
            var hp = document.getElementById('nhp').value;
            var mp = document.getElementById('nmp').value;
        } else {
            var hp = document.getElementById('nhp' + rule_id).value;
            var mp = document.getElementById('nmp' + rule_id).value;
        }
        if (rule_id == 0 && reason == '') {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未輸入懲罰理由！';
            warnModal.show();
            return;
        }
        if (hp == 0 && mp == 0) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未輸入懲罰點數！';
            warnModal.show();
            return;
        }
        negativeModal.hide();
        restore();
        window.axios.post('{{ route('game.negative_act') }}', {
            uuids: uuids.toString(),
            rule: rule_id,
            reason: reason,
            hp: hp,
            mp: mp,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            for (var k in response.data.characters) {
                characters[response.data.characters[k].seat] = response.data.characters[k];
            };
            characters.forEach(char => {
                var lvl = document.getElementById('level' + char.seat);
                lvl.innerHTML = char.level;
                var stat = document.getElementById('status' + char.seat);
                stat.innerHTML = char.status_desc;
                var hp = document.getElementById('hp' + char.seat);
                hp.innerHTML = char.hp + '/' + char.max_hp;
                var mp = document.getElementById('mp' + char.seat);
                mp.innerHTML = char.mp + '/' + char.max_mp;
                var ap = document.getElementById('ap' + char.seat);
                ap.innerHTML = char.final_ap + '[' + char.ap + ']';
                var dp = document.getElementById('dp' + char.seat);
                dp.innerHTML = char.final_dp + '[' + char.dp + ']';
                var sp = document.getElementById('sp' + char.seat);
                sp.innerHTML = char.final_sp + '[' + char.sp + ']';
                var xp = document.getElementById('xp' + char.seat);
                xp.innerHTML = char.xp;
                var gp = document.getElementById('gp' + char.seat);
                gp.innerHTML = char.gp;
            });
            select_none();
        });
    }

    function negative_delay() {
        uuids = [];
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]:checked');
        nodes.forEach( (node) => {
            uuids.push(node.id);
        });
        var rule = document.querySelector('input[name="negative"]:checked');
        if (rule == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇條款！';
            warnModal.show();
            return;
        }
        var rule_id = rule.value;
        var reason = document.getElementById('n_reason').value;
        if (rule_id == 0) {
            var hp = document.getElementById('nhp').value;
            var mp = document.getElementById('nmp').value;
        } else {
            var hp = document.getElementById('nhp' + rule_id).value;
            var mp = document.getElementById('nmp' + rule_id).value;
        }
        if (hp == 0 && mp == 0) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未輸入懲罰點數！';
            warnModal.show();
            return;
        }
        negativeModal.hide();
        restore();
        window.axios.post('{{ route('game.negative_delay') }}', {
            uuids: uuids.toString(),
            rule: rule_id,
            reason: reason,
            hp: hp,
            mp: mp,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            var url = response.data.url;
            var delay = response.data.delay;
            var delays = document.getElementById('delayUL');
            var li = document.createElement('li');
            li.classList.add('border-b');
            var href = document.createElement('a');
            href.setAttribute('href', url);
            href.classList.add('block','px-4','py-2','hover:bg-gray-100','dark:hover:bg-gray-600','dark:hover:text-white');
            href.innerHTML = delay.description;
            li.appendChild(href);
            delays.appendChild(li);
            var info = document.getElementById('delay_info');
            if (info) info.classList.add('hidden');
        });
        select_none();
    }

    function prepare_skill(uuid) {
        var ul = document.getElementById('skillList');
        ul.innerHTML = '';
        window.axios.post('{{ route('game.get_skills') }}', {
            uuid: uuid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            character = response.data.character;
            skills = [];
            for (var k in response.data.skills) {
                var skill = response.data.skills[k];
                skills[skill.id] = skill;
            }
            if (skills.length > 0) {
                skills.forEach( skill => {
                    var li = document.createElement('li');
                    var radio = document.createElement('input');
                    radio.id = 'skill' + skill.id;
                    radio.value = skill.id;
                    radio.setAttribute('type', 'radio');
                    radio.setAttribute('name', 'skill');
                    radio.classList.add('hidden','peer');
                    li.appendChild(radio);
                    var label = document.createElement('label');
                    label.setAttribute('for', 'skill' + skill.id);
                    label.classList.add('inline-block','w-full','p-2','text-gray-500','bg-white','rounded-lg','border-2','border-gray-200','cursor-pointer','peer-checked:border-blue-600','hover:text-teal-600','peer-checked:text-blue-600','hover:bg-teal-50');
                    var name = document.createElement('div');
                    name.classList.add('inline-block','w-auto','text-base');
                    name.innerHTML = skill.name;
                    label.appendChild(name);
                    var cost = document.createElement('div');
                    cost.classList.add('inline-block','w-16','text-base','text-center');
                    cost.innerHTML = '-' + skill.cost_mp + 'MP';
                    label.appendChild(cost);
                    if (skill.ap > 0) {
                        var ap = document.createElement('div');
                        ap.classList.add('inline-block','w-16','text-base','text-center');
                        ap.innerHTML = skill.ap + 'AP';
                        label.appendChild(ap);
                    }
                    if (skill.xp > 0) {
                        var xp = document.createElement('div');
                        xp.classList.add('inline-block','w-16','text-base','text-center');
                        xp.innerHTML = skill.xp + 'XP';
                        label.appendChild(xp);
                    }
                    if (skill.gp > 0) {
                        var gp = document.createElement('div');
                        gp.classList.add('inline-block','w-16','text-base','text-center');
                        gp.innerHTML = skill.gp + 'GP';
                        label.appendChild(gp);
                    }
                    var help = document.createElement('div');
                    help.classList.add('inline-block','w-96','text-sm');
                    help.innerHTML = skill.description;
                    label.appendChild(help);
                    li.appendChild(label);
                    ul.appendChild(li);
                });
            } else {
                if (character.class_id == '') {
                    ul.innerHTML = '尚未設定職業，所以沒有任何技能！';
                } else {
                    ul.innerHTML = '沒有可用的技能！';
                }
            }
            skillsModal.show();
        });
    }

    function prepare_item(uuid) {
        var ul = document.getElementById('itemList');
        ul.innerHTML = '';
        window.axios.post('{{ route('game.get_items') }}', {
            uuid: uuid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            character = response.data.character;
            items = [];
            for (var k in response.data.items) {
                var item = response.data.items[k];
                items[item.id] = item;
            }
            if (character.class_id == '') {
                ul.innerHTML = '尚未設定職業，無法使用道具！';
            } else if (items.length > 0) {
                items.forEach( item => {
                    var li = document.createElement('li');
                    var radio = document.createElement('input');
                    radio.id = 'bag' + item.id;
                    radio.value = item.id;
                    radio.setAttribute('type', 'radio');
                    radio.setAttribute('name', 'item');
                    radio.classList.add('hidden','peer');
                    li.appendChild(radio);
                    var label = document.createElement('label');
                    label.setAttribute('for', 'bag' + item.id);
                    label.classList.add('inline-block','w-full','p-2','text-gray-500','bg-white','rounded-lg','border-2','border-gray-200','cursor-pointer','peer-checked:border-blue-600','hover:text-teal-600','peer-checked:text-blue-600','hover:bg-teal-50');
                    var name = document.createElement('div');
                    name.classList.add('inline-block','w-auto','text-base');
                    name.innerHTML = item.name;
                    label.appendChild(name);
                    var quantity = document.createElement('div');
                    quantity.classList.add('inline-block','w-16','text-base','text-center');
                    quantity.innerHTML = item.pivot.quantity + '個';
                    label.appendChild(quantity);
                    if (item.hp > 0) {
                        var hp = document.createElement('div');
                        hp.classList.add('inline-block','w-16','text-base','text-center');
                        hp.innerHTML = item.hp + 'HP';
                        label.appendChild(hp);
                    }
                    if (item.mp > 0) {
                        var mp = document.createElement('div');
                        mp.classList.add('inline-block','w-16','text-base','text-center');
                        mp.innerHTML = item.mp + 'MP';
                        label.appendChild(mp);
                    }
                    if (item.ap > 0) {
                        var ap = document.createElement('div');
                        ap.classList.add('inline-block','w-16','text-base','text-center');
                        ap.innerHTML = item.ap + 'AP';
                        label.appendChild(ap);
                    }
                    if (item.dp > 0) {
                        var dp = document.createElement('div');
                        dp.classList.add('inline-block','w-16','text-base','text-center');
                        dp.innerHTML = item.dp + 'DP';
                        label.appendChild(dp);
                    }
                    if (item.sp > 0) {
                        var sp = document.createElement('div');
                        sp.classList.add('inline-block','w-16','text-base','text-center');
                        sp.innerHTML = item.sp + 'SP';
                        label.appendChild(sp);
                    }
                    var help = document.createElement('div');
                    help.classList.add('inline-block','w-full','text-sm');
                    help.innerHTML = item.description;
                    label.appendChild(help);
                    li.appendChild(label);
                    ul.appendChild(li);
                });
            } else {
                ul.innerHTML = '沒有任何道具！';
            }
            itemsModal.show();
        });
    }

    function skill_cast() {
        var skill_obj = document.querySelector('input[name="skill"]:checked');
        if (skill_obj == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇技能！';
            warnModal.show();
            return;
        }
        skillsModal.hide();
        data_type = 'skill';
        data_skill = skill_obj.value;
        var data_target = skills[data_skill].object;
        var data_inspire = skills[data_skill].inspire;
        if (data_inspire == 'throw') {
            data_type = 'skill_then_item';
            prepare_item(character);
            return;
        }
        if (data_target == 'partner') {
            var ul = document.getElementById('memberList');
            ul.innerHTML = '';
            window.axios.post('{{ route('game.get_teammate') }}', {
                uuid: character.uuid,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                members = [];
                for (var k in response.data.teammate) {
                    var mate = response.data.teammate[k];
                    members[k] = mate;
                }
                if (members.length > 0) {
                    members.forEach( partner => {
                        var li = document.createElement('li');
                        var radio = document.createElement('input');
                        radio.id = 'team' + partner.uuid;
                        radio.value = partner.uuid;
                        radio.setAttribute('type', 'radio');
                        radio.setAttribute('name', 'teammate');
                        radio.classList.add('hidden','peer');
                        li.appendChild(radio);
                        var label = document.createElement('label');
                        label.setAttribute('for', 'team' + partner.uuid);
                        label.classList.add('inline-block','w-full','p-2','text-gray-500','bg-white','rounded-lg','border-2','border-gray-200','cursor-pointer','peer-checked:border-blue-600','hover:text-teal-600','peer-checked:text-blue-600','hover:bg-teal-50');
                        var seat = document.createElement('div');
                        seat.classList.add('inline-block','w-48','text-base');
                        seat.innerHTML = partner.seat;
                        label.appendChild(seat);
                        var name = document.createElement('div');
                        name.classList.add('inline-block','w-48','text-base');
                        name.innerHTML = partner.name;
                        label.appendChild(name);
                        li.appendChild(label);
                        ul.appendChild(li);
                    });
                }
            });
            teamModal.show();
        } else {
            window.axios.post('{{ route('game.skill_cast') }}', {
                self: character.uuid,
                target: character.uuid,
                skill: data_skill,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                for (var k in response.data.characters) {
                    characters[response.data.characters[k].seat] = response.data.characters[k];
                };
                characters.forEach(char => {
                    var lvl = document.getElementById('level' + char.seat);
                    lvl.innerHTML = char.level;
                    var stat = document.getElementById('status' + char.seat);
                    stat.innerHTML = char.status_desc;
                    var hp = document.getElementById('hp' + char.seat);
                    hp.innerHTML = char.hp + '/' + char.max_hp;
                    var mp = document.getElementById('mp' + char.seat);
                    mp.innerHTML = char.mp + '/' + char.max_mp;
                    var ap = document.getElementById('ap' + char.seat);
                    ap.innerHTML = char.final_ap + '[' + char.ap + ']';
                    var dp = document.getElementById('dp' + char.seat);
                    dp.innerHTML = char.final_dp + '[' + char.dp + ']';
                    var sp = document.getElementById('sp' + char.seat);
                    sp.innerHTML = char.final_sp + '[' + char.sp + ']';
                    var xp = document.getElementById('xp' + char.seat);
                    xp.innerHTML = char.xp;
                    var gp = document.getElementById('gp' + char.seat);
                    gp.innerHTML = char.gp;
                });
            });
        }
    }

    function item_use() {
        var item_obj = document.querySelector('input[name="item"]:checked');
        if (item_obj == null) {
            var msg = document.getElementById('message');
            msg.innerHTML = '您尚未選擇道具！';
            warnModal.show();
            return;
        }
        itemsModal.hide();
        if (data_type == 'skill_then_item') {
            data_type = 'item_after_skill';
        } else {
            data_type = 'item';
        }
        data_item = item_obj.value;
        var data_target = items[data_item].object;
        if (data_target == 'partner' || data_type == 'item_after_skill') {
            var ul = document.getElementById('memberList');
            ul.innerHTML = '';
            window.axios.post('{{ route('game.get_teammate') }}', {
                uuid: character.uuid,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                members = [];
                for (var k in response.data.teammate) {
                    var mate = response.data.teammate[k];
                    members[k] = mate;
                }
                if (members.length > 0) {
                    members.forEach( partner => {
                        var li = document.createElement('li');
                        var radio = document.createElement('input');
                        radio.id = 'team' + partner.uuid;
                        radio.value = partner.uuid;
                        radio.setAttribute('type', 'radio');
                        radio.setAttribute('name', 'teammate');
                        radio.classList.add('hidden','peer');
                        li.appendChild(radio);
                        var label = document.createElement('label');
                        label.setAttribute('for', 'team' + partner.uuid);
                        label.classList.add('inline-block','w-full','p-2','text-gray-500','bg-white','rounded-lg','border-2','border-gray-200','cursor-pointer','peer-checked:border-blue-600','hover:text-teal-600','peer-checked:text-blue-600','hover:bg-teal-50');
                        var seat = document.createElement('div');
                        seat.classList.add('inline-block','w-48','text-base');
                        seat.innerHTML = partner.seat;
                        label.appendChild(seat);
                        var name = document.createElement('div');
                        name.classList.add('inline-block','w-48','text-base');
                        name.innerHTML = partner.name;
                        label.appendChild(name);
                        li.appendChild(label);
                        ul.appendChild(li);
                    });
                }
            });
            teamModal.show();
        } else {
            window.axios.post('{{ route('game.item_use') }}', {
                self: character.uuid,
                target: character.uuid,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                for (var k in response.data.characters) {
                    characters[response.data.characters[k].seat] = response.data.characters[k];
                };
                characters.forEach(char => {
                    var lvl = document.getElementById('level' + char.seat);
                    lvl.innerHTML = char.level;
                    var stat = document.getElementById('status' + char.seat);
                    stat.innerHTML = char.status_desc;
                    var hp = document.getElementById('hp' + char.seat);
                    hp.innerHTML = char.hp + '/' + char.max_hp;
                    var mp = document.getElementById('mp' + char.seat);
                    mp.innerHTML = char.mp + '/' + char.max_mp;
                    var ap = document.getElementById('ap' + char.seat);
                    ap.innerHTML = char.final_ap + '[' + char.ap + ']';
                    var dp = document.getElementById('dp' + char.seat);
                    dp.innerHTML = char.final_dp + '[' + char.dp + ']';
                    var sp = document.getElementById('sp' + char.seat);
                    sp.innerHTML = char.final_sp + '[' + char.sp + ']';
                    var xp = document.getElementById('xp' + char.seat);
                    xp.innerHTML = char.xp;
                    var gp = document.getElementById('gp' + char.seat);
                    gp.innerHTML = char.gp;
                });
            });
        }
    }

    function cast() {
        if (data_type == 'skill') {
            var obj = document.querySelector('input[name="teammate"]:checked');
            if (obj == null) {
                var msg = document.getElementById('message');
                msg.innerHTML = '您尚未選擇技能施展對象！';
                warnModal.show();
                return;
            }
            teamModal.hide();
            window.axios.post('{{ route('game.skill_cast') }}', {
                self: character.uuid,
                target: obj.value,
                skill: data_skill,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                for (var k in response.data.characters) {
                    characters[response.data.characters[k].seat] = response.data.characters[k];
                };
                characters.forEach(char => {
                    var lvl = document.getElementById('level' + char.seat);
                    lvl.innerHTML = char.level;
                    var stat = document.getElementById('status' + char.seat);
                    stat.innerHTML = char.status_desc;
                    var hp = document.getElementById('hp' + char.seat);
                    hp.innerHTML = char.hp + '/' + char.max_hp;
                    var mp = document.getElementById('mp' + char.seat);
                    mp.innerHTML = char.mp + '/' + char.max_mp;
                    var ap = document.getElementById('ap' + char.seat);
                    ap.innerHTML = char.final_ap + '[' + char.ap + ']';
                    var dp = document.getElementById('dp' + char.seat);
                    dp.innerHTML = char.final_dp + '[' + char.dp + ']';
                    var sp = document.getElementById('sp' + char.seat);
                    sp.innerHTML = char.final_sp + '[' + char.sp + ']';
                    var xp = document.getElementById('xp' + char.seat);
                    xp.innerHTML = char.xp;
                    var gp = document.getElementById('gp' + char.seat);
                    gp.innerHTML = char.gp;
                });
            });
        }
        if (data_type == 'item_after_skill') {
            var obj = document.querySelector('input[name="teammate"]:checked');
            if (obj == null) {
                var msg = document.getElementById('message');
                msg.innerHTML = '您尚未選擇技能施展對象！';
                warnModal.show();
                return;
            }
            teamModal.hide();
            window.axios.post('{{ route('game.skill_cast') }}', {
                self: character.uuid,
                target: obj.value,
                skill: data_skill,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                for (var k in response.data.characters) {
                    characters[response.data.characters[k].seat] = response.data.characters[k];
                };
                characters.forEach(char => {
                    var lvl = document.getElementById('level' + char.seat);
                    lvl.innerHTML = char.level;
                    var stat = document.getElementById('status' + char.seat);
                    stat.innerHTML = char.status_desc;
                    var hp = document.getElementById('hp' + char.seat);
                    hp.innerHTML = char.hp + '/' + char.max_hp;
                    var mp = document.getElementById('mp' + char.seat);
                    mp.innerHTML = char.mp + '/' + char.max_mp;
                    var ap = document.getElementById('ap' + char.seat);
                    ap.innerHTML = char.final_ap + '[' + char.ap + ']';
                    var dp = document.getElementById('dp' + char.seat);
                    dp.innerHTML = char.final_dp + '[' + char.dp + ']';
                    var sp = document.getElementById('sp' + char.seat);
                    sp.innerHTML = char.final_sp + '[' + char.sp + ']';
                    var xp = document.getElementById('xp' + char.seat);
                    xp.innerHTML = char.xp;
                    var gp = document.getElementById('gp' + char.seat);
                    gp.innerHTML = char.gp;
                });
            });
        }
        if (data_type == 'item') {
            var obj = document.querySelector('input[name="teammate"]:checked');
            if (obj == null) {
                var msg = document.getElementById('message');
                msg.innerHTML = '您尚未選擇道具使用對象！';
                warnModal.show();
                return;
            }
            teamModal.hide();
            window.axios.post('{{ route('game.item_use') }}', {
                self: character.uuid,
                target: obj.value,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                for (var k in response.data.characters) {
                    characters[response.data.characters[k].seat] = response.data.characters[k];
                };
                characters.forEach(char => {
                    var lvl = document.getElementById('level' + char.seat);
                    lvl.innerHTML = char.level;
                    var stat = document.getElementById('status' + char.seat);
                    stat.innerHTML = char.status_desc;
                    var hp = document.getElementById('hp' + char.seat);
                    hp.innerHTML = char.hp + '/' + char.max_hp;
                    var mp = document.getElementById('mp' + char.seat);
                    mp.innerHTML = char.mp + '/' + char.max_mp;
                    var ap = document.getElementById('ap' + char.seat);
                    ap.innerHTML = char.final_ap + '[' + char.ap + ']';
                    var dp = document.getElementById('dp' + char.seat);
                    dp.innerHTML = char.final_dp + '[' + char.dp + ']';
                    var sp = document.getElementById('sp' + char.seat);
                    sp.innerHTML = char.final_sp + '[' + char.sp + ']';
                    var xp = document.getElementById('xp' + char.seat);
                    xp.innerHTML = char.xp;
                    var gp = document.getElementById('gp' + char.seat);
                    gp.innerHTML = char.gp;
                });
            });
        }
    }

    function prepare_character(uuid) {
        window.axios.post('{{ route('game.get_character') }}', {
            uuid: uuid,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            character = response.data.character;
            var party = document.getElementById('party');
            for (var op in party.options) {
                if (party.options[op].value == character.party_id) {
                    party.options[op].selected = true;
                } else {
                    party.options[op].selected = false;
                }
            }
            var title = document.getElementById('title');
            if (character.title) {
                title.value = character.title;
            } else {
                title.value = '';
            }
            var myclass = document.getElementById('profession');
            for (var op in myclass.options) {
                if (myclass.options[op].value == character.class_id) {
                    myclass.options[op].selected = true;
                } else {
                    myclass.options[op].selected = false;
                }
            }
            editModal.show();
        });
    }

    function fastedit() {
        editModal.hide();
        var party = document.getElementById('party').value;
        var title = document.getElementById('title').value;
        var profession = document.getElementById('profession').value;
        window.axios.post('{{ route('game.character_edit') }}', {
            uuid: character.uuid,
            party: party,
            title: title,
            profession: profession,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            location.reload();
        });
    }

    function revive() {
        editModal.hide();
        var party = document.getElementById('party').value;
        var title = document.getElementById('title').value;
        var profession = document.getElementById('profession').value;
        window.axios.post('{{ route('game.character_revive') }}', {
            uuid: character.uuid,
            party: party,
            title: title,
            profession: profession,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            location.reload();
        });
    }
</script>
@endsection
