@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增職務層級
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('units') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 m-5" role="alert">
    <p>
        在單一身份驗證服務中，職務層級用來分辨行政人員的等階，目前已知層級為校長 C01，
        主任 C02，組長 C03，其它行政人員 C04，級任教師 C05，科任教師 C06，約聘僱人員 C99。
        職務層級代號為 3 碼，同一職務層級在不同行政單位可以設定不同職稱，通常會使用該職務的簡稱。
    </p>
</div>
<form id="edit-unit" action="{{ route('roles.add') }}" method="POST">
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
    <label for="organize" class="inline-flex relative items-center cursor-pointer">
    <input type="checkbox" id="organize" name="organize" value="yes" class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
        納入職編
    </label>　　
    <label for="role_name" class="inline p-2">職稱：</label>
    <input class="inline w-36 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
        type="text" name="role_name">
    <div class="inline py-4 px-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            新增
        </button>
    </div>
    </div>
</form>
@endsection
