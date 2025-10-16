@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯預約紀錄
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('venue.reserve', ['id' => $reserve->venue->id, 'date' => substr($reserve->reserved_at, 0, 10)]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="add-club" action="{{ route('venue.reserve.update') }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $reserve->id }}">
    <p><div class="p-3">
        <label class="inline">名稱：{{ $reserve->venue->name }}</label>
    </div></p>
    <p><div class="p-3">
        <label class="inline">借用須知：{{ $reserve->venue->description }}</label>
    </div></p>
    <p><div class="p-3">
        <label class="inline">預約者：{{ $reserve->subscriber->realname }}</label>
    </div></p>
    <p><div class="p-3">
        <label class="inline">預約日期：{{ substr($reserve->reserved_at, 0, 10) }}</label>
    </div></p>
    <p><div class="p-3">
        <label class="inline">預約開始節次：{{ $session_name }}</label>
    </div></p>
    <p><div class="p-3">
        <label class="inline">共預約幾節？</label>
        <select name="length">
            @for ($i=1; $i<=$max; $i++)
            <option value="{{ $i }}"{{ ($i == $reserve->length) ? ' selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
    </div></p>
    <p><div class="p-3">
        <label class="inline">預約原因（用途）：</label>
        <textarea name="reason" rows="4" class="inline p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >{{ $reserve->reason }}</textarea>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" name="act" value="edit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
            <button type="submit" name="act" value="cancel" class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                取消預約
            </button>
        </div>
    </p>
</form>
@endsection
