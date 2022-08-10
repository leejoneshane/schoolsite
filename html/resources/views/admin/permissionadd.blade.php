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
        <div class="text-2xl font-bold leading-normal pb-5">
            新增權限
            <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('permission') }}">
                <i class="fa-solid fa-eject"></i>返回上一頁
            </a>
        </div>
        <div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
            <p>
                單一應用稱為 APP，例如：社團報名。該應用可能需要多種不同權限，例如：社團分類、社團管理、學生管理、收費統計...等。
                有些權限可以直接透過登入身分取得，因此無需新增該權限，例如：學生自動取得報名、取消報名權限。
            </p>
        </div>
        <form id="edit-unit" action="{{ route('permission.add') }}" method="POST">
            @csrf
            <div class="block">
            <label for="app" class="inline p-2">APP 代碼：</label>
            <input class="inline w-40 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="app" value="{{ old('app') }}" autofocus required pattern="[a-z0-9]+" placeholder="請輸入英數半形">　　
            <label for="perm" class="inline p-2">權限代碼：</label>
            <input class="inline w-40 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="perm" value="{{ old('perm') }}" autofocus required pattern="[a-z0-9]+" placeholder="請輸入英數半形">　　
            <p class="mt-2">
                <label for="perm" class="inline p-2">權限描述：</label>
                <input class="inline w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" name="desc" value="{{ old('desc') }}" required placeholder="請輸入中文">
            </p>
            <p class="mt-2">
                <div` class="block py-4 px-6">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        新增
                    </button>
                </div>
            </p>
            </div>
        </form>
    </div>
</div>
@endsection
