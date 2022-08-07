@extends('layouts.admin')

@section('content')
<div class="relative m-5">
    <div class="p-10">
        @if (session('error'))
        <div class="border border-red-500 bg-red-100 border-b-2" role="alert">
            {{ session('error') }}
        </div>
        @endif
        @if (session('success'))
        <div class="border border-green-500 bg-green-100 border-b-2" role="alert">
            {{ session('success') }}
        </div>
        @endif
        <div class="text-2xl font-bold leading-normal pb-5">
            新增職務層級
            <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('units') }}">
                <i class="fa-solid fa-eject"></i>返回上一頁
            </a>
        </div>
        <div class="border-blue-500 bg-blue-100 border-b-2 m-5" role="alert">
            <p>
                在單一身份驗證服務中，職務層級用來分辨行政人員的等階，目前已知層級為校長 C01，
                主任 C02，組長 C03，其它行政人員 C04，級任教師 C05，科任教師 C06，約聘僱人員 C99。
                職務層級代號為 3 碼，同一職務層級在不同行政單位可以設定不同職稱，通常會使用該職務的簡稱。
            </p>
        </div>
        <form id="edit-unit" action="{{ route('units') }}" method="POST">
            @csrf
            <div class="block">
            <label for="role_id" class="inline p-2">職級代號：</label>
            <input class="inline w-24 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="role_id">　　
            <label for="role_unit" class="inline p-2">隸屬單位：</label>
            <select class="inline w-36 rounded px-3 py-2 border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                name="role_unit">
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
            @endforeach
            </select>　　
            <label for="role_name" class="inline p-2">職稱：</label>
            <input class="inline w-36 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="role_name">
            <div` class="inline py-4 px-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    新增
                </button>
            </div>
            </div>
        </form>
    </div>
</div>
@endsection
