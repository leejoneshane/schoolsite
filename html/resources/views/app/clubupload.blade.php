@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    匯入課外社團
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        上傳檔案須為 xlsx（Excel 2010 以後版本），要匯入的資料必須位於第一個工作表。
        該工作表的第一列必須為標題列，資料請放置於第二列（含）以後，順序不拘，多餘的欄位不會匯入，有效的標題如下：<br>
        dep,name,short,grade,week,sdate,edate,stime,etime,teacher,place,cash,total,maxnum,memo,lunch,remove<br>
        欄位說明如下：<ul>
            <li>dep:負責單位，請輸入處室名稱。</li>
            <li>name:課外社團全名，請不要包含學年學期資訊，系統會自動將學生報名資訊依學年度分開管理。</li>
            <li>short:簡稱，五個中文字以內，必填欄位，請勿留白。</li>
            <li>grade:招生年級，使用六個字元的數字表示，例如：招生年級為2、3、4，請寫為 011100。</li>
            <li>week:每週上課日，使用五個字元的數字表示，例如：上課日為星期 一、四、五，請寫為 10011。若為 00000 表示由家長自選上課日。</li>
            <li>sdate:開始上課的日期，格式為 2022/09/01 或 2022-09-01。</li>
            <li>edate:結束上課的日期，格式同上。</li>
            <li>stime:每次上課的開始時間，請使用24時制，例如 18:20。</li>
            <li>etime:每次上課的結束時間，格式同上。</li>
            <li>teacher:教師的姓名，若超過一個人，中間請用空格分隔。</li>
            <li>place:上課地點，請簡要描述。</li>
            <li>cash:應繳交費用，無需費用請填 0 或留白。請輸入數字。</li> 
            <li>total:招生人數，填 0 或留白表示沒有人數限制。請輸入數字。</li>
            <li>maxnum:報名限制，若 maxnum 少於 total，將會保留招生名額，作為現場報名或其他安排。若 maxnum 大於 total 超出部分不會自動錄取，可視為候補。請輸入數字。</li>
            <li>memo:備註</li>
            <li>lunch:是否顯示午餐選項，要顯示請輸入 1，不顯示請輸入 0。（午餐選項包含：自理、葷食、素食。）</li>
            <li>remove:是否允許學生自由取消報名，不允許請輸入 0。</li>
        </ul>
    </p>
</div>
<form id="upload" action="{{ route('clubs.import') }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="kind" class="inline">匯入到哪個類別？</label>
        <select name="kind" class="inline w-48 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            @foreach ($kinds as $k)
            <option value="{{ $k->id }}"{{ ($kind == $k->id) ? ' selected' : '' }}>{{ $k->name }}</option>
        @endforeach
        </select>
    </div></p>
    <p><div class="p-3">
        <label for="file" class="inline">請選擇上傳檔案！</label>
        <input type="file" name="file" class="inline file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" required>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                匯入
            </button>
        </div>    
    </p>
</form>
@endsection
