@extends('layouts.game')

@section('content')
<div class="mt-40">
    @php
        $z = 0;
    @endphp
    @foreach ($classes as $pro)
    @php
        $img = $pro->images->count() > 0 ? $pro->images->random() : null;
    @endphp
    <div id="{{ $pro->id }}" class="z-{{$z}} inline-flex flex-col w-60 h-100 p-2 text-gray-500 bg-white bg-opacity-50 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-blue-500 hover:bg-opacity-50 hover:border-blue-500"
        onmouseover="show(this);" onmouseout="hide();" onclick="">
        <img src="{{ $img && $img->avaliable() ? $img->url() : '' }}" class="w-60" />
        <div class="w-60 text-black text-lg text-center font-semibold">{{ $pro->name }}</div>
    </div>
    @php 
        $z += 30;
    @endphp
    @endforeach
</div>
<div id="description" class="bg-white bg-opacity-50 rounded-lg m-10 px-3">
</div>
<script nonce="selfhost">
var messages = [ '',
    @foreach ($classes as $pro)
    '{{ $pro->description }}',
    @endforeach
];

function show(party) {
    const elem = document.getElementById('description');
    elem.innerHTML = messages[party.id];
}

function hide() {
    const elem = document.getElementById('description');
    elem.innerHTML = '';
}
</script>
@endsection
