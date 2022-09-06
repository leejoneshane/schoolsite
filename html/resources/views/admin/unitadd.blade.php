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
            新增行政單位
            <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('units') }}">
                <i class="fa-solid fa-eject"></i>返回上一頁
            </a>
        </div>
        <div class="border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 m-5" role="alert">
            <p>
                行政單位分為上層單位與次級單位，上層單位代號為 3 碼，次級單位代號為 6 碼，
                其前 3 碼即該次級單位的上層單位代號！<br>在單一身份驗證服務中，上層單位通常就是
                處室，次級單位即該處室中所有行政人員的完整職稱（即包含處室名稱＋職稱）。
            </p>
        </div>
        <form id="edit-unit" action="{{ route('units.add') }}" method="POST">
            @csrf
            <div class="block">
            <label for="unit_id" class="inline p-2">單位代號：</label>
            <input class="inline w-24 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="unit_id">　　
            <label for="unit_name" class="inline p-2">單位名稱：</label>
            <input class="inline w-64 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="unit_name">
            <div class="inline py-4 px-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    新增
                </button>
            </div>
            </div>
        </form>
    </div>
</div>
@endsection
