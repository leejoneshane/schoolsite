@extends('layouts.admin')

@section('content')
<div class="relative m-5">
    <div class="p-10">
        @if (session('error'))
        <div class="border border-red-500 bg-red-100 dark:bg-red-700 border-b-2" role="alert">
            {{ session('error') }}
        </div>
        @endif
        @if (session('success'))
        <div class="border border-green-500 bg-green-100 dark:bg-green-700 border-b-2" role="alert">
            {{ session('success') }}
        </div>
        @endif
        <div class="text-2xl font-bold leading-normal pb-5">教職員</div>
        <select id="units" class="block w-full py-2.5 px-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
            onchange="
            var url = this.value;
            window.location.replace('{{ route('teachers') }}' + '/' + url);
            ">
            @foreach ($units as $u)
            <option value="{{ $u->id }}" {{ ($current == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
        <table class="w-full py-4 text-left font-normal">
            <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
                <th scope="col" class="p-2">
                    識別碼
                </th>
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
                <td class="p-2">{{ $t->role_name }}</td>
                <td class="p-2">{{ $t->realname }}</td>
                <td class="p-2">{{ $t->account }}</td>
                <td class="p-2">{{ $t->email }}</td>
                <td class="p-2">
                    <a class="py-2 px-6 text-blue-300 hover:text-blue-600"
                        href="{{ route('teachers.edit', ['uuid' => $t->uuid]) }}">
                        <i class="fa-solid fa-user-pen"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
        <div class="mb-10"></div>
    </div>
</div>
@endsection
