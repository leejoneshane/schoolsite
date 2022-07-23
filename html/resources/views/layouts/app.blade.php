<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>
    @include('layouts.partials.header')
    <main class="w-full`">
        @yield('content')
    </main>
    @include('layouts.partials.footer')
</body>
</html>