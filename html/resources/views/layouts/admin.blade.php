<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="h-screen">
    @include('layouts.partials.header')
    @include('layouts.partials.nav')
    <div class="min-h-full grid grid-cols-9 grid-flow-col gap-3 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        @include('layouts.partials.sidebar')
        @include('layouts.partials.content')
    </div>
    @include('layouts.partials.footer')
</body>
</html>