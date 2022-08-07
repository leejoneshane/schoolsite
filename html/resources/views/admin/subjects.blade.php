@extends('layouts.admin')

@section('content')
<div class="relative m-5">
    <div class="p-10">
        @if (session('error'))
        <div class="border border-red-500 bg-red-100 border-b-2" role="alert">
            {{ session('error') }}
        </div>
        @endif
        @if (session('success'))
        <div class="border border-green-500 bg-green-100 border-b-2" role="alert">
            {{ session('success') }}
        </div>
        @endif
        <div class="text-2xl font-bold leading-normal pb-5">學習科目</div>
        <form id="edit-subject" action="{{ route('subjects') }}" method="POST">
            @csrf
            <div id="subject_list">
            <table class="w-full text-sm text-left">
                @foreach ($subjects as $s)
                <tr>
                    <td class="p-2">{{ $s->id }}</td>
                    <td class="p-2">
                        <input class="rounded px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                            type="text" name="subjects[{{ $s->id }}]" value="{{ $s->name }}">
                    </td>
                </tr>
                @endforeach
            </table>
            </div>
            <div class="py-2 px-6 mb-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">修改</button>
            </div>
        </form>
    </div>
</div>
@endsection
