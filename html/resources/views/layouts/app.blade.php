<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>
    @include('layouts.partials.header')
    @include('layouts.partials.content')
    @include('layouts.partials.footer')
</body>
</html>