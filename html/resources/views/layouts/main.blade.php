<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="h-screen">
    @include('layouts.partials.header')
    @include('layouts.partials.nav')
    <div class="grid grid-cols-12 grid-flow-col gap-3">
        @include('layouts.partials.aside')
        <main class="mb-auto">
        @yield('content')
        </main>
    </div>
    @include('layouts.partials.footer')
</body>
</html>