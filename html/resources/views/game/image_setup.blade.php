@extends('layouts.game')

@section('content')
<div class="h-full flex">
<div class="w-1/2 h-full mt-80 flex">
    @foreach ($character->profession->images as $img)
    <div id="{{ $img->id }}" class="z-10 inline-flex flex-col w-20 h-20 p-2 text-gray-500 bg-white bg-opacity-50 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-blue-500 hover:bg-opacity-50 hover:border-blue-500"
         onmouseover="show({{$img->id}})" onclick="setup({{$img->id}});">
        <img src="{{ $img && $img->avaliable() ? $img->thumb_url() : '' }}" class="w-20 z-20" />
    </div>
    @endforeach
</div>
<div class="w-1/2 h-full inline-flex place-items-center">
    <img src="" id="big" class="w-auto h-auto" />
</div>
</div>
<form class="hidden" id="save" action="{{ route('game.image_setup', [ 'uuid' => $character->uuid ]) }}" method="POST">
    @csrf
    <input id="data" type="hidden" name="image_id" value="">
</form>
<script nonce="selfhost">
var messages = [];
@foreach ($character->profession->images as $img)
messages[{{ $img->id }}] = '{{ $img->url() }}';
@endforeach

function show(id) {
    const elem = document.getElementById('big');
    elem.src = messages[id];
}

function setup(id) {
    const myform = document.getElementById('save');
    const elem = document.getElementById('data');
    elem.value = id;
    myform.submit();
}
</script>
@endsection
