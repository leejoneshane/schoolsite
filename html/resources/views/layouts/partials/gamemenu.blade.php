<aside class="w-16 h-auto bg-teal-500" aria-label="Sidebar">
    <div class="overflow-y-auto">
        <div class="py-2 text-center">
            <a href="{{ route('game') }}"><i class="text-2xl fa-solid fa-house" title="首頁"></i></a>
        </div>
        @teacher
        <div class="py-2 text-center">
            <a href="{{ route('game.room', [ 'room_id' => session('gameclass') ]) }}"><i class="text-2xl fa-solid fa-clipboard-user" title="點名表"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/pickup"><i class="text-2xl fa-solid fa-dice-d20" title="抽籤機"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/timer"><i class="text-2xl fa-solid fa-stopwatch-20" title="計時器"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/silence"><i class="text-2xl fa-solid fa-ear-listen" title="分貝計"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/map"><i class="text-2xl fa-solid fa-map-location-dot" title="批改學習單"></i></a>
        </div>
        @endteacher
        @student
        <div class="py-2 text-center">
            <a href="/game/me"><i class="text-2xl fa-solid fa-person-hiking" title="我的角色"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/party"><i class="text-2xl fa-solid fa-place-of-worship" title="公會"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/box"><i class="text-2xl fa-solid fa-toolbox" title="道具箱"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/arena"><i class="text-2xl fa-brands fa-battle-net" title="競技場"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/dungeon"><i class="text-2xl fa-solid fa-dungeon" title="地牢"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/travel"><i class="text-2xl fa-solid fa-map-location-dot" title="冒險地圖"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/work_shop"><i class="text-2xl fa-solid fa-couch" title="矮人工坊"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/item_shop"><i class="text-2xl fa-solid fa-cart-shopping" title="妖精道具屋"></i></a>
        </div>
        <div class="py-2 text-center">
            <a href="/game/pet_shop"><i class="text-2xl fa-solid fa-horse" title="寵物牧場"></i></a>
        </div>
        @endstudent
    </div>
</aside>