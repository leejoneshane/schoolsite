@extends('layouts.game')

@section('content')
<div class="w-full h-screen flex flex-col justify-between">
    <div id="our_side" class="absolute w-1/3 bottom-12 inline-flex content-end">
    </div>
    <div id="enemy_side" class="absolute w-1/3 bottom-12 inline-flex content-end">
    </div>
</div>
<script nonce="selfhost">
    var character = '{{ $character->uuid }}';
    var members = [];

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-arena');
    var our_side = document.getElementById('our_side');
    var enemy_side = document.getElementById('enemy_side');
    window.onload = who;
    setInterval(who, 3000);

    function who() {
        window.axios.post('{{ route('game.in_arena') }}', {
            uuid: character,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( response => {
            members = [];
            for (var k in response.data.characters) {
                var member = response.data.characters[k];
                members[k] = member;
            }
            if (members.length > 0) {
                our_side.innerHTML = '';
                var z = 10;
                members.forEach( member => {
                    var image = document.createElement('img');
                    image.classList.add('w-1/2', 'z-' + z);
                    if (member.url) {
                        image.setAttribute('title', member.name);
                        image.src = member.url;
                    } else {
                        image.src = '{{ asset('images/game/blank.png') }}';
                    }
                    our_side.appendChild(image);
                    z += 10;
                });
            }
        });
    }
</script>
@endsection
