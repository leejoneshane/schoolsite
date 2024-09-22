@extends('layouts.game')

@section('content')
<div class="flex flex-col">
    <div class="w-full">
        <div id="text" class="leading-none text-white text-[30rem]"></div>
        <div class="volume-group w-full h-12 bg-black flex items-center gap-4 p-10">
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
            <div class="volume-cell w-4 h-8 bg-gray-300"></div>
        </div>
    </div>
    <div class="w-full p-2 bg-blue-100 bg-opacity-50">
        <p>靜謐山谷：在時間內保持安靜，悄悄通過山谷，完成任務獲得經驗值。如果在時間停止前發出太大的聲音，將給予懲罰。</p>
    </div>
    <div class="flex flex-row">
        <div class="w-1/3 flex flex-col bg-blue-100 bg-opacity-50">
            <div class="ml-5 p-2 text-white rounded-lg drop-shadow-lg">
                <span>獎勵設定：</span>
                XP:<input type="number" id="xp" name="xp" value="50" class="inline w-16 bg-transparent border p-0"> 
                GP:<input type="number" id="gp" name="gp" value="50" class="inline w-16 bg-transparent border p-0"> 
                <select id="item" name="item" class="ms-1 inline w-24 bg-transparent border p-0">
                <option value=""></option>
                @foreach ($items as $i)
                <option value="{{ $i->id }}">{{ $i->name }}</option>
                @endforeach
                </select>
            </div>
            <div class="ml-5 p-2 text-white rounded-lg drop-shadow-lg">
                <span>懲罰設定：</span>
                HP:<input type="number" id="hp" name="hp" value="10" class="inline w-16 bg-transparent border p-0"> 
                MP:<input type="number" id="mp" name="mp" value="0" class="inline w-16 bg-transparent border p-0"> 
            </div>
        </div>
        <div class="w-1/4 bg-blue-100 bg-opacity-50 flex flex-col">
            <div class="text-white rounded-lg drop-shadow-lg">
                計時<input type="number" id="timeout" min="1" max="30" value="5" class="p-0 bg-transparent">分鐘，
                不超過<input type="number" id="volume" min="1" max="100" value="60" class="p-0 bg-transparent">分貝
            </div>
            <div class="inline-flex">
                <button id="start" class="ml-6 bg-green-300 hover:bg-green-500 text-white font-bold py-2 px-4 rounded-full" onclick="start();">
                    <i class="fa-solid fa-play"></i>開始計時
                </button>
                <button id="stop" class="ml-6 bg-red-300 hover:bg-red-500 text-white font-bold py-2 px-4 rounded-full" onclick="stop();" disabled>
                    <i class="fa-solid fa-stop"></i>停止重來
                </button>    
            </div>
        </div>
        <div class="w-5/12 bg-blue-100 bg-opacity-50 text-center flex flex-col">
            <div id="buttons">
                <button class="bg-blue-500 hover:bg-blue-700 disabled:bg-gray-500 text-white font-bold py-2 px-4 rounded-full" onclick="positive_act();">
                    <i class="fa-solid fa-plus"></i>發送獎勵
                </button>
                <button class="ml-6 bg-red-500 hover:bg-red-700 disabled:bg-gray-500 text-white font-bold py-2 px-4 rounded-full" onclick="negative_act();">
                    <i class="fa-solid fa-minus"></i>發送懲罰
                </button>
            </div>
        </div>    
    </div>
