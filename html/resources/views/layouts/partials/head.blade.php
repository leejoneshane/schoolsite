<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Laravel') }}</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100;300;400;500;700;900&family=Noto+Serif+TC:wght@200;300;400;500;600;700;900&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/cwtexfangsong.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/cwtexkai.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="stylesheet" href="/css/ckeditor.css">

<!-- Scripts -->
<script nonce="flowbite" src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
@vite(['resources/css/tailwind.css','resources/scripts/main.ts'])