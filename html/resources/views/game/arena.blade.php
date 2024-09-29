@extends('layouts.game')

@section('content')
<div class="relative w-full h-full flex flex-col gap-10 justify-between">

</div>
<script nonce="selfhost">
    var character = '{{ $character->uuid }}';

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-arena');
</script>
@endsection
