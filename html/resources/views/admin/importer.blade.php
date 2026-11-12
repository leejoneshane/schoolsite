@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    匯入校務行政
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5 p-4 rounded" role="alert">
    <p class="mb-2">
        請選擇要匯入的校務行政資料表類型，並將從校務行政系統匯出的 Excel 或 CSV 檔案（支援 <code>.xlsx</code>、<code>.xls</code>、<code>.csv</code> 格式）拖曳至下方上傳區塊或點擊選取檔案進行匯入。
    </p>
    <p class="text-sm text-gray-700 dark:text-gray-200 mb-3">
        新建帳號預設將使用身分證字號後六碼做為密碼。要匯入的資料必須位於第一個工作表，第一列為標題列，各資料表欄位格式如下：
    </p>

    <div id="format-teachers" class="overflow-x-auto bg-white dark:bg-gray-800 p-3 rounded border border-blue-300 dark:border-gray-600">
        <div class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-2">
            <i class="fa-solid fa-table mr-1"></i>全校教師資料表 標題列與範例格式：
        </div>
        <table class="w-full text-xs text-left border-collapse border border-gray-400 dark:border-gray-600 whitespace-nowrap">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">員工代碼</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">姓名</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">性別</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">身分證</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">出生日期</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">編制內否</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">教育程度</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">辦公室電話</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">行動電話</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">電子郵件</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">到職日期</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">離職否</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">離職日期</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">聘任方式</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">帳號類型</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">主要學校</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">外部排課教師代碼</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">教師卡卡號</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-gray-600 dark:text-gray-300">
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">1003</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">王小明</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">女</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">A223456789</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">071/12/16</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">1</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">碩士</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">02-23033555</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">0912345678</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">demo@tc.meps.tp.edu.tw</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">115/08/01</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">在職</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1"></td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">專任</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">教師</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">1</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1"></td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="format-students" class="hidden overflow-x-auto bg-white dark:bg-gray-800 p-3 rounded border border-blue-300 dark:border-gray-600">
        <div class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-2">
            <i class="fa-solid fa-table mr-1"></i>班級學生資料表：直接上傳校務行政系統匯出之學生名冊（72 欄標準格式，支援密碼鎖定之 .xlsx），系統將自動對應以下主要欄位：
        </div>
        <table class="w-full text-xs text-left border-collapse border border-gray-400 dark:border-gray-600 whitespace-nowrap">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">班級</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">班級代碼</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">座號</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">學號</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">身分證</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">姓名</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">性別</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">出生年月日</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">學生行動電話 / 家長1行動電話</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">通訊電話 / 家長1住家電話</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">通訊地址 / 戶籍地址</th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">學籍狀態</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-gray-600 dark:text-gray-300">
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">一年一班</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">101</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">01</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">115014</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">A133707953</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">吳少楷</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">1</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">20191004</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">0918061986</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">0223020186</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">臺北市中正區...53號</td>
                    <td class="border border-gray-400 dark:border-gray-600 px-2 py-1">在學</td>
                </tr>
            </tbody>
        </table>
        <p class="mt-2 text-xs text-gray-600 dark:text-gray-300">
            說明：若班級不存在將自動建立；地址優先取通訊地址（無則取戶籍地址）並將全形數字轉半形；「移除不在籍學生」僅作用於本次匯入檔案包含的班級範圍內。
        </p>
    </div>
</div>

@if (session()->has('import_logs') && is_array(session('import_logs')) && count(session('import_logs')) > 0)
<div class="w-full border-teal-500 bg-teal-50 dark:bg-gray-800 border-l-4 p-4 mb-5 rounded shadow-sm">
    <div class="font-bold text-teal-800 dark:text-teal-300 mb-2">
        <i class="fa-solid fa-list-check mr-2"></i>匯入處理紀錄：
    </div>
    <div class="max-h-60 overflow-y-auto text-sm text-gray-700 dark:text-gray-300 space-y-1">
        @foreach (session('import_logs') as $log)
            <div>{{ $log }}</div>
        @endforeach
    </div>
</div>
@endif

