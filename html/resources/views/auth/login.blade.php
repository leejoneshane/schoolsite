@extends('layouts.app')

@section('content')
<div class="m-5 bg-white relative flex flex-col gap-3 justify-center items-center">
    <div class="md:border md:border-gray-300 bg-white md:shadow-lg shadow rounded p-10">
        <div class="text-2xl font-bold leading-normal text-center pb-5" >登入</div>
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
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="relative mb-6">
                <label for="name" class="label block mb-2 text-sm font-medium text-gray-900">帳號</label>
                <input id="name" class="w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="text" name="name" required autofocus>
                @error('username')
                <span class="border-red-500 bg-red-100 border-t-2" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="relative mb-6">
                <label for="password" class="label block mb-2 text-sm font-medium text-gray-900">密碼</label>
                <input id="password" class="w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none" type="password" name="password" required autofocus>
                @error('password')
                <span class="border-red-500 bg-red-100 border-t-2" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="space-y-9">
                <div class="text-sm flex justify-between items-center">
                    <button class="py-2 px-6 rounded text-white btn bg-blue-500 hover:bg-blue-600" type="submit">下一步</button>
                    @if (Route::has('password.request'))
                    <a class="py-2 px-6 rounded text-blue-300 btn bg-white hover:text-blue-600" href="{{ route('password.request') }}">忘記密碼？</a>
                    @endif
                </div>
            </div>

            <div class="relative my-6 items-center">
                <div class="social-login-buttons">
                    <a href="/login/google"><i class="fab fa-2x fa-google text-red-800" title="使用 Google 登入"></i></a>　
                    <a href="/login/facebook"><i class="fab fa-2x fa-facebook text-blue-700" title="使用 Facebook 登入"></i></a>　
                    <a href="/login/yahoo"><i class="fab fa-2x fa-yahoo text-purple-800" title="使用 Yahoo 登入"></i></a>　
                    <a href="/login/line"><i class="fab fa-2x fa-line text-green-700" title="使用 Line 登入"></i></a>　　
                    <a href="/login/tpedu"><img src="{{ asset('images/tpedusso_240.png') }}" class="inline h-14" title="使用臺北市教育局單一身份驗證登入"></a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
