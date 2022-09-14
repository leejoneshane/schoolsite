<nav class="flex items-center justify-between flex-wrap bg-teal-500 px-6 py-1">
  <div id="left-section" class="flex items-center flex-shrink-0 text-white">
    <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto divide-x divide-teal-200">
      @if (!Request::is('/'))
      <span class="block lg:inline-block mt-2 lg:mt-0 px-4 lg:px-2">
        <i class="fa-solid fa-house"></i>
        <a href="{{ route('home') }}" class="text-teal-200 hover:text-white">回首頁</a>
      </span>
      @endif
      <span class="block lg:inline-block mt-2 lg:mt-0 px-4 lg:px-2">
        <i class="fa-solid fa-school"></i>
        <a href="https://www.meps.tp.edu.tw" class="text-teal-200 hover:text-white">前往官網</a>
      </span>
      <span class="block lg:inline-block mt-2 lg:mt-0 px-4 lg:px-2">
        <i class="fa-solid fa-database"></i>
        <a href="https://next.meps.tp.edu.tw" class="text-teal-200 hover:text-white">雲端硬碟</a>
      </span>
      <span class="block lg:inline-block mt-2 lg:mt-0 px-4 lg:px-2">
        <i class="fa-solid fa-film"></i>
        <a href="https://utube.meps.tp.edu.tw" class="text-teal-200 hover:text-white">影音平台</a>
      </span>
@admin
      <span class="block lg:inline-block mt-2 lg:mt-0 px-4 lg:px-2">
        <i class="fa-solid fa-gear"></i>
        <a href="{{ route('admin') }}" class="text-teal-200 hover:text-white">管理面板</a>
      </span>
@endadmin
    </div>
  </div>
  <x-messager />
  <div id="right-section" class="flex items-center flex-shrink-0 text-white">
@auth
    <span class="block lg:inline-block mt-2 lg:mt-0 px-4 lg:px-2">
      <i class="fa-solid fa-share-nodes"></i>
      <a href="{{ route('social') }}" class="text-teal-200 hover:text-white">社群帳號管理</a>
    </span>
    <span class="inline-block mt-2 lg:mt-0 px-4 lg:px-2 py-1 leading-none border rounded border-white hover:border-transparent text-white hover:text-teal-500 hover:bg-white">    
      <i class="fa-solid fa-door-open"></i>
      <a href="#" onclick="document.getElementById('logout-form').submit();" class="text-sm">登出</a>
    </span>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
      @csrf
    </form>
@endauth
@guest
    <span class="inline-block mt-2 lg:mt-0 px-4 lg:px-2 py-1 leading-none border rounded border-white hover:border-transparent text-white hover:text-teal-500 hover:bg-white">    
      <i class="fa-solid fa-circle-user"></i>
      <a href="{{ route('login') }}" class="text-sm">登入</a>
    </span>
@endguest
  </div>
</nav> 