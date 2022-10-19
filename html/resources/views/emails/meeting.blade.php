<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>網路朝會報告事項</title>
    @vite
</head>
<body>
    <div class="text-2xl font-bold leading-normal pb-5">
        國語實驗國民小學{{ date('Y-m-d') }}網路朝會報告事項
        <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('meeting') }}">
            <i class="fa-solid fa-calendar-check"></i>線上版
        </a>
    </div>
    <table class="w-full text-sm text-left">
    @foreach ($meets as $meet)
        <tr class="text-white bg-blue-700 font-semibold text-lg">
            <th class="p-2 w-8">{{ $meet->role . $meet->reporter }}：{{ $meet->created_at . $meet->unit->name }}業務報告</th>
        </tr>
        <tr class="bg-white">
            <td class="p-2">{{ $meet->words }}</td>
        </tr>
    @endforeach
    </table>
</body>
</html>
