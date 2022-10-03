<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite
</head>
<body>
    <div class="flex min-h-screen p-5 bg-blue-100 min-w-screen">
        <div class="max-w-xl p-8 justify-items-center text-gray-800 bg-white shadow-xl lg:max-w-3xl rounded-3xl lg:p-12">
            <img src="{{ asset('images/logo.jpg') }}" class="m-5">
            <h3 class="text-2xl">敬愛的家長{{ $enroll->parent }}，您好：</h3>
            <div class="mt-4">
                貴子弟（{{ $student->classroom->name }}{{ $student->seat }}號{{ $student->realname }}），因報名參加{{ $club->studytime }}上課的{{ $club->name }}，以下注意事項提醒您：
            </div>
            <div class="mt-4">
                <div class="m-5 border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2" role="alert">
                    {{ $info }}
                </div>
                <p class="mt-4 text-sm text-gray-500">© 國語實小E化服務網. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>