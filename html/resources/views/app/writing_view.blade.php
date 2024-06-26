@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    作品欣賞：{{ $context->title }}
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ $referer }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        寫作須知：使用中文直式稿紙，右邊第一行必須輸入標題，作者會自動帶入請不要輸入，請使用滑鼠控制游標，按「ctrl + s」儲存，按「ctrl + w」儲存並關閉！
    </p>
</div>
@php
    $words = 21 - mb_strlen($context->title) - mb_strlen($context->author);
@endphp
<div id="sheet" class="p-0" style="
    width: 1792px;
    height: 810px;
    font-family: 'cwTeXKai', 'cwTeXFangSong', '標楷體';
    font-variant-east-asian: traditional;
    east-asian-width-values: full-width;
    padding-top: 0.25rem;
    font-size: 1.5rem;
    line-height: 2rem;
    letter-spacing: 0.535rem;
    ime-mode: active;
    writing-mode:vertical-rl;
    -webkit-writing-mode: vertical-rl;
    word-break: break-all;
    white-space: pre-wrap;
    overflow-x: scroll;
">　　　　{{ $context->title }}@for ($i = 0; $i < $words; $i++)　@endfor{{ $context->author }}<br>{{ str_replace(' ', '', $context->words) }}</div>
<script nonce="selfhost">
    var font = 32;
    var c = document.createElement('canvas');
    c.width = 1792;
    c.height = 810;
    var col = Math.trunc(1792 / font);
    var ctx = c.getContext("2d");
    ctx.beginPath();
    for (var i=0; i<=25; i++) {
        ctx.moveTo(0, i * font);
        ctx.lineTo(1792, i * font);
    }
    for (var i=0; i<=col; i++) {
        ctx.moveTo(i * font, 0);
        ctx.lineTo(i * font, 800);
    }
    ctx.strokeStyle = '#BBF7D0';
    ctx.stroke();
    var t = document.getElementById('sheet');
    t.style.background = 'url(' + c.toDataURL() + ')';
</script>
@endsection
