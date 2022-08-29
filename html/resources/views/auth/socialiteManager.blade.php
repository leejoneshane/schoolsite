@extends('layouts.main')

@section('content')
<div class="m-5 w-full items-center">
    <div class="text-2xl font-bold leading-normal text-center pb-5">社群帳號綁定</div>
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
    <div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
        <p>社群帳號綁定說明：</p>
        <p>
            <ul class="list-decimal list-inside">
                <li class="list-item">綁定社群帳號前，應先刪除瀏覽器快取資料，或使用「無痕視窗」、「私密瀏覽」...等模式，以避免綁定其他人的帳號。</li>
                <li class="list-item">不應該綁定父母或朋友的帳號，以免身分遭到冒用。</li>
                <li class="list-item">不應該綁定非法申請的社群帳號（未滿 13 歲無法申請之帳號），以免觸犯法律規定。</li>
                <li class="list-item">每種社群平台只能綁定一個帳號，若要變更已經綁定的社群帳號，請先解除綁定後再重新設定！</li>
            </ul>
        </p>  
    </div>
    <div class="h-full grid grid-cols-4 grid-flow-col gap-3">
    @if ($google)
    <div class="col-span-1">
        Google 帳號：{{ $google->userId }}
        <button type="button" class="p-2 rounded text-white btn bg-red-500 hover:bg-red-600"
                 onclick="$('#socialite').val('google');
                         $('#userid').val('{{ $google->userId }}');
                         $('#form').submit();">解除</button>
    </div>
    @else
    <div class="col-span-1">
        Google 帳號：
        <a href="/login/google" class="p-2 rounded text-white btn bg-blue-500 hover:bg-blue-600">綁定</a>
    </div>
    @endif

    @if ($facebook)
    <div class="col-span-1">
        Facebook 帳號：{{ $facebook->userId }}
        <button type="button" class="p-2 rounded text-white btn bg-red-500 hover:bg-red-600"
                 onclick="$('#socialite').val('facebook');
                         $('#userid').val('{{ $facebook->userId }}');
                         $('#form').submit();">解除</a>
    </div>
    @else
    <div class="col-span-1">
        Facebook 帳號：
        <a href="/login/facebook" class="p-2 rounded text-white btn bg-blue-500 hover:bg-blue-600">綁定</a>
    </div>
    @endif
                
    @if ($yahoo)
    <div class="col-md-8">
        Yahoo 帳號：{{ $yahoo->userId }}
        <button type="button" class="py-2 px-6 rounded text-white btn bg-red-500 hover:bg-red-600"
                 onclick="$('#socialite').val('yahoo');
                         $('#userid').val('{{ $yahoo->userId }}');
                         $('#form').submit();">解除</button>
    </div>
    @else
    <div class="col-md-8">
        Yahoo 帳號：
        <a href="/login/yahoo" class="py-2 px-6 rounded text-white btn bg-blue-500 hover:bg-blue-600">綁定</a>
    </div>
    @endif

    @if ($line)
    <div class="col-md-8">
        Line 帳號：{{ $line->userId }}
        <button type="button" class="py-2 px-6 rounded text-white btn bg-red-500 hover:bg-red-600"
                 onclick="$('#socialite').val('line');
                         $('#userid').val('{{ $line->userId }}');
                         $('#form').submit();">解除</button>
    </div>
    @else
    <div class="col-md-8">
        Line 帳號：
        <a href="/login/line" class="py-2 px-6 rounded text-white btn bg-blue-500 hover:bg-blue-600">綁定</a>
    </div>
    @endif

                <form class="hidden" id="form" action="{{ route('social.remove') }}" method="POST">
                @csrf
                <input type="hidden" id='socialite' name='socialite' value="">
                <input type="hidden" id='userid' name='userid' value="">
                </form>
    </div>
</div>
@endsection