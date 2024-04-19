<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>國語實驗國民小學學生行事曆</title>
    @vite(['resources/css/tailwind.css','resources/scripts/main.ts'])
</head>
<body>
    <div class="text-2xl font-bold leading-normal pb-5">
        國語實驗國民小學學生行事曆
        <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar.student') }}">
            <i class="fa-solid fa-calendar-check"></i>線上版
        </a>
    </div>
    <table class="w-full text-sm text-left">
        <tr class="bg-gray-300 font-semibold text-lg">
            <th colspan="4" class="p-2 text-center">民國{{ $year }}年{{ $month }}月</th>
        </tr>
        <tr class="bg-gray-300 font-semibold text-lg">
            <th scope="col" class="p-2 w-8">月</th>
            <th scope="col" class="p-2 w-8">日</th>
            <th scope="col" class="p-2 w-8">星期</th>
            <th scope="col" class="p-2 text-justify">當日行事</th>
        </tr>
        @foreach ($events as $event)
        <tr class="bg-white">
            <td class="p-2 text-center">{{ $event->month }}</td>
            <td class="p-2 text-center">{{ $event->day }}</td>
            <td class="p-2 text-center">{{ $event->weekday }}</td>
            <td class="p-2">{{ $event->content }}</td>
        </tr>
        @endforeach
    </table>
    <p class="mt-4 text-sm text-gray-500">© {{ config('app.name') }}. All rights reserved.</p>
</body>
</html>
