@extends('layouts.app')

@section('content')
<div class="m-5 bg-white relative flex flex-col gap-3 justify-center items-center">
    <div class="md:border md:border-gray-300 bg-white md:shadow-lg shadow rounded p-10">
        <div class="text-2xl font-bold leading-normal text-center pb-5" >驗證您的電子郵件信箱</div>

        @if (session('resent'))
        <div class="border-green-500 bg-green-100 border-t-2" role="alert">
            新的驗證連結已經寄送到您的電子郵件信箱，請收取信件然後點擊信件中的驗證按鈕！
        </div>
        @endif

        <div class="block mb-2 text-sm font-medium text-gray-900">
            在進行下一步驟之前，請先驗證您的電子郵件信箱是否能正常收發郵件，如果您尚未收到驗證信，請檢查信箱是否正確，或者
            <form class="inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button class="inline py-2 px-3 rounded text-white btn bg-blue-500 hover:bg-blue-600" type="submit">
                    重送寄送驗證信！
                </button>.
            </form>
        </div>
    </div>
</div>
@endsection
