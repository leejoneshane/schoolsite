<main class="w-full mb-32">
    <div class="m-5 relative bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        <div class="py-4">
            @if (isset($error) || session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <i class="fa-regular fa-bell"></i> {{ isset($error) ? $error : session()->get('error') }}
            </div>
            @endif
            @if (isset($success) || session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <i class="fa-regular fa-bell"></i> {{ isset($success) ? $success : session()->get('success') }}
            </div>
            @endif
            @if (isset($message) || session()->has('message'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <i class="fa-regular fa-bell"></i> {{ isset($message) ? $message : session()->get('message') }}
            </div>
            @endif
        </div>
        @yield('content')
    </div>
    <audio id="received" muted autoplay>
        <source src="{{ asset('sound/notify.mp3') }}" type="audio/mpeg">
    </audio>
    <div id="messager" class="fixed z-10 right-0 bottom-0 flex flex-col-reverse place-items-end">
        <div id="template" class="hidden flex items-center p-4 max-w-xs text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
            <div class="inline-flex flex-shrink-0 justify-center items-center w-8 h-8 text-blue-500 bg-blue-100 rounded-lg dark:bg-blue-800 dark:text-blue-200"></div>
            <div class="ml-3 text-sm font-normal"></div>
            <div class="flex gap-2">
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-7 w-7 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#template" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>    
            </div>
        </div>
    </div>
</main>
<script>
@auth
    function reply(uid) {
        var me = {{ auth()->user()->id }};
        var tell = prompt('您要告訴對方什麼？');
        if (tell) {
            window.axios.post('{{ route('messager.send') }}', {
                from: me,
                to: uid,
                message: tell,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }
@endauth

    document.addEventListener("DOMContentLoaded", function(event) { 
        window.Echo.channel('public').listen('PublicMessage', (e) => {
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-12', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
            from.innerText = '廣播';
            let info = document.createElement('div');
            info.classList.add('ml-3', 'text-sm', 'font-normal');
            info.innerText = e.message;
            let btn = document.createElement('button');
            btn.classList.add('ml-auto', '-mx-1.5', '-my-1.5', 'text-xl', 'bg-white', 'text-gray-400', 'hover:text-gray-900', 'rounded-lg', 'focus:ring-2', 'focus:ring-gray-300', 'p-1.5', 'hover:bg-gray-100', 'inline-flex', 'h-8', 'w-8', 'dark:text-gray-500', 'dark:hover:text-white', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
            btn.setAttribute('data-dismiss-target', '#messager_' + rnd);
            btn.setAttribute('aria-label', 'Close');
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            popup.appendChild(from);
            popup.appendChild(info);
            popup.appendChild(btn);
            let parent = document.getElementById('messager');
            parent.appendChild(popup);
            document.getElementById('received').play();
            new window.Dismiss(popup, { triggerEl: btn });
        });
    @auth
        window.Echo.private('private.{{ auth()->user()->id }}').listen('PrivateMessage', (e) => {
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-12', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
            from.innerText = e.from_user;
            let info = document.createElement('div');
            info.classList.add('ml-3', 'text-sm', 'font-normal');
            info.innerText = e.message;
            let btns = document.createElement('div');
            btns.classList.add('flex', 'gap-2');
            let reply = document.createElement('button');
            reply.classList.add('ml-auto', '-mx-1.5', '-my-1.5', 'bg-white', 'text-gray-400', 'hover:text-gray-900', 'rounded-lg', 'focus:ring-2', 'focus:ring-gray-300', 'p-1.5', 'hover:bg-gray-100', 'inline-flex', 'h-7', 'w-7', 'dark:text-gray-500', 'dark:hover:text-white', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
            reply.setAttribute("onclick","reply('" + e.from + "');");
            reply.innerHTML = '<i class="fa-solid fa-reply"></i>';
            btns.appendChild(reply);
            let btn = document.createElement('button');
            btn.classList.add('ml-auto', '-mx-1.5', '-my-1.5', 'bg-white', 'text-gray-400', 'hover:text-gray-900', 'rounded-lg', 'focus:ring-2', 'focus:ring-gray-300', 'p-1.5', 'hover:bg-gray-100', 'inline-flex', 'h-7', 'w-7', 'dark:text-gray-500', 'dark:hover:text-white', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
            btn.setAttribute('data-dismiss-target', '#messager_' + rnd);
            btn.setAttribute('aria-label', 'Close');
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            btns.appendChild(btn);
            popup.appendChild(from);
            popup.appendChild(info);
            popup.appendChild(btns);
            let parent = document.getElementById('messager');
            parent.appendChild(popup);
            document.getElementById('received').play();
            new window.Dismiss(popup, { triggerEl: btn });
        });
    @endauth
    @admin
        window.Echo.channel('admin').listen('AdminMessage', (e) => {
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-12', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
            from.innerText = '系統';
            let info = document.createElement('div');
            info.classList.add('ml-3', 'text-sm', 'font-normal');
            info.innerText = e.message;
            let btn = document.createElement('button');
            btn.classList.add('ml-auto', '-mx-1.5', '-my-1.5', 'text-xl', 'bg-white', 'text-gray-400', 'hover:text-gray-900', 'rounded-lg', 'focus:ring-2', 'focus:ring-gray-300', 'p-1.5', 'hover:bg-gray-100', 'inline-flex', 'h-8', 'w-8', 'dark:text-gray-500', 'dark:hover:text-white', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
            btn.setAttribute('data-dismiss-target', '#messager_' + rnd);
            btn.setAttribute('aria-label', 'Close');
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            popup.appendChild(from);
            popup.appendChild(info);
            popup.appendChild(btn);
            let parent = document.getElementById('messager');
            parent.appendChild(popup);
            document.getElementById('received').play();
            new window.Dismiss(popup, { triggerEl: btn });
        });
    @endadmin
    });
</script>