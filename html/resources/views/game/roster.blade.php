@extends('layouts.game')

@section('content')
<p><div class="pb-3">
    @locked($room->id)
    <p class="w-full text-center">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" onclick="openModal(1);">
            <i class="fa-solid fa-plus"></i>獎勵
        </button>
        <button class="ml-6 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full" onclick="openModal(2);">
            <i class="fa-solid fa-minus"></i>懲罰
        </button>
    </p>
    <input type="checkbox" id="all" onchange="select_all();" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
    @endlocked
    <div class="inline text-3xl">{{ $room->name }}</div>
</div></p>
@foreach ($parties as $p)
<p><div class="pb-3">
    @locked($room->id)
    <input type="checkbox" id="group{{ $p->id }}" data-party="{{ $p->id }}" onchange="select_party({{ $p->id }});" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
    @endlocked
    <div class="inline text-2xl">{{ $p->name }}</div>
    <br><span class="text-sm">{{ $p->description }}</span>
</div></p>
<div class="relative inline-flex items-center justify-between w-full py-4 rounded-md border border-teal-300 mb-6 bg-cover bg-center z-0" style="background-image: url('{{ $p->fundation && $p->fundation->avaliable() ? $p->fundation->url() : '' }}')">
    <div class="absolute w-full h-full z-10 opacity-30" /></div>
    <table class="w-full h-full z-20 text-left font-normal">
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
                等級
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
            <td class="p-2">{{ $s->name }}</td>
            <td class="p-2">
                @locked($room->id)
                <input type="checkbox" id="absent{{ $s->uuid }}" name="absent" value="yes"{{ ($s->absent) ? ' checked' : '' }} onchange="absent('{{ $s->uuid }}');" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-white disabled:border-gray-100">
                @endlocked
            </td>
            <td class="p-2">{{ ($s->profession) ? $s->profession->name : '無'}}</td>
            <td class="p-2">{{ $s->level }}</td>
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
            <td class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
            <td class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
            <td class="p-2">{{ $s->final_ap }}</td>
            <td class="p-2">{{ $s->final_dp }}</td>
            <td class="p-2">{{ $s->final_sp }}</td>
            <td class="p-2">{{ $s->xp }}</td>
            <td class="p-2">{{ $s->gp }}</td>
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
        <td class="p-2">{{ $s->hp }}/{{ $s->max_hp }}</td>
        <td class="p-2">{{ $s->mp }}/{{ $s->max_mp }}</td>
        <td class="p-2">{{ $s->gp }}</td>
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
<div id="warnModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[80] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
<div id="positiveModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
                        XP:<input type="number" id="xp{{ $r->id }}" name="xp" value="{{ $r->effect_xp }}" class="inline w-8 border-0 border-b p-0"> 
                        GP:<input type="number" id="gp{{ $r->id }}" name="gp" value="{{ $r->effect_gp }}" class="inline w-8 border-0 border-b p-0"> 
                        <select id="item{{ $r->id }}" name="item" class="ms-1 inline w-12 border-0 border-b p-0">
                        <option value=""></option>
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
                        XP:<input type="number" id="xp" name="xp" class="inline w-8 border-0 border-b p-0"> 
                        GP:<input type="number" id="gp" name="gp" class="inline w-8 border-0 border-b p-0"> 
                        <select name="item" id="item" class="ms-1 inline w-12 border-0 border-b p-0">
                        <option value=""></option>
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
<div id="negativeModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
                        HP:<input type="number" id="hp{{ $r->id }}" name="hp" value="{{ $r->effect_hp }}" class="inline w-8 border-0 border-b p-0"> 
                        MP:<input type="number" id="mp{{ $r->id }}" name="mp" value="{{ $r->effect_mp }}" class="inline w-8 border-0 border-b p-0"> 
                    </label>
                    </li>
                    @endforeach
                    <li>
                    <input type="radio" id="n0" name="negative" value="0" class="hidden peer" />
                    <label for="n0" class="inline-block w-full w-full p-2 text-gray-500 bg-white border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <input type="text" id="n_reason" name="reason" class="inline w-[28rem] border-0 border-b p-0" placeholder="請輸入臨時懲罰條款...">
                        HP:<input type="number" id="hp" name="hp" class="inline w-8 border-0 border-b p-0">
                        MP:<input type="number" id="mp" name="mp" class="inline w-8 border-0 border-b p-0">
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
<div id="skillsModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
<div id="itemsModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
<div id="teammateModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[70] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
<div id="editModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[80] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
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
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
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
    var character;
    var data_type;
    var data_skill;
    var data_item;
    var skills = [];
    var items = [];
    var uuids = [];
    var positive = [];
    @foreach ($positive_rules as $rule)
    positive[{{ $rule->id }}] = { 'xp':'{{ $rule->effect_xp }}', 'gp':'{{ $rule->effect_gp }}', 'item':'{{ $rule->effect_item }}'  };
    @endforeach
    var negative = [];
    @foreach ($negative_rules as $rule)
    negative[{{ $rule->id }}] = { 'hp':'{{ $rule->effect_hp }}', 'mp':'{{ $rule->effect_mp }}'  };
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
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]');
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
                document.getElementById('xp' + node.id).value = positive[node.id].xp;
                document.getElementById('gp' + node.id).value = positive[node.id].gp;
                document.getElementById('item' + node.id).value = positive[node.id].item;
            }
        });
        document.getElementById('p_reason').value = '';
        document.getElementById('xp').value = '';
        document.getElementById('gp').value = '';
        document.getElementById('item').value = '';

        var nodes = document.querySelectorAll('input[name="negative"]');
        nodes.forEach( (node) => {
            if (node.id != 'n0') {
                document.getElementById('hp' + node.id).value = negative[node.id].hp;
                document.getElementById('mp' + node.id).value = negative[node.id].mp;
            }
        });
        document.getElementById('n_reason').value = '';
        document.getElementById('hp').value = '';
        document.getElementById('mp').value = '';
    }

    function positive_act() {
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
        positiveModal.hide();
        var rule_id = rule.value;
        var reason = document.getElementById('p_reason').value;
        if (rule_id == 0) {
            var xp = document.getElementById('xp').value;
            var gp = document.getElementById('gp').value;
            var item = document.getElementById('item').value;
        } else {
            var xp = document.getElementById('xp' + rule_id).value;
            var gp = document.getElementById('gp' + rule_id).value;
            var item = document.getElementById('item' + rule_id).value;
        }
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
        });
        window.location.reload();
    }

    function negative_act() {
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
        negativeModal.hide();
        var rule_id = rule.value;
        var reason = document.getElementById('n_reason').value;
        if (rule_id == 0) {
            var hp = document.getElementById('hp').value;
            var mp = document.getElementById('mp').value;
        } else {
            var hp = document.getElementById('hp' + rule_id).value;
            var mp = document.getElementById('mp' + rule_id).value;
        }
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
        });
        window.location.reload();
    }

    function negative_delay() {
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
        negativeModal.hide();
        var rule_id = rule.value;
        var reason = document.getElementById('n_reason').value;
        if (rule_id == 0) {
            var hp = document.getElementById('hp').value;
            var mp = document.getElementById('mp').value;
        } else {
            var hp = document.getElementById('hp' + rule_id).value;
            var mp = document.getElementById('mp' + rule_id).value;
        }
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
        });
        window.location.reload();
    }

    function prepare_skill(uuid) {
        character = uuid;
        var ul = document.getElementById('skillList');
        ul.innerHTML = '';
        window.axios.post('{{ route('game.get_skills') }}', {
            uuid: character,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
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
                    cost.classList.add('inline-block','w-16','text-base');
                    cost.innerHTML = '-' + skill.cost_mp + 'MP';
                    label.appendChild(cost);
                    if (skill.ap > 0) {
                        var ap = document.createElement('div');
                        ap.classList.add('inline-block','w-16','text-base');
                        ap.innerHTML = skill.ap + 'AP';
                        label.appendChild(ap);
                    }
                    if (skill.xp > 0) {
                        var xp = document.createElement('div');
                        xp.classList.add('inline-block','w-16','text-base');
                        xp.innerHTML = skill.xp + 'XP';
                        label.appendChild(xp);
                    }
                    if (skill.gp > 0) {
                        var gp = document.createElement('div');
                        gp.classList.add('inline-block','w-16','text-base');
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
                ul.innerHTML = '沒有可用的技能！';
            }
            skillsModal.show();
        });
    }

    function prepare_item(uuid) {
        character = uuid;
        var ul = document.getElementById('itemList');
        ul.innerHTML = '';
        window.axios.post('{{ route('game.get_items') }}', {
            uuid: character,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            items = [];
            for (var k in response.data.items) {
                var item = response.data.items[k];
                items[item.id] = item;
            }
            if (items.length > 0) {
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
                    quantity.classList.add('inline-block','w-16','text-base','pl-4');
                    quantity.innerHTML = item.pivot.quantity + '個';
                    label.appendChild(quantity);
                    if (item.hp > 0) {
                        var hp = document.createElement('div');
                        hp.classList.add('inline-block','w-16','text-base','pl-4');
                        hp.innerHTML = item.hp + 'HP';
                        label.appendChild(hp);
                    }
                    if (item.mp > 0) {
                        var mp = document.createElement('div');
                        mp.classList.add('inline-block','w-16','text-base','pl-4');
                        mp.innerHTML = item.mp + 'MP';
                        label.appendChild(mp);
                    }
                    if (item.ap > 0) {
                        var ap = document.createElement('div');
                        ap.classList.add('inline-block','w-16','text-base','pl-4');
                        ap.innerHTML = item.ap + 'AP';
                        label.appendChild(ap);
                    }
                    if (item.dp > 0) {
                        var dp = document.createElement('div');
                        dp.classList.add('inline-block','w-16','text-base','pl-4');
                        dp.innerHTML = item.dp + 'DP';
                        label.appendChild(dp);
                    }
                    if (item.sp > 0) {
                        var sp = document.createElement('div');
                        sp.classList.add('inline-block','w-16','text-base','pl-4');
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
                uuid: character,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                uuids = [];
                for (var k in response.data.teammate) {
                    var mate = response.data.teammate[k];
                    uuids.push(mate.uuid);
                }
                if (uuids.length > 0) {
                    uuids.forEach( partner => {
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
                self: character,
                target: character,
                skill: data_skill,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            window.location.reload();
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
        var data_target = items[data_id].object;
        if (data_target == 'partner' || data_type == 'item_after_skill') {
            var ul = document.getElementById('memberList');
            ul.innerHTML = '';
            window.axios.post('{{ route('game.get_teammate') }}', {
                uuid: character,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( response => {
                uuids = [];
                for (var k in response.data.teammate) {
                    var mate = response.data.teammate[k];
                    uuids.push(mate.uuid);
                }
                if (uuids.length > 0) {
                    uuids.forEach( partner => {
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
                self: character,
                target: character,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            window.location.reload();
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
            teammateModal.hide();
            window.axios.post('{{ route('game.skill_cast') }}', {
                self: character,
                target: obj.value,
                skill: data_skill,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            window.location.reload();
        }
        if (data_type == 'item_after_skill') {
            var obj = document.querySelector('input[name="teammate"]:checked');
            if (obj == null) {
                var msg = document.getElementById('message');
                msg.innerHTML = '您尚未選擇技能施展對象！';
                warnModal.show();
                return;
            }
            teammateModal.hide();
            window.axios.post('{{ route('game.skill_cast') }}', {
                self: character,
                target: obj.value,
                skill: data_skill,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            window.location.reload();
        }
        if (data_type == 'item') {
            var obj = document.querySelector('input[name="teammate"]:checked');
            if (obj == null) {
                var msg = document.getElementById('message');
                msg.innerHTML = '您尚未選擇道具使用對象！';
                warnModal.show();
                return;
            }
            teammateModal.hide();
            window.axios.post('{{ route('game.item_use') }}', {
                self: character,
                target: obj.value,
                item: data_item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            window.location.reload();
        }
    }

    function prepare_character(uuid) {
        character = uuid;
        window.axios.post('{{ route('game.get_character') }}', {
            uuid: character,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            var temp = response.data;
            var party = document.getElementById('party');
            for (var op in party.options) {
                if (party.options[op].value == temp.party_id) {
                    party.options[op].selected = true;
                } else {
                    party.options[op].selected = false;
                }
            }
            var title = document.getElementById('title');
            if (temp.title) {
                title.value = temp.title;
            } else {
                title.value = '';
            }
            var myclass = document.getElementById('profession');
            for (var op in myclass.options) {
                if (myclass.options[op].value == temp.class_id) {
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
            uuid: character,
            party: party,
            title: title,
            profession: profession,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        window.location.reload();
    }
</script>
@endsection
