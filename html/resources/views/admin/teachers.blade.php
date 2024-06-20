@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">教職員</div>
<label for="unit" class="inline p-2">行政單位：</label>
<select id="unit" class="inline rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    onchange="query('unit')">
    <option value=""></option>
    @foreach ($units as $u)
    <option value="{{ $u->id }}"{{ ($current == $u->id) ? ' selected' : '' }}>{{ $u->name }}</option>
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
        <th scope="col" class="p-2">
            識別碼
        </th>
        @if (empty($current))
        <th scope="col" class="p-2">
            單位
        </th>
        @endif
        <th scope="col" class="p-2">
            職稱
        </th>
        @if ($current == 24)
        <th scope="col" class="p-2">
            導師班級
        </th>
        @endif
        @if ($current == 25)
        <th scope="col" class="p-2">
            隸屬領域
        </th>
        @endif
        <th scope="col" class="p-2">
            姓名
        </th>
        <th scope="col" class="p-2">
            帳號
        </th>
        <th scope="col" class="p-2">
            電子郵件
        </th>
        <th scope="col" class="p-2">
            管理
        </th>
    </tr>
    @foreach ($teachers as $t)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600{{ ($t->trashed() ? ' text-red-700' : '')}}">
        <td class="p-2">{{ $t->uuid }}</td>
        @if (empty($current))
        <td class="p-2">{{ ($t->mainunit) ? $t->mainunit->name : '' }}</td>
        @endif
        <td class="p-2">{{ $t->role_name }}</td>
        @if ($current == 24)
        <td class="p-2">{{ ($t->tutor) ?: '' }}</td>
        @endif
        @if ($current == 25)
        <td class="p-2">{{ ($t->domain) ? $t->domain->name : '' }}</td>
        @endif
        <td class="p-2">{{ $t->realname }}</td>
        <td class="p-2">{{ $t->account }}</td>
        <td class="p-2">{{ $t->email }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" title="編輯"
                href="{{ route('teachers.edit', ['uuid' => $t->uuid]) }}">
                <i class="fa-solid fa-user-pen"></i>
            </a>
            <button class="py-2 pr-6 text-green-300 hover:text-green-600" title="同步"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('teachers.sync', ['uuid' => $t->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-rotate"></i>
            </button>
            @if ($t->trashed())
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="回復"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('teachers.restore', ['uuid' => $t->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash-arrow-up"></i>
            </button>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="徹底刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('teachers.destroy', ['uuid' => $t->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-burst"></i>
            </button>
            @else
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('teachers.remove', ['uuid' => $t->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </button>
            @endif
        </td>
    </tr>
    @endforeach
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
<script nonce="selfhost">
    function query(main) {
        var search = '';
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
            window.location.replace('{{ route('teachers') }}' + '/' + search);
        } else {
            var unit_id = document.getElementById('unit').value;
            window.location.replace('{{ route('teachers', ['search' => 'unit=' . $current ]) }}');
        }
    }
</script>
@endsection
