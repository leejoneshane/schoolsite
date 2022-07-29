@extends('layouts.app')

@section('content')
<div class="m-5 bg-white relative flex flex-col gap-3 justify-center items-center">
    <div class="md:border md:border-gray-300 bg-white md:shadow-lg shadow rounded p-10">
        <div class="text-2xl font-bold leading-normal text-center pb-5" >註冊管理員帳號</div>

        <form method="POST" action="{{ route('register') }}">
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
                <label for="email" class="label block mb-2 text-sm font-medium text-gray-900">電子郵件</label>
                <input id="email" class="w-60 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="text" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                <span class="border-red-500 bg-red-100 border-t-2" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="relative mb-6">
                <label for="password" class="label block mb-2 text-sm font-medium text-gray-900">密碼</label>
                <input id="password" class="w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="password" name="password" required autofocus>
                @error('password')
                <span class="border-red-500 bg-red-100 border-t-2" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="relative mb-6">
                <label for="password-confirm" class="label block mb-2 text-sm font-medium text-gray-900">確認密碼</label>
                <input id="password-confirm" class="w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="password" name="password_confirmation" required autocomplete="new-password">
                @error('password-confirm')
                <span class="border-red-500 bg-red-100 border-t-2" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="space-y-9">
                <div class="text-sm flex justify-between items-center">
                    <button class="py-2 px-6 rounded text-white btn bg-blue-500 hover:bg-blue-600" type="submit">
                        下一步
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
