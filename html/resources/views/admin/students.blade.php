@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">學生</div>
<label for="classes" class="inline p-2">就讀班級：</label>
<select id="classes" class="inline rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    onchange="
    var cls = this.value;
    window.location.replace('{{ route('students') }}' + '/class=' + cls);
    ">
    @foreach ($classes as $cls)
    <option value="{{ $cls->id }}" {{ ($current == $cls->id) ? 'selected' : '' }}>{{ $cls->name }}</option>
    @endforeach
</select>
<label for="idno" class="inline p-2">身份證字號：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="idno" value="{{ $idno }}" onchange="query()">
<label for="idno" class="inline p-2">學號：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="id" value="{{ $id }}" onchange="query()">
<label for="name" class="inline p-2">姓名：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="name" value="{{ $realname }}" onchange="query()">
<label for="email" class="inline p-2">電子郵件：</label>
<input class="inline w-32 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    type="text" id="email" value="{{ $email }}" onchange="query()">
<i class="fa-solid fa-magnifying-glass" onclick="query()"></i>
<table class="w-full py-4 text-left font-normal">
    <tr class="font-semibold text-lg">
        <th scope="col" class="p-2">
            識別碼
        </th>
        <th scope="col" class="p-2">
            學號
        </th>
        @if (empty($current))
        <th scope="col" class="p-2">
            班級
        </th>
        @endif
        <th scope="col" class="p-2">
            座號
        </th>
        <th scope="col" class="p-2">
            姓名
        </th>
        <th scope="col" class="p-2">
            性別
        </th>
        <th scope="col" class="p-2">
            電子郵件
        </th>
    </tr>
    @foreach ($students as $s)
    @php
        switch($s->gender) {
            case 0:
                $gender = '未知';
                break;
            case 1:
                $gender = '男';
                break;
            case 2:
                $gender = '女';
                break;
            case 9:
                $gender = '其他';
        }
    @endphp
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600{{ ($s->trashed() ? ' text-red-700' : '')}}">
        <td class="p-2">{{ $s->uuid }}</td>
        <td class="p-2">{{ $s->id }}</td>
        @if (empty($current))
        <td class="p-2">{{ $s->class_id }}</td>
        @endif
        <td class="p-2">{{ $s->seat }}</td>
        <td class="p-2">{{ $s->realname }}</td>
        <td class="p-2">{{ $gender }}</td>
        <td class="p-2">{{ $s->email }}</td>
        <td class="p-2">
            <a class="py-2 pr-6 text-blue-300 hover:text-blue-600" title="編輯"
                href="{{ route('students.edit', ['uuid' => $s->uuid]) }}">
                <i class="fa-solid fa-user-pen"></i>
            </a>
            <button class="py-2 pr-6 text-green-300 hover:text-green-600" title="回復密碼"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('students.password', ['uuid' => $s->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-key"></i>
            </button>
            <button class="py-2 pr-6 text-green-300 hover:text-green-600" title="同步"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('students.sync', ['uuid' => $s->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-rotate"></i>
            </button>
            @if ($s->trashed())
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="回復"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('students.restore', ['uuid' => $s->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-trash-arrow-up"></i>
            </button>
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="徹底刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('students.destroy', ['uuid' => $s->uuid]) }}';
                    myform.submit();
            ">
                <i class="fa-solid fa-burst"></i>
            </button>
            @else
            <button class="py-2 pr-6 text-red-300 hover:text-red-600" title="刪除"
                onclick="
                    const myform = document.getElementById('remove');
                    myform.action = '{{ route('students.remove', ['uuid' => $s->uuid]) }}';
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
    function query() {
        var search = '';
    var idno = document.getElementById('idno').value;
    if (idno) {
        search = search + 'idno=' + idno + '&';
    }
    var id = document.getElementById('id').value;
    if (id) {
        search = search + 'id=' + id + '&';
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
        window.location.replace('{{ route('students') }}' + '/' + search);
    } else {
        var cls = document.getElementById('classes').value;
        window.location.replace('{{ route('students') }}' + '/class=' + cls);
    }
}
</script>
@endsection