</div>
<script nonce="selfhost">
    var audioContext;
    const volumeCells = document.querySelectorAll(".volume-cell")
    activeSound();

    var reason1 = '在靜謐山谷中安然度過危機';
    var reason2 = '挑戰靜謐山谷失敗';
    var uuids = [];
    @foreach ($characters as $c)
    uuids[{{ $loop->index }}] = '{{ $c->uuid }}';
    @endforeach

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-sound');
    var seconds = 0;
    var timerId;
    var startTime;
    var pauseTime;
    var remainingTime;
    var bar = document.getElementById('bar');
    var text = document.getElementById('text');
    var elem_xp = document.getElementById('xp');
    var elem_gp = document.getElementById('gp');
    var elem_item = document.getElementById('item');
    var elem_hp = document.getElementById('hp');
    var elem_mp = document.getElementById('mp');
    var elem_count = document.getElementById('timeout');
    var elem_start = document.getElementById('start');
    var elem_stop = document.getElementById('stop');
    elem_stop.disabled = true;

    function activeSound () {
        try {
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
            navigator.getUserMedia({ audio: true, video: false }, onMicrophoneGranted, onMicrophoneDenied);
        } catch(e) {
            alert(e);
        }
    }

    async function onMicrophoneGranted(stream) {
        audioContext = new AudioContext();
        let microphone = audioContext.createMediaStreamSource(stream);
        await audioContext.audioWorklet.addModule("{{ asset('js/processor.js')}}");
        const node = new AudioWorkletNode(audioContext, 'vumeter');
        node.port.onmessage = function (event) {
            handleVolumeCellColor(event.data.volume);
        };
        microphone.connect(node).connect(audioContext.destination);
    }

    function onMicrophoneDenied() {
        console.log('denied')
    }

    function handleVolumeCellColor(volume) {
        const allVolumeCells = [...volumeCells]
        const numberOfCells = Math.round(volume)
        const cellsToColored = allVolumeCells.slice(0, numberOfCells)

        for (const cell of allVolumeCells) {
            cell.classList.add('bg-gray-300');
        }

        for (const cell of cellsToColored) {
            cell.classList.add('bg-blue-300');
        }
    }

    function start() {
        targetSeconds = elem_count.value * 60;
        startTime = new Date().getTime();
        update(targetSeconds);
        timerId = setInterval(timer, 1000);

        elem_start.classList.add('hidden');
        elem_stop.disabled = false;
        elem_xp.setAttribute('readonly', '');
        elem_gp.setAttribute('readonly', '');
        elem_item.setAttribute('disabled', '');
        elem_hp.setAttribute('readonly', '');
        elem_mp.setAttribute('readonly', '');
    }

    function stop() {
        clearInterval(timerId);
        update(0);
        elem_start.classList.remove('hidden');
        elem_xp.removeAttribute('readonly');
        elem_gp.removeAttribute('readonly');
        elem_item.removeAttribute('disabled');
        elem_hp.removeAttribute('readonly');
        elem_mp.removeAttribute('readonly');
    }
 
    function timer() {
        var currentTime = new Date().getTime();
        var diffSec = Math.round((currentTime - startTime) / 1000);
        remainingTime = targetSeconds - diffSec;
        update(remainingTime);
        if (remainingTime <= 0) {
            clearInterval(timerId);
            update(0);
        }
    }

    function update (seconds) {
        var percent = Math.round((seconds / targetSeconds) * 100);
        bar.style.width = percent + "%";
        bar.innerHTML = percent + "%";

        var sec = seconds % 60;  
        var min = Math.floor(seconds / 60); 
        min = min.toString().padStart(2, '0');
        sec = sec.toString().padStart(2, '0');
        text.innerHTML = min + ":" + sec;		
    }

    function positive_act() {
        uuids = [];
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]:checked');
        nodes.forEach( node => {
            parties[node.id].forEach( char => {
                uuids.push(char); 
            });
        });

        var percent = remainingTime / targetSeconds;
        var xp = elem_xp.value * percent;
        var gp = elem_gp.value * percent;
        var item = elem_item.value;
        window.axios.post('{{ route('game.positive_act') }}', {
            uuid: '{{ Auth::user()->uuid }}',
            uuids: uuids.toString(),
            reason: reason1,
            xp: xp,
            gp: gp,
            item: item,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        window.location.reload();
    }

    function negative_act() {
        uuids = [];
        var nodes = document.querySelectorAll('input[type="checkbox"][data-group]:checked');
        nodes.forEach( node => {
            parties[node.id].forEach( char => {
                uuids.push(char); 
            });
        });

        var hp = elem_hp.value;
        var mp = elem_mp.value;
        window.axios.post('{{ route('game.negative_act') }}', {
            uuid: '{{ Auth::user()->uuid }}',
            uuids: uuids.toString(),
            reason: reason2,
            hp: hp,
            mp: mp,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        window.location.reload();
    }
</script>
@endsection
