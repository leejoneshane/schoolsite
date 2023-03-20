@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">教職員</div>
<label for="unit" class="inline p-2">行政單位：</label>
<select id="unit" class="inline rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    onchange="
        var unit_id = this.value;
        window.location.replace('{{ route('teachers') }}' + '/unit=' + unit_id);
">
    <option value=""></option>
    @foreach ($units as $u)
    <option value="{{ $u->id }}"{{ ($current == $u->id) ? ' selected' : '' }}>{{ $u->name }}</option>
    @endforeach
</select>
<label for="domain" class="inline p-2">隸屬領域：</label>
<select id="domain" class="inline rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    onchange="
        var domain_id = this.value;
        window.location.replace('{{ route('teachers') }}' + '/domain=' + domain_id);
">
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
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $t->uuid }}</td>
        @if (empty($current))
        <td class="p-2">{{ $t->mainunit->name }}</td>
        @endif
        <td class="p-2">{{ $t->role_name }}</td>
        <td class="p-2">{{ $t->realname }}</td>
        <td class="p-2">{{ $t->account }}</td>
        <td class="p-2">{{ $t->email }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600"
                href="{{ route('teachers.edit', ['uuid' => $t->uuid]) }}">
                <i class="fa-solid fa-user-pen"></i>
            </a>
            <button class="py-2 pr-6 text-green-300 hover:text-green-600"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('teachers.sync', ['uuid' => $t->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-rotate"></i>
            </button>
            <a class="py-2 pr-6 text-red-300 hover:text-red-600" href="void()"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('teachers.remove', ['uuid' => $t->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash"></i>
            </a>
        </td>
    </tr>
    @endforeach
    <form class="hidden" id="remove" action="" method="POST">
        @csrf
    </form>
</table>
<script>
    function query() {
        var search = '';
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
            window.location.replace('{{ route('teachers') }}' + '/unit=' + unit_id);
        }
    }
</script>
@endsection
