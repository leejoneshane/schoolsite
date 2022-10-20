@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    網路朝會—編輯業務報告
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('meeting') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<script src="https://cdn.ckeditor.com/ckeditor5/35.2.1/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/35.2.1/classic/translations/zh.js"></script>
<div class="p-2 w-full text-white bg-blue-700 font-semibold text-lg">
    {{ $meet->role . $meet->reporter }}：{{ date('Y-m-d') . $meet->unit->name }}業務報告
</div>
<form action="{{ route('meeting.edit', ['id' => $meet->id]) }}" method="POST">
    <p><div class="p-3">
        <label class="inline text-sm">截止日期：</label>
        <input class="inline w-36 rounded px-2 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
            type="date" name="enddate" value="{{ $meet->expired_at }}">
        <span class="pl-6"></span>
        <label for="open" class="inline-flex relative items-center align-middle cursor-pointer">
            <input type="checkbox" id="open" name="open" value="yes" class="sr-only peer"{{ ($meet->inside) ? '' : ' checked'}}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">通知訂閱者！</span>
        </label>
        <label for="switch" class="inline-flex relative items-center align-middle cursor-pointer">
            <input type="checkbox" id="switch" name="switch" value="yes" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">將張貼者變更為您！</span>
        </label>
    </div></p>
    <p><div class="p-3">
        <textarea id="editor" name="words" class="w-full">{{ $meet->words }}</textarea>
    </div></p>
    <p>
        <div class="p-3">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                修改
            </button>
        </div>
    </p>
</form>
<script>
    ClassicEditor
        .create( document.querySelector( '#editor' ), {
            language: 'zh'
        } )
        .catch( error => {
            console.error( error );
        } );
</script>
@endsection
