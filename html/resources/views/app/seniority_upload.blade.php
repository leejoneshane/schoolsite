@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    匯入教職員年資
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seniority') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        上傳檔案須為 xlsx（Excel 2010 以後版本），要匯入的資料必須位於第一個工作表。
        該工作表的前兩列為標題列，格式如下：
        <div class="p-2"><table class="w-2/3 table-auto border border-collapse border-black">
            <thead>
            <tr>
                <th colspan="10" class="border border-black text-center">臺北市國語實驗國民小學{{ $current }}學年度教師教學年資統計初稿  統計至{{ date('Y.m.d') }}</th>
            </tr>
            <tr>
                <th class="border border-black text-center">
                    唯一編號
                </th>
                <th class="border border-black text-center">
                    職別
                </th>
                <th class="border border-black text-center">
                    姓名
                </th>
                <th class="border border-black text-center">
                    在校年
                </th>
                <th class="border border-black text-center">
                    在校月
                </th>
                <th class="border border-black text-center">
                    在校積分
                </th>
                <th class="border border-black text-center">
                    校外年
                </th>
                <th class="border border-black text-center">
                    校外月
                </th>
                <th class="border border-black text-center">
                    校外積分
                </th>
                <th class="border border-black text-center">
                    備註
                </th>
            </tr>
            </thead>
        </table></div>
    </p>
</div>
<form id="upload" action="{{ route('seniority.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="p-3">
        <span class="sr-only">請選擇上傳檔案！</span>
        <input type="file" name="excel" accept=".xls,.xlsx,.pdf" class="block text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" required>
    </div>
    <div class="p-3">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            匯入
        </button>
    </div>
</form>
@endsection
