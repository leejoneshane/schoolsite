<aside class="fixed w-16 top-28 h-screen bg-teal-500 z-40" aria-label="Sidebar">
    <div class="overflow-y-auto">
        @teacher
        <div class="py-2 text-center">
            <a href="{{ route('game') }}"><i class="text-2xl fa-solid fa-house" title="首頁"></i></a>
        </div>
        @locked(session('gameclass'))
        <div class="py-2 text-center">
            <a href="{{ session('gameclass') ? route('game.room', [ 'room_id' => session('gameclass') ]) : '#' }}"><i class="text-2xl fa-solid fa-clipboard-user" title="點名表"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="{{ route('game.pickup', [ 'room_id' => session('gameclass') ]) }}"><i class="text-2xl fa-solid fa-dice-d20" title="抽籤機"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="{{ route('game.timer', [ 'room_id' => session('gameclass') ]) }}"><i class="text-2xl fa-solid fa-stopwatch-20" title="計時器"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="{{ route('game.silence', [ 'room_id' => session('gameclass') ]) }}"><i class="text-2xl fa-solid fa-ear-listen" title="分貝計"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/map"><i class="text-2xl fa-solid fa-map-location-dot" title="批改學習單"></i></a>
        </div>
        @endlocked
        @endteacher
        @student
        <div class="py-2 text-center">
            <a href="{{ route('game.player') }}"><i class="text-2xl fa-solid fa-user" title="我的角色"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="{{ route('game.party') }}"><i class="text-2xl fa-solid fa-place-of-worship" title="公會"></i></a>
        </div>
        @locked
        <div class="py-2 text-center">
            <a href="{{ route('game.arena') }}"><i class="text-2xl fa-brands fa-battle-net" title="競技場"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/travel"><i class="text-2xl fa-solid fa-map-location-dot" title="冒險地圖"></i></a>
        </div>
        @endlocked
        <div class="py-2 text-center">
            <a href="{{ route('game.dungeon') }}"><i class="text-2xl fa-solid fa-dungeon" title="地下城"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="{{ route('game.furniture_shop') }}"><i class="text-2xl fa-solid fa-couch" title="矮人工坊"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="{{ route('game.item_shop') }}"><i class="text-2xl fa-solid fa-cart-shopping" title="妖精道具屋"></i></a>
        </div>
        @endstudent
    </div>
</aside>