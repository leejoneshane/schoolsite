<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>
    @include('layouts.partials.header')
    <main class="bg-white dark:bg-gray-700 text-black dark:text-gray-200">
    @include('layouts.partials.content')
    </main>
    @include('layouts.partials.footer')
</body>
</html>