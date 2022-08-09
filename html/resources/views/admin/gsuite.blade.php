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
        <div class="h-full grid">
        <div class="col-span-1">
            <div class="text-2xl font-bold leading-normal pb-5">
                同步到 AD
            </div>
            <div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
                <p>
                    進行帳號同步到 G Suite 時，會花費較久的時間，因此同步作業將會在背景執行，並於工作完成後通知您！
                </p>
                <p>
                    同步程式無法同步密碼，程序運作流程如下：
                    <ol class="list-decimal list-inside">
                        <li>以單一身分驗證服務的自訂帳號搜尋 G Suite，帳號已存在者使用現有帳號，如果搜尋不到則自動幫您建立帳號。並且把學生學號新增為郵箱別名。</li>
                        <li>搜尋 G Suite 群組的說明(description)欄位是否與資料庫裡的所屬部門名稱相同，若相同則使用該群組，如果找不到則自動幫您建立群組。（如果您已經有一個匹配的 G Suite 群組，請在說明欄輸入部門名稱，以便讓程式可以正確辨識）</li>
                        <li>檢查使用者是否已經在群組裡，若否則將使用者加入。</li>
                        <li>將使用者退出其它群組。</li>
                    </ol>
                </p>
            </div>
            <form id="edit-unit" action="{{ route('syncGsuite') }}" method="POST">
                @csrf
                <label for="password" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="password" name="password" value="sync" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">密碼重設為身分證字號後六碼！</span>
                </label>
                <p class="py-4 px-6">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        開始進行同步
                    </button>
                </p>    
            </form>
        </div>
    </div>
</div>
@endsection
