@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    變更座位表版型
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seats') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<form id="edit-seats" action="{{ route('seats.change', [ 'id' => $seats->id ]) }}" method="POST">
    @csrf
    <div class="flex flex-row justify-center">
        <table class="p-3">
            <tr>
                <td>
                    <label for="classroome" class="inline">班級：</label>
                    <select id="classroom" name="classroom" class="inline rounded py-2 mr-6 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}"{{ ($cls->id == $seats->class_id) ? ' selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                    </select>
                    <label for="theme" class="inline">版型：</label>
                    <select id="theme" name="theme" class="inline rounded py-2 mr-6 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                        @foreach ($themes as $theme)
                            <option value="{{ $theme->id }}"{{ ($theme->id == $seats->theme_id) ? ' selected' : '' }}>{{ $theme->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        修改
                    </button>
                </td>
            </tr>
        </table>
    </div>
</form>
@endsection