<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>國語實驗國民小學學生課外社團家長通知</title>
    @vite
</head>
<body>
    <div class="text-2xl font-bold leading-normal pb-5">
        親愛的家長您好：
    </div>
    <div class="leading-normal pb-5">
            貴子弟（{{ $student->class->name }}{{ $student->seat }}號{{ $student->name }}），因報名參加{{ $club->studytime }}上課的{{ $club->name }}已獲錄取，以下注意事項提醒您：
    </div>
    <div class="m-5 border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2" role="alert">
        {{ $message }}
    </div>
</body>
</html>