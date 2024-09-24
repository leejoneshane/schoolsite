@extends('layouts.game')

@section('content')
<div class="flex flex-col">
    <div class="w-full">
        <div id="text" class="leading-none text-white text-[30rem]"></div>
        <div class="relative w-full h-12 bg-gray-200 dark:bg-gray-700">
            <div id="bar" class="h-12 bg-red-600 p-1 text-lg text-center leading-none" style="width: 100%"></div>
            <div id="mark" class="absolute top-0 w-1 h-12 bg-blue-600" style="left: 60%"></div>
        </div>
    <div class="w-full p-2 bg-blue-100 bg-opacity-50">
        <p>靜謐山谷：在時間內保持安靜，悄悄通過山谷，完成任務獲得經驗值。如果在時間停止前發出太大的聲音，將給予懲罰。</p>
    </div>
    <div class="flex flex-row">
        <div class="w-1/2 flex flex-col bg-blue-100 bg-opacity-50">
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
        <div class="w-1/2 bg-blue-100 bg-opacity-50 flex flex-col">
            <div class="text-white rounded-lg drop-shadow-lg">
                計時<input type="number" id="timeout" min="1" max="30" value="5" class="p-0 bg-transparent">分鐘，
                不超過<input type="number" id="volume" min="1" max="100" value="60" class="p-0 bg-transparent" onchange="show_mark();">分貝
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
    </div>
</div>
<div id="warnModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[80] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-teal-300 rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">通知</h3>
            </div>
            <div id="message" class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="warnModal.hide();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    我知道了
                </button>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var reason1 = '在靜謐山谷中安然度過危機';
    var reason2 = '挑戰靜謐山谷失敗';
    var uuids = [];
    @foreach ($characters as $c)
    uuids[{{ $loop->index }}] = '{{ $c->uuid }}';
    @endforeach

    var main = document.getElementsByTagName('main')[0];
    main.classList.replace('bg-game-map50', 'bg-game-sound');
    var $targetEl = document.getElementById('warnModal');
    const warnModal = new window.Modal($targetEl);

    var audioContext;
    var targetSeconds = 0;
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
        console.log('denied');
    }

    function handleVolumeCellColor(volume) {
        var max = document.getElementById('volume').value;
        bar.style.width = volume + "%";
        bar.innerHTML = Math.floor(volume);
        if (volume > max) {
            stop();
            negative_act();
        }
    }

    function show_mark() {
        var max = document.getElementById('volume').value;
        var mark = document.getElementById('mark');
        mark.style.left = max + "%";
    }

    function start() {
        activeSound();
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
            positive_act();
        }
    }

    function update (seconds) {
        var sec = seconds % 60;  
        var min = Math.floor(seconds / 60); 
        min = min.toString().padStart(2, '0');
        sec = sec.toString().padStart(2, '0');
        text.innerHTML = min + ":" + sec;
    }

    function positive_act() {
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
        var msg = document.getElementById('message');
        msg.innerHTML = '恭喜，獎勵已經發送給全班！';
        warnModal.show();
    }

    function negative_act() {
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
        var msg = document.getElementById('message');
        msg.innerHTML = '糟糕，挑戰失敗，所有人遭到懲罰！';
        warnModal.show();
    }
</script>
@endsection
