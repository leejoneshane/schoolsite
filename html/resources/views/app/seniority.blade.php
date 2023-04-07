@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    年資統計
    @if ($year == $current)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seniority.future') }}">
        <i class="fa-solid fa-file-export"></i>自動產生年資並下載校對稿
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seniority.import') }}">
        <i class="fa-solid fa-file-import"></i>匯入年資 Excel
    </a>
    @endif
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seniority.export', ['year' => $year]) }}">
        <i class="fa-solid fa-file-export"></i>匯出本學期總表
    </a>
</div>
<label for="years">請選擇學年度：</label>
<select id="years" class="inline w-16 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
    onchange="query()">
    @foreach ($years as $y)
    <option value="{{ $y }}"{{ ($year == $y) ? ' selected' : '' }}>{{ $y }}</option>
    @endforeach
</select>
<label for="unit" class="inline p-2">行政單位：</label>
<select id="unit" class="inline rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    onchange="query('unit')">
    <option value=""></option>
    @foreach ($units as $u)
    <option value="{{ $u->id }}"{{ ($unit == $u->id) ? ' selected' : '' }}>{{ $u->name }}</option>
    @endforeach
</select>
<label for="domain" class="inline p-2">隸屬領域：</label>
<select id="domain" class="inline rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    onchange="query('domain')">
    <option value=""></option>
    @foreach ($domains as $d)
    <option value="{{ $d->id }}"{{ ($domain == $d->id) ? ' selected' : '' }}>{{ $d->name }}</option>
    @endforeach
</select>
<label for="idno" class="inline p-2">身份證字號：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="idno" value="{{ $idno }}" onchange="query()">
<label for="name" class="inline p-2">姓名：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="name" value="{{ $realname }}" onchange="query()">
<label for="email" class="inline p-2">電子郵件：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="email" value="{{ $email }}" onchange="query()">
<i class="fa-solid fa-magnifying-glass" onclick="query()"></i>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" rowspan="2" class="px-2 text-center">
            職別
        </th>
        <th scope="col" rowspan="2" class="px-2 text-center">
            姓名
        </th>
        <th colspan="7" class="px-2 text-center bg-green-300 dark:bg-green-500">
            人事室概算
        </th>
        <th colspan="6" class="px-2 text-center bg-blue-300 dark:bg-blue-500">
            修正後
        </th>
        <th rowspan="2" class="px-2 text-center">
            管理
        </th>
    </tr>
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="w-16 text-center">
            在校年
        </th>
        <th scope="col" class="w-16 text-center">
            在校月
        </th>
        <th scope="col" class="w-16 text-center">
            校外年
        </th>
        <th scope="col" class="w-16 text-center">
            校外月
        </th>
        <th scope="col" class="w-16 text-center">
            年資
        </th>
        <th scope="col" class="w-16 text-center">
            積分
        </th>
        <th scope="col" class="w-16 text-center">
            校正
        </th>
        <th scope="col" class="w-16 text-center">
            在校年
        </th>
        <th scope="col" class="w-16 text-center">
            在校月
        </th>
        <th scope="col" class="w-16 text-center">
            校外年
        </th>
        <th scope="col" class="w-16 text-center">
            校外月
        </th>
        <th scope="col" class="w-16 text-center">
            年資
        </th>
        <th scope="col" class="w-16 text-center">
            積分
        </th>
    </tr>
    @forelse ($teachers as $teacher)
    @php
        $seniority = $teacher->seniority($year);
    @endphp
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ ($teacher->tutor) ?: (($teacher->unit_id == 25 && $teacher->domain) ? $teacher->domain->name : $teacher->role_name) }}</td>
        <td class="p-2">{{ $teacher->realname }}</td>
        <td id="osy{{ $loop->iteration }}" class="text-right text-green-700 dark:text-green-300">{{ $seniority->school_year }}</td>
        <td id="osm{{ $loop->iteration }}" class="text-right text-green-700 dark:text-green-300">{{ $seniority->school_month }}</td>
        <td id="oty{{ $loop->iteration }}" class="text-right text-green-700 dark:text-green-300">{{ $seniority->teach_year }}</td>
        <td id="otm{{ $loop->iteration }}" class="text-right text-green-700 dark:text-green-300">{{ $seniority->teach_month }}</td>
        <td id="oy{{ $loop->iteration }}" class="text-center text-green-700 dark:text-green-300">{{ $seniority->years }}</td>
        <td id="os{{ $loop->iteration }}" class="text-center text-green-700 dark:text-green-300">{{ $seniority->score }}</td>
        <td class="p-2">
            @if (($manager))
            <button id="edit{{ $loop->iteration }}" class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                title="編輯" onclick="edit_default('{{ $loop->iteration }}')">
                <i class="fa-solid fa-pen"></i>
            </button>
            <button id="save{{ $loop->iteration }}" class="hidden py-2 pr-6 text-blue-300 hover:text-blue-600"
                title="儲存" onclick="save_default('{{ $loop->iteration }}')">
                <i class="fa-solid fa-floppy-disk"></i>
            </button>
            @endif
        </td>
        <td id="nsy{{ $loop->iteration }}" class="text-right text-blue-700 dark:text-blue-300">{{ $seniority->new_school_year }}</td>
        <td id="nsm{{ $loop->iteration }}" class="text-right text-blue-700 dark:text-blue-300">{{ $seniority->new_school_month }}</td>
        <td id="nty{{ $loop->iteration }}" class="text-right text-blue-700 dark:text-blue-300">{{ $seniority->new_teach_year }}</td>
        <td id="ntm{{ $loop->iteration }}" class="text-right text-blue-700 dark:text-blue-300">{{ $seniority->new_teach_month }}</td>
        <td id="ny{{ $loop->iteration }}" class="text-center text-blue-700 dark:text-blue-300">{{ $seniority->newyears }}</td>
        <td id="ns{{ $loop->iteration }}" class="text-center text-blue-700 dark:text-blue-300">{{ $seniority->newscore }}</td>
        <td class="p-2">
        @if (Auth::user()->uuid == $seniority->uuid)
            @if ($seniority->ok)
            <button class="py-2 pr-6 text-blue-500">
                <i class="fa-solid fa-check"></i>
            </button>
            @else
            <button id="edit{{ $loop->iteration }}" class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                title="編輯" onclick="edit('{{ $loop->iteration }}')">
                <i class="fa-solid fa-pen"></i>
            </button>
            <button id="save{{ $loop->iteration }}" class="hidden py-2 pr-6 text-blue-300 hover:text-blue-600"
                title="儲存" onclick="save('{{ $loop->iteration }}')">
                <i class="fa-solid fa-floppy-disk"></i>
            </button>
            <label for="ok{{ $loop->iteration }}" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="ok{{ $loop->iteration }}" name="{{ $teacher->uuid }}" value="yes" class="sr-only peer"{{ $seniority->ok ? ' checked' : '' }} onclick="checkit('{{ $loop->iteration }}')">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-gray-900 dark:text-gray-300">正確無誤</span>
            </label>
            @endif
        @endif
        </td>
    </tr>
    @empty
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <th colspan="15" class="p-2 text-3xl font-semibold text-center">找不到年資紀錄！</th>
    </tr>
    @endforelse
