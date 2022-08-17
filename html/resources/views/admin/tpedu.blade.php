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
                更新快取資料
            </div>
            <div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
                <p>
                    將從臺北市校園單一身分驗證服務取得校務行政資料！本同步作業無法同步密碼，新建帳號一律使用身分證字號後六碼做為密碼，除非必要請勿重設密碼，以避免造成教職員和學生無法使用自訂密碼登入。
                </p>
                <p>
                    由於單一身份驗證服務中的行政單位與職稱資料，並非同步自校務行政系統，所以需要大量編輯以符合校務行政系統內的組織架構！在學校組織架構未大量變更的情況下，建議不要重新同步行政單位與職稱，請從編輯界面自行增刪即可。由於教職員所屬行政單位與職稱來源自單一身分驗證服務中的錯誤資料，因此在同步教師後，您仍需要手動自行調整行政人員所屬單位和職稱，此步驟可以從教職員編輯介面進行調整。
                </p>
            </div>
            <form id="edit-unit" action="{{ route('sync') }}" method="POST">
                @csrf
                <label for="expire" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="expire" name="expire" value="yes" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">只同步超過{{ config('services.tpedu.expired_days') }}天的資料！</span>
                </label>
                <br>
                <label for="password" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="password" name="password" value="sync" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">密碼重設為身分證字號後六碼！</span>
                </label>
                <br>
                <label for="sync_units" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="sync_units" name="sync_units" value="yes" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">重新同步行政單位與職稱！</span>
                </label>
                <br>
                <label for="sync_classes" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="sync_classes" name="sync_classes" value="yes" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">重新同步班級資訊！</span>
                </label>
                <br>
                <label for="sync_subjects" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="sync_subjects" name="sync_subjects" value="yes" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">重新同步科目資訊！</span>
                </label>
                <br>
                <label for="remove" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="remove" name="leave" value="remove" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">移除已離職帳號（資料將保留，但標註為已刪除）！</span>
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
