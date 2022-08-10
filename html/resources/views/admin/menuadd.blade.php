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
            新增選單項目
            <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ empty($current) ? route('menus') : route('menus', ['menu' => $current]) }}">
                <i class="fa-solid fa-eject"></i>返回上一頁
            </a>
        </div>
        <div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
            <p>
                URL 若設定為「#」，該選單項目會轉變成「次選單」，您也可以直接從，<span class="font-semibold">類別選單</span>選取。
                外部 URL 應包含完整路徑，若省略 https://host 則視為本機連結，本機連結建議使用路由名稱「route.名稱」的格式自動產生，以避免繁複修改。
                所有路由名稱列表如右：{{ implode('、', $routes) }}。
            </p>
        </div>
        <form id="edit-unit" action="{{ route('menus.add', ['menu' => $current]) }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $current }}">
            <div class="block">
            <label for="mid" class="inline p-2">識別碼：</label>
            <input class="inline w-40 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="mid" autofocus required pattern="[a-z0-9]+" placeholder="請輸入英數半形">　　
            <label for="caption" class="inline p-2">選單名稱：</label>
            <input class="inline w-48 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="caption" required placeholder="請輸入中文">
            <p class="mt-2">
            <label for="kind" class="inline p-2">類　別：</label>
            <select id="kind" class="form-select w-24 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                name="kind" onchange="
                    var kind = this.value;
                    var myp = document.getElementById('desc');
                    var myc = document.getElementById('url');
                    if (kind == 'sub') {
                        myc.value = '#';
                        myp.classList.add('hidden');
                    } else {
                        myp.classList.remove('hidden');
                    }
                ">
                <option value="sub">次選單</option>
                <option selected value="item">超連結</option>
            </select>　　
            <span id="desc" class="inline p-2">
                <label for="url" class="inline p-2">URL：</label>
                <input class="inline w-64 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" id="url" name="url" required placeholder="請詳細閱讀上面的說明再填寫！">
            </span>
            </p>
            <p class="mt-2">
            <label for="weight" class="inline p-2">權　重：</label>
            <input class="inline w-16 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="weight" pattern="[0-9]+">
            </p>
            <div` class="block py-4 px-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    新增
                </button>
            </div>
            </div>
        </form>
    </div>
</div>
@endsection