</table>
{{ $teachers->links('pagination::tailwind') }}
<script>
    var input = false;
    function checkit(target) {
        box = document.getElementById('ok' + target);
        btn1 = document.getElementById('edit' + target);
        btn2 = document.getElementById('save' + target);
        nsy = document.getElementById('nsy' + target);
        nsm = document.getElementById('nsm' + target);
        nty = document.getElementById('nty' + target);
        ntm = document.getElementById('ntm' + target);
        ny = document.getElementById('ny' + target);
        ns = document.getElementById('ns' + target);
        if (box.checked) {
            btn1.classList.add('hidden');
            btn2.classList.add('hidden');
            if (input) {
                nsy.innerHTML = '';
                nsm.innerHTML = '';
                nty.innerHTML = '';
                ntm.innerHTML = '';
                ny.innerHTML = '0';
                ns.innerHTML = '0';
            }
            window.axios.post('{{ route('seniority.confirm') }}', {
                uuid: box.name,
                year: {{ $year }},
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        } else {
            btn1.classList.remove('hidden');
            btn2.classList.add('hidden');
            window.axios.post('{{ route('seniority.cancel') }}', {
                uuid: box.name,
                year: {{ $year }},
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }

    function edit(target) {
        input = true;
        box = document.getElementById('ok' + target);
        btn1 = document.getElementById('edit' + target);
        btn2 = document.getElementById('save' + target);
        nsy = document.getElementById('nsy' + target);
        nsm = document.getElementById('nsm' + target);
        nty = document.getElementById('nty' + target);
        ntm = document.getElementById('ntm' + target);
        ny = document.getElementById('ny' + target);
        ns = document.getElementById('ns' + target);
        nsy_value = nsy.innerText;
        nsy.innerHTML = '';
        const elem_nsy = document.createElement('input');
        elem_nsy.classList.add('w-16','border','border-gray-300','focus:border-blue-700','focus:ring-1','focus:ring-blue-700','focus:outline-none','active:outline-none','dark:border-gray-400','dark:focus:border-blue-600','bg-opacity-100','text-black','dark:text-gray-200');
        elem_nsy.type = 'text';
        elem_nsy.name = 'new_school_year';
        elem_nsy.value = nsy_value;
        nsy.appendChild(elem_nsy);
        nsm_value = nsm.innerText;
        nsm.innerHTML = '';
        const elem_nsm = document.createElement('input');
        elem_nsm.classList.add('w-16','border','border-gray-300','focus:border-blue-700','focus:ring-1','focus:ring-blue-700','focus:outline-none','active:outline-none','dark:border-gray-400','dark:focus:border-blue-600','bg-opacity-100','text-black','dark:text-gray-200');
        elem_nsm.type = 'text';
        elem_nsm.name = 'new_school_month';
        elem_nsm.value = nsm_value;
        nsm.appendChild(elem_nsm);
        nty_value = nty.innerText;
        nty.innerHTML = '';
        const elem_nty = document.createElement('input');
        elem_nty.classList.add('w-16','border','border-gray-300','focus:border-blue-700','focus:ring-1','focus:ring-blue-700','focus:outline-none','active:outline-none','dark:border-gray-400','dark:focus:border-blue-600','bg-opacity-100','text-black','dark:text-gray-200');
        elem_nty.type = 'text';
        elem_nty.name = 'new_teach_year';
        elem_nty.value = nty_value;
        nty.appendChild(elem_nty);
        ntm_value = ntm.innerText;
        ntm.innerHTML = '';
        const elem_ntm = document.createElement('input');
        elem_ntm.classList.add('w-16','border','border-gray-300','focus:border-blue-700','focus:ring-1','focus:ring-blue-700','focus:outline-none','active:outline-none','dark:border-gray-400','dark:focus:border-blue-600','bg-opacity-100','text-black','dark:text-gray-200');
        elem_ntm.type = 'text';
        elem_ntm.name = 'new_teach_month';
        elem_ntm.value = ntm_value;
        ntm.appendChild(elem_ntm);
        btn1.classList.add('hidden');
        btn2.classList.remove('hidden');
    }

    function save(target) {
        input = false;
        box = document.getElementById('ok' + target);
        btn1 = document.getElementById('edit' + target);
        btn2 = document.getElementById('save' + target);
        nsy = document.getElementById('nsy' + target);
        nsm = document.getElementById('nsm' + target);
        nty = document.getElementById('nty' + target);
        ntm = document.getElementById('ntm' + target);
        ny = document.getElementById('ny' + target);
        ns = document.getElementById('ns' + target);
        nsy_value = nsy.firstChild.value;
        nsm_value = nsm.firstChild.value;
        nty_value = nty.firstChild.value;
        ntm_value = ntm.firstChild.value;
        window.axios.post('{{ route('seniority.update') }}', {
            uuid: box.name,
            year: {{ $year }},
            new_school_year: nsy_value,
            new_school_month: nsm_value,
            new_teach_year: nty_value,
            new_teach_month: ntm_value,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(function (response) {
            var seniority = response.data;
            nsy.innerHTML = seniority.new_school_year;
            nsm.innerHTML = seniority.new_school_month;
            nty.innerHTML = seniority.new_teach_year;
            ntm.innerHTML = seniority.new_teach_month;
            ny.innerHTML = seniority.newyears;
            ns.innerHTML = seniority.newscore;
        });
        btn1.classList.remove('hidden');
        btn2.classList.add('hidden');
    }

    function query(main) {
        var search = '';
        var year = document.getElementById('years').value;
        if (year) {
            search = search + 'year=' + year + '&';
        }
        if (main == 'unit') {
            var unit = document.getElementById('unit').value;
            search = search + 'unit=' + unit + '&';
        }
        if (main == 'domain') {
            var domain = document.getElementById('domain').value;
            search = search + 'domain=' + domain + '&';
        }
        var idno = document.getElementById('idno').value;
        if (idno) {
            search = search + 'idno=' + idno + '&';
        }
        var myname = document.getElementById('name').value;
        if (myname) {
            search = search + 'name=' + myname + '&';
        }
        var email = document.getElementById('email').value;
        if (email) {
            search = search + 'email=' + email + '&';
        }
        search = search.slice(0, -1);
        if (search) {
            window.location.replace('{{ route('seniority') }}' + '/' + search);
        } else {
            window.location.replace('{{ route('seniority') }}' + '/year=' + year);
        }
    }
</script>
@endsection
