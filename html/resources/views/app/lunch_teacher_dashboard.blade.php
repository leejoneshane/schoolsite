@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    教師午餐管理
</div>

<div class="flex flex-col gap-6 md:flex-row justify-center items-stretch mt-10">
    <!-- Manage Student Lunch -->
    <a href="{{ route('lunch.manage') }}" class="block w-full md:w-1/3 p-6 bg-white rounded-lg border border-gray-200 shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 text-center transition">
        <div class="text-6xl text-blue-600 mb-4">
            <i class="fa-solid fa-users-viewfinder"></i>
        </div>
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">管理學生午餐</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">
            如果您是導師或午餐秘書，請點選此處管理班級學生的午餐調查狀況。
        </p>
    </a>

    <!-- Teacher's Lunch -->
    <a href="{{ route('lunch.teacher.edit') }}" class="block w-full md:w-1/3 p-6 bg-white rounded-lg border border-gray-200 shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 text-center transition">
        <div class="text-6xl text-green-600 mb-4">
            <i class="fa-solid fa-utensils"></i>
        </div>
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">登記個人用餐</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">
            登記您個人的午餐用餐地點、時間以及特殊飲食需求。
        </p>
    </a>
</div>
@endsection
