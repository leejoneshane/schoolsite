@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯場地或設備
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('venues') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-club" action="{{ route('venue.edit', ['id' => $venue->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <p><div class="p-3">
        <label for="title" class="inline">名稱：</label>
        <input type="text" id="title" name="title" value="{{ $venue->name }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </div></p>
    <p><div class="p-3">
        <label for="manager" class="inline">管理員：</label>
        <select id="manager" name="manager" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        @foreach ($teachers as $t)
        @php
            $gap = '';
            $rname = '';
            if ($t->role_name) $rname = $t->role_name;
            for ($i=0;$i<6-mb_strlen($rname);$i++) {
                $gap .= '　';
            }
            $display = $t->role_name . $gap . $t->realname;
        @endphp
        <option value="{{ $t->uuid }}"{{ ($t->uuid == $venue->uuid) ? ' selected' : '' }}>{{ $display }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="description" class="inline">借用須知：</label>
        <textarea id="description" name="description" rows="4" class="inline p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >{{ $venue->description }}</textarea>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請輸入場地容留人數、可使用設備（如：單槍、大屏、麥克風、有無網路...等）、鑰匙保管方式，設備請輸入外觀、規格、配件、使用條件或限制...等資訊！</span>
    </div></p>
    <p><div class="p-3">
        <label class="inline">附加螢幕截圖：</label>
        <input type="file" name="reserved_info" accept="image/png,image/jpeg" capture="camera" class="block text-sm text-slate-500 py-2 px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>顯示於場地預約畫面右側，僅支援 .png 或 .jpg 兩種格式。</span>
    </div></p>
    <p><div class="p-3">
        <label class="inline">不出借時段：</label>
        <input type="checkbox" name="unavailable" value="yes" onclick="
            const sdate = document.getElementById('sdate');
            const edate = document.getElementById('edate');
            if (this.checked) {
                sdate.removeAttribute('disabled');
                edate.removeAttribute('disabled');
            } else {
                sdate.setAttribute('disabled', '');
                edate.setAttribute('disabled', '');
            }
        "{{ ($venue->denytime) ? ' checked' : '' }}>
        <input class="w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" id="sdate" name="startdate" value="{{ $venue->unavailable_at }}"{{ ($venue->denytime == '無') ? ' disabled' : '' }}>　到　
        <input class="w-36 rounded px-2 py-5 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" id="edate" name="enddate" value="{{ $venue->unavailable_until }}"{{ ($venue->denytime == '無') ? ' disabled' : '' }}>
    </div></p>
    <p><div class="p-3">
        <label class="inline">不出借節次：</label>
        <table class="inline border-collapse text-sm text-left">
            <thead>
                <tr class="font-semibold text-lg">
                    <th class="border border-slate-300">星期</th>
                    <th class="border border-slate-300">一</th>
                    <th class="border border-slate-300">二</th>
                    <th class="border border-slate-300">三</th>
                    <th class="border border-slate-300">四</th>
                    <th class="border border-slate-300">五</th>
                </tr>    
            </thead>
            <tbody>
                @foreach ($venue->sessions as $key => $se)
                <tr>
                    <th class="border border-slate-300 font-semibold text-lg">{{ $se }}</th>
                    <td class="border border-slate-300"><input type="checkbox" name="map[0][{{ $key }}]" value="yes"{{ ($venue->available[0][$key]) ? '' : ' checked' }}></td>
                    <td class="border border-slate-300"><input type="checkbox" name="map[1][{{ $key }}]" value="yes"{{ ($venue->available[1][$key]) ? '' : ' checked' }}></td>
                    <td class="border border-slate-300"><input type="checkbox" name="map[2][{{ $key }}]" value="yes"{{ ($venue->available[2][$key]) ? '' : ' checked' }}></td>
                    <td class="border border-slate-300"><input type="checkbox" name="map[3][{{ $key }}]" value="yes"{{ ($venue->available[3][$key]) ? '' : ' checked' }}></td>
                    <td class="border border-slate-300"><input type="checkbox" name="map[4][{{ $key }}]" value="yes"{{ ($venue->available[4][$key]) ? '' : ' checked' }}></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div></p>
    <p><div class="p-3">
        <label for="start" class="inline">幾天後開始預約？</label>
        <input type="number" id="start" name="start" value="{{ $venue->schedule_start }}" min="0" max="180" inputmode="numeric" class="inline w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">天後
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請輸入數字，0或留白代表當天可預約！</span>
    </div></p>
    <p><div class="p-3">
        <label for="limit" class="inline">可預約幾天內的空堂？</label>
        <input type="number" id="limit" name="limit" value="{{ $venue->schedule_limit }}" min="0" max="180" inputmode="numeric" class="inline w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">天內
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請輸入數字，0或留白代表無限制！</span>
    </div></p>
    <p><div class="p-3">
        <label for="open" class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" id="open" name="open" value="yes" class="sr-only peer"{{ ($venue->open) ? ' checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-gray-900 dark:text-gray-300">開放預約</span>
        </label>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
@endsection
