<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>
    @include('layouts.partials.gameheader')
    @include('layouts.partials.gamenav')
    <div class="min-h-screen w-full flex bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        @include('layouts.partials.gamemenu')
        @include('layouts.partials.main')
    </div>
    @include('layouts.partials.footer')
</body>
</html>