@extends('layouts.app')

@section('content')
<div class="m-5 bg-white relative flex flex-col gap-3 justify-center items-center">
    <div class="md:border md:border-gray-300 bg-white md:shadow-lg shadow rounded p-10">
        <div class="text-2xl font-bold leading-normal text-center pb-5" >更新密碼</div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="relative mb-6">
                <label for="password" class="label block mb-2 text-sm font-medium text-gray-900">新密碼</label>
                <input id="password" class="w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="password" name="password" required autocomplete="new-password">
                @error('password')
                <span class="border-red-500 bg-red-100 border-t-2" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="relative mb-6">
                <label for="password-confirm" class="label block mb-2 text-sm font-medium text-gray-900">確認密碼</label>
                <input id="password-confirm" class="w-full rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="password" name="password_confirmation" required autocomplete="new-password">
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
