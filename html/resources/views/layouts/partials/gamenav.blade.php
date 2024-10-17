<nav class="relative bg-teal-500 border-gray-200 dark:bg-gray-900 dark:border-gray-700">
  <div class="w-full flex justify-between mx-0 px-4 py-2.5">
    <button data-collapse-toggle="navbar-dropdown" type="button" class="inline-flex items-center p-2 ms-3 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-dropdown" aria-expanded="false">
      <span class="sr-only">下拉選單</span>
      <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
      </svg>
    </button>
    <div class="hidden w-full md:block md:w-auto" id="navbar-dropdown">
      <ul class="flex flex-col font-medium p-1 mt-4 border border-gray-100 rounded-lg bg-teal-300 md:flex-row md:mt-0 md:text-sm  md:border-0 md:bg-teal-300 dark:bg-teel-800 md:dark:bg-teal-900 dark:border-teal-700 md:space-x-8 md:rtl:space-x-reverse">
        <li>
          <button id="classList" data-dropdown-toggle="classes" data-dropdown-placement="bottom" class="flex items-center justify-between w-full py-2 px-3 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">
            選擇班級
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <!-- Dropdown menu -->
          <div id="classes" class="z-10 hidden font-normal bg-teal-100 divide-y divide-gray-100 rounded-lg shadow w-auto dark:bg-gray-700 dark:divide-gray-600">
              <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownLargeButton">
                @foreach (employee()->classrooms as $cls)
                <li>
                  <a href="{{ route('game.room', [ 'room_id' => $cls->id ]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                    {{ $cls->name }}
                    @if (session('gameclass') == $cls->id || session('viewclass') == $cls->id)
                    <i class="fa-solid fa-check"></i>
                    @endif
                  </a>
                </li>
                @endforeach
              </ul>
          </div>
        </li>
        <li>
          <button id="teacherMenu" data-dropdown-toggle="settings" class="flex items-center justify-between w-full py-2 px-3 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">
            教室規則
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <!-- Dropdown menu -->
          <div id="settings" class="z-10 hidden font-normal bg-teal-100 divide-y divide-gray-100 rounded-lg shadow w-auto dark:bg-gray-700 dark:divide-gray-600">
              <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownLargeButton">
                <li>
                  <a href="{{ route('game.positive') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">獎勵條款</a>
                </li>
                <li>
                  <a href="{{ route('game.negative') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">懲罰條款</a>
                </li>
                <li>
                  <a href="{{ route('game.evaluates') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">自主評量</a>
                </li>
                <li>
                  <a href="/game/teacher/map" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">冒險地圖</a>
                </li>
              </ul>
          </div>
        </li>
        @locked(session('gameclass'))
        <li>
          <button id="classroomMenu" data-dropdown-toggle="configure" class="flex items-center justify-between w-full py-2 px-3 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">
            班級規則
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <!-- Dropdown menu -->
          <div id="configure" class="z-10 hidden font-normal bg-teal-100 divide-y divide-gray-100 rounded-lg shadow w-auto dark:bg-gray-700 dark:divide-gray-600">
              <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownLargeButton">
                <li>
                  <a href="{{ route('game.classroom_config') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">遊戲規則</a>
                </li>
                <li>
                  <a href="{{ route('game.regroup') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">公會調整</a>
                </li>
                <li>
                  <a href="{{ route('game.characters') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">角色調整</a>
                </li>
                <li>
                  <a href="{{ route('game.dungeons') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">評量歷程</a>
                </li>
                <li>
                  <a href="{{ route('game.reset') }}" class="block px-4 py-2 text-red-500 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-red-900">重新遊戲</a>
                </li>
              </ul>
          </div>
        </li>
        @endlocked
        @if (Auth::user()->is_admin || Auth::user()->hasPermission('game.manager'))
        <li>
          <button id="adminMenu" data-dropdown-toggle="administration" class="flex items-center justify-between w-full py-2 px-3 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">
            系統管理
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <!-- Dropdown menu -->
          <div id="administration" class="z-10 hidden font-normal bg-teal-100 divide-y divide-gray-100 rounded-lg shadow w-auto dark:bg-gray-700 dark:divide-gray-600">
              <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownLargeButton">
                <li>
                  <a href="{{ route('game.classes') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">職業</a>
                </li>
                <li>
                  <a href="{{ route('game.skills') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">技能</a>
                </li>
                <li>
                  <a href="{{ route('game.bases') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">據點</a>
                </li>
                <li>
                  <a href="{{ route('game.furnitures') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">家具</a>
                </li>
                <li>
                  <a href="{{ route('game.items') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">道具</a>
                </li>
                <li>
                  <a href="{{ route('game.monsters') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">怪物</a>
                </li>
                <li>
                  <a href="{{ route('game.maps') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">地圖</a>
                </li>
              </ul>
          </div>
        </li>
        @endif
      </ul>
    </div>
    <div class="w-auto">
    @locked(session('gameclass'))
    <span class="text-xl text-white">{{ session('gameclass') }}</span>
    @elseif (session('viewclass'))
    <span class="text-xl text-red-500">{{ session('viewclass') }}</span>
    @endlocked
    </div>
    @locked(session('gameclass'))
    <div class="hidden w-auto md:block" id="right-dropdown">
      <ul class="flex flex-col font-medium p-1 mt-4 border border-gray-100 rounded-lg bg-teal-300 md:flex-row md:mt-0 md:text-sm  md:border-0 md:bg-teal-300 dark:bg-teel-800 md:dark:bg-teal-900 dark:border-teal-700 md:space-x-8 md:rtl:space-x-reverse">
        <li>
          <button id="delayList" data-dropdown-toggle="delays" data-dropdown-placement="bottom" class="flex items-center justify-between w-full py-2 px-3 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">
            延遲處置
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <!-- Dropdown menu -->
          <div id="delays" class="z-10 hidden font-normal bg-teal-100 divide-y divide-gray-100 rounded-lg shadow w-auto dark:bg-gray-700 dark:divide-gray-600">
              <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownLargeButton">
                @forelse (employee()->game_delay as $d)
                <li>
                  <a href="{{ route('game.regress', [ 'delay_id' => $d->id ]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                    {{ $d->description }}
                  </a>
                </li>
                @empty
                <li>恭喜，沒有需要處理的事項！</li>
                @endforelse
              </ul>
          </div>
        </li>
        <li>
          <button id="delayList" data-dropdown-toggle="logs" data-dropdown-placement="bottom" class="flex items-center justify-between w-full py-2 px-3 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">
            遊戲日誌
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <!-- Dropdown menu -->
          <div id="logs" class="z-10 hidden font-normal bg-teal-100 divide-y divide-gray-100 rounded-lg shadow w-auto dark:bg-gray-700 dark:divide-gray-600">
              <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownLargeButton">
                @forelse (employee()->game_logs(session('gameclass')) as $l)
                <li>
                    {{ $l->content }}
                </li>
                @empty
                <li>沒有遊戲紀錄！</li>
                @endforelse
              </ul>
          </div>
        </li>
      </ul>
    </div>
    @endlocked
    </div>
</nav>
@locked(session('gameclass'))
<script nonce="selfhost">
    function health() {
        window.axios.get('{{ route('game.health') }}')
        .then(response => {
            if (!(response.data.health)) {
                window.location.replace('{{ route('game') }}');
            }
        });
    }
    window.setInterval(health, 60000);
</script>
@endlocked