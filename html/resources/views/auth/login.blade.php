@extends('layouts.app')

@section('content')
<div class="m-5 bg-white relative flex flex-col gap-3 justify-center items-center">
    <div class="md:border md:border-gray-300 bg-white md:shadow-lg shadow-none rounded p-10">
        <div class="flex flex-col items-center p-5">
            <span class="text-2xl font-semi-bold leading-normal" >登入</span>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="relative mb-6">
                <label for="username" class="label block mb-2 text-sm font-medium text-gray-900">帳號</label>
                <input id="username" class="w-full rounded px-3 border border-gray-300 pt-5 pb-2 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="text" name="username" required autofocus>
                @error('username')
                <span class="border-red-500 bg-red-100 border-t-2" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="relative mb-6">
                <label for="password" class="label block mb-2 text-sm font-medium text-gray-900">密碼</label>
                <input id="password" class="w-full rounded px-3 border border-gray-300 pt-5 pb-2 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="password" name="password" required autofocus>
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
        </form>
    </div>
</div>
@endsection
