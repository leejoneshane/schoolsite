@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    學生課外社團
    @student
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.enroll') }}">
        <i class="fa-solid fa-pen-nib"></i>我要報名
    </a>
    @endstudent
    @if ($manager)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.kinds') }}">
        <i class="fa-solid fa-bookmark"></i>社團分類
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin') }}">
        <i class="fa-solid fa-people-roof"></i>社團管理
    </a>
    @endif
	@if ($cash_reporter)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.cash') }}">
        <i class="fa-solid fa-sack-dollar"></i>收費統計表
    </a>
	@endif
</div>
<ol class="ml-4 list-decimal">
    <li class="mb-4 leading-6 text-green-600">
	    歡迎您使用學生課外社團、暑期學藝活動班、課後照顧班【網路報名系統】，請仔細閱讀各社團之資訊，注意上課時段是否有衝突，如有衝突請於下方表格取消報名，謝謝您的愛用與合作！
	</li>
	<li class="mb-4 leading-6 text-green-600">
	    如果您想替自己的子女報名，請按照登入頁面的說明登入小朋友的帳號，即可報名！完成報名後，請按鍵盤上的［<span class="font-semibold">PrintScreen</span>］將畫面留存或列印出來，若事後報名資訊遺失，方能出示此憑據以維護自身權益。
	</li>
	<li class="mb-4 leading-6 text-green-600">
	    報名成功時將提供報名順位資訊！報名序大於開班人數時，則視為候補，報名截止後若員額出現空缺，會依序通知成功候補的同學。
	</li>
	<li class="mb-4 leading-6 text-green-600">
	    繳費規定：
        <ul class="ml-4 mb-4 list-disc">
		    <li class="leading-6 text-green-600">
		        學生課外社團、課後學藝活動：拿到三聯單後請盡速完成繳費，未於期限內完成繳費者，則視同放棄權利，名額將釋出給其他學童。
            </li>
		    <li class="leading-6 text-green-600">
		        課後照顧班：併入下學期三聯單收費。
            </li>
	    </ul>
	</li>
	<li class="mb-4 leading-6 text-green-600">
	    系統於工作人員確認錄取後，另行公告於穿堂，並依照公告之說明辦理繳費，進一步訊息請連繫承辦單位，電話 (02)23033555
	    <ul class="ml-4 mb-4 list-disc">
		    <li class="leading-6 text-green-600">
                學務處，分機 401
            </li>
		    <li class="leading-6 text-green-600">
		        教務處，分機 201
            </li>
	    </ul>
	</li>
	<li class="mb-4 leading-6 text-green-600">
	    退費規定：
	    <ul class="ml-4 mb-4 list-disc">
		    <li class="mb-4 leading-6 text-green-600">
		        學生課外社團：（開課後材料費不退費）
		        <ol class="ml-4 mb-4 list-decimal">
			        <li class="leading-6 text-green-600">
			            開課前申請退班者，需扣除必要之行政作業處理費用。
			        </li>
			        <li class="leading-6 text-green-600">
			            開課後至未逾上課總週數1/3，扣除必要之行政作業費用後，退還所繳學費之2/3。
			        </li>
			        <li class="leading-6 text-green-600">
			            開課後超過上課總週數1/3、未達2/3而申請退班者，退還所繳學費之1/3者，不予退費。
			        </li>
			        <li class="leading-6 text-green-600">
			            申請退班時已超過上課總週數之2/3者，不予退費。 
			        </li>
		        </ol>
		    </li>
		    <li class="mb-4 leading-6 text-green-600">
		        課後學藝班及暑期學藝活動班：（開課後材料費不退費，冷氣費不退費）
                <ol class="ml-4 mb-4 list-decimal">
                    <li class="leading-6 text-green-600">
                        開課確認後（非開始上課）至未逾上課總週數1/3，退還所繳學費之2/3者，不予退費。
                    </li>
			        <li class="leading-6 text-green-600">
			            開課後超過上課總週數1/3未達2/3，退還所繳學費之1/3者，不予退費。
                    </li>
                    <li class="leading-6 text-green-600">
			            申請退費時已超過上課總週數之2/3者，不予退費。 
                    </li>
		        </ol>
		    </li>
            <li class="mb-4 leading-6 text-green-600">
		        課後照顧班：
                <ol class="ml-4 mb-4 list-decimal">
                    <li class="leading-6 text-green-600">
						開學日前申請退班者，不收取費用。
                    </li>
				    <li class="leading-6 text-green-600">
                        開學當日至未逾上課總週數1/3，應繳總費用1/3。
                    </li>
                    <li class="leading-6 text-green-600">
				        已超過上課總週數1/3、未達2/3，退還所繳費用之1/3。
                    </li>
				    <li class="leading-6 text-green-600">
				        申請退班時已超過上課總週數之2/3者，不予退費。
				    </li>
			    </ol>
		    </li>
	    </ul>
	</li>
</ol>
@endsection