<form id="import-sms-form" action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-6">
        <label class="block text-base font-semibold text-gray-900 dark:text-gray-200 mb-3">
            1. 請選擇要匯入的資料表類型：
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
            <label for="type_teachers" class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-gray-600">
                <input type="radio" id="type_teachers" name="type" value="teachers" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" checked>
                <div class="ml-3">
                    <span class="block text-base font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fa-solid fa-chalkboard-user mr-1 text-blue-600 dark:text-blue-400"></i>全校教師資料表
                    </span>
                    <span class="block text-xs text-gray-500 dark:text-gray-300 mt-1">
                        匯入並更新全校教職員基本資料、登入帳號與單位職稱
                    </span>
                </div>
            </label>

            <label for="type_students" class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-gray-600">
                <input type="radio" id="type_students" name="type" value="students" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <div class="ml-3">
                    <span class="block text-base font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fa-solid fa-user-graduate mr-1 text-blue-600 dark:text-blue-400"></i>班級學生資料表
                    </span>
                    <span class="block text-xs text-gray-500 dark:text-gray-300 mt-1">
                        匯入並更新各班級學生名冊、學號、座號與基本資料
                    </span>
                </div>
            </label>
        </div>
    </div>

    <div class="mb-6 max-w-2xl space-y-2">
        <div>
            <label for="password" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="password" name="password" value="sync" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">將已有帳號的密碼重設為身分證字號後六碼！</span>
            </label>
        </div>
        <div>
            <label for="remove" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="remove" name="leave" value="remove" class="sr-only peer" checked>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">移除未在名冊內的已離職或轉出帳號（資料將保留，但標註為已刪除）！</span>
            </label>
        </div>
    </div>

    <div id="excel-password-group" class="hidden mb-6 max-w-2xl">
        <label for="excel-password" class="block text-base font-semibold text-gray-900 dark:text-gray-200 mb-2">
            Excel 檔案密碼（選填）：
        </label>
        <div class="relative flex items-center">
            <input type="password" id="excel-password" name="excel_password"
                aria-describedby="excel-password-help"
                autocorrect="off" autocapitalize="none" spellcheck="false"
                placeholder="若學生名冊 .xlsx 受密碼保護請輸入解鎖密碼"
                class="w-full rounded-lg px-3 py-2.5 pr-11 border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:placeholder-gray-400">
            <button type="button" id="toggle-excel-password"
                aria-pressed="false" aria-label="顯示密碼"
                class="absolute right-2.5 p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600">
                <i class="fa-solid fa-eye" aria-hidden="true"></i>
            </button>
        </div>
        <p id="excel-password-help" class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
            校務行政系統匯出之學生名冊（.xlsx）若有加密保護，請在此填寫解鎖密碼；未加密檔案或 CSV 檔請留空。
        </p>
    </div>

    <div class="mb-6 max-w-2xl">
        <label class="block text-base font-semibold text-gray-900 dark:text-gray-200 mb-3">
            2. 上傳 Excel / CSV 檔案：
        </label>
        <div id="dropzone-area" class="flex flex-col items-center justify-center w-full h-64 border-2 border-blue-400 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-blue-50 dark:border-gray-500 dark:hover:border-blue-400 transition-colors relative">
            <input id="excel-file" name="file" type="file" accept=".xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required />
            <div id="upload-prompt" class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4 pointer-events-none">
                <i class="fa-solid fa-cloud-arrow-up text-5xl text-blue-500 dark:text-blue-400 mb-4"></i>
                <p class="mb-2 text-base text-gray-700 dark:text-gray-200">
                    <span class="font-semibold text-blue-600 dark:text-blue-400">點擊選取檔案</span> 或將檔案拖曳至此處
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    支援格式：Excel (.xlsx, .xls) 或 CSV (.csv)
                </p>
            </div>
            <div id="file-info" class="hidden flex-col items-center justify-center pt-5 pb-6 text-center px-4 pointer-events-none">
                <i class="fa-solid fa-file-excel text-5xl text-green-600 dark:text-green-400 mb-3"></i>
                <p id="file-name" class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1"></p>
                <p id="file-size" class="text-xs text-gray-500 dark:text-gray-400 mb-3"></p>
                <span class="text-xs px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                    點擊或重新拖曳即可更換檔案
                </span>
            </div>
        </div>
    </div>

    <div class="py-2">
        <button type="submit" id="submit-btn" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-6 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <i class="fa-solid fa-file-import mr-2"></i>開始匯入資料
        </button>
    </div>
</form>

<script nonce="selfhost">
document.addEventListener('DOMContentLoaded', function () {
    const typeTeachers = document.getElementById('type_teachers');
    const typeStudents = document.getElementById('type_students');
    const formatTeachers = document.getElementById('format-teachers');
    const formatStudents = document.getElementById('format-students');
    const excelPasswordGroup = document.getElementById('excel-password-group');
    const excelPasswordInput = document.getElementById('excel-password');
    const toggleExcelPassword = document.getElementById('toggle-excel-password');

    function updateFormatTable() {
        if (typeStudents.checked) {
            formatTeachers.classList.add('hidden');
            formatStudents.classList.remove('hidden');
            excelPasswordGroup.classList.remove('hidden');
        } else {
            formatTeachers.classList.remove('hidden');
            formatStudents.classList.add('hidden');
            excelPasswordGroup.classList.add('hidden');
        }
    }

    typeTeachers.addEventListener('change', updateFormatTable);
    typeStudents.addEventListener('change', updateFormatTable);
    updateFormatTable();

    toggleExcelPassword.addEventListener('click', function () {
        const isPassword = excelPasswordInput.type === 'password';
        excelPasswordInput.type = isPassword ? 'text' : 'password';
        this.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        this.setAttribute('aria-label', isPassword ? '隱藏密碼' : '顯示密碼');
        const icon = this.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        }
    });

    const dropzoneArea = document.getElementById('dropzone-area');
    const fileInput = document.getElementById('excel-file');
    const uploadPrompt = document.getElementById('upload-prompt');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function updateFileDisplay(file) {
        if (file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatBytes(file.size);
            uploadPrompt.classList.add('hidden');
            fileInfo.classList.remove('hidden');
            fileInfo.classList.add('flex');
            dropzoneArea.classList.remove('border-blue-400');
            dropzoneArea.classList.add('border-green-500', 'bg-green-50', 'dark:bg-gray-800');
        } else {
            uploadPrompt.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            fileInfo.classList.remove('flex');
            dropzoneArea.classList.add('border-blue-400');
            dropzoneArea.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-gray-800');
        }
    }

    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            updateFileDisplay(this.files[0]);
        } else {
            updateFileDisplay(null);
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneArea.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneArea.classList.add('border-blue-600', 'bg-blue-100', 'dark:bg-gray-600');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneArea.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneArea.classList.remove('border-blue-600', 'bg-blue-100', 'dark:bg-gray-600');
        }, false);
    });

    dropzoneArea.addEventListener('drop', function (e) {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
            fileInput.files = dt.files;
            updateFileDisplay(dt.files[0]);
        }
    }, false);
});
</script>
@endsection
