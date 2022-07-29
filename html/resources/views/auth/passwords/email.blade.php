@extends('layouts.app')

@section('content')
<div class="m-5 bg-white relative flex flex-col gap-3 justify-center items-center">
    <div class="md:border md:border-gray-300 bg-white md:shadow-lg shadow rounded p-10">
        <div class="text-2xl font-bold leading-normal text-center pb-5" >重設密碼</div>

        @if (session('status'))
        <div class="border-green-500 bg-green-100 border-t-2" role="alert">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="relative mb-6">
                <label for="email" class="label block mb-2 text-sm font-medium text-gray-900">電子郵件地址</label>
                <input id="email" class="w-60 rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none input active:outline-none" type="text" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                <span class="border-red-500 bg-red-100 border-t-2" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="space-y-9">
                <div class="text-sm flex justify-between items-center">
                    <button class="py-2 px-6 rounded text-white btn bg-blue-500 hover:bg-blue-600" type="submit">
                        傳送密碼重設連結！
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
