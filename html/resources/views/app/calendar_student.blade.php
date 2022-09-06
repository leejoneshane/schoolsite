<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>國語實驗國民小學學生行事曆</title>
    @vite
</head>
<body>
    <div class="text-2xl font-bold leading-normal pb-5">
        學生行事曆
        <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('calendar').'?current='.$current }}">
            <i class="fa-solid fa-calendar-plus"></i>返回上一頁
        </a>
    </div>
    <table class="w-full text-sm text-left">
        <tr class="bg-gray-300 font-semibold text-lg">
            <th scope="col" class="p-2">月</th>
            <th scope="col" class="p-2">日</th>
            <th scope="col" class="p-2">星期</th>
            <th scope="col" class="p-2 text-justify">當日行事</th>
        </tr>
        @foreach ($events as $day => $event)
        <tr class="bg-white">
            <td class="p-2">{{ $month }}</td>
            <td class="p-2">{{ $day }}</td>
            <td class="p-2">{{ $event->wd }}</td>
            <td class="p-2">{{ $event->content }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
@endsection
