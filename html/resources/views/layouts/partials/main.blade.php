<main class="w-full h-full min-h-screen bg-game-map50 bg-cover bg-center bg-repeat-y flex flex-col">
    <div class="block w-full h-28"></div>
    <div class="relative w-full h-screen flex">
        <div class="relative block w-16 min-h-full"></div>
        <div class="w-full h-full">
            @teacher
            <div class="m-5 relative dark:bg-gray-700 text-black dark:text-gray-200">
                <div>
                    @if (isset($error) || session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-4 rounded relative" role="alert">
                        <i class="fa-regular fa-bell"></i> {{ isset($error) ? $error : session()->get('error') }}
                    </div>
                    @endif
                    @if (isset($success) || session()->has('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded relative" role="alert">
                        <i class="fa-regular fa-bell"></i> {{ isset($success) ? $success : session()->get('success') }}
                    </div>
                    @endif
                    @if (isset($message) || session()->has('message'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-4 rounded relative" role="alert">
                        <i class="fa-regular fa-bell"></i> {{ isset($message) ? $message : session()->get('message') }}
                    </div>
                    @endif
                </div>
                @yield('content')
            </div>
            @endteacher
            @student
            @yield('content')
            @endstudent
        </div>
    </div>
</main>
<audio id="received" muted autoplay>
    <source src="{{ asset('sound/notify.mp3') }}" type="audio/mpeg">
</audio>
@student
<div id="messager" class="fixed z-10 right-0 bottom-0 flex flex-col-reverse place-items-end">
    <div id="template" class="flex items-center p-4 max-w-xs text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
        <div class="inline-flex flex-shrink-0 justify-center items-center w-8 h-8 text-blue-500 bg-blue-100 rounded-lg dark:bg-blue-800 dark:text-blue-200"></div>
        <div class="ml-3 text-sm font-normal"></div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-7 w-7 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#template" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>
<script nonce="selfhost">
    document.addEventListener("DOMContentLoaded", function(event) {
        window.Echo.channel('classroom.{{ player()->classroom_id }}').listen('GameRoomChannel', (e) => {
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-16', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
            from.innerText = '全班廣播';
            popup.appendChild(from);
            let info = document.createElement('div');
            info.classList.add('ml-3', 'text-sm', 'font-normal');
            info.innerText = e.message;
            popup.appendChild(info);
            let btn = document.createElement('button');
            btn.classList.add('ml-auto', '-mx-1.5', '-my-1.5', 'text-xl', 'bg-white', 'text-gray-400', 'hover:text-gray-900', 'rounded-lg', 'focus:ring-2', 'focus:ring-gray-300', 'p-1.5', 'hover:bg-gray-100', 'inline-flex', 'h-8', 'w-8', 'dark:text-gray-500', 'dark:hover:text-white', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
            btn.setAttribute('data-dismiss-target', '#messager_' + rnd);
            btn.setAttribute('aria-label', 'Close');
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            popup.appendChild(btn);
            let parent = document.getElementById('messager');
            parent.appendChild(popup);
            document.getElementById('received').play();
            new window.Dismiss(popup, btn);
        });

        @if (player()->party)
        window.Echo.channel('party.{{ player()->party_id }}').listen('GamePartyChannel', (e) => {
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-16', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
            from.innerText = '公會頻道';
            popup.appendChild(from);
            let info = document.createElement('div');
            info.classList.add('ml-3', 'text-sm', 'font-normal');
            info.innerText = e.message;
            popup.appendChild(info);
            let btn = document.createElement('button');
            btn.classList.add('ml-auto', '-mx-1.5', '-my-1.5', 'text-xl', 'bg-white', 'text-gray-400', 'hover:text-gray-900', 'rounded-lg', 'focus:ring-2', 'focus:ring-gray-300', 'p-1.5', 'hover:bg-gray-100', 'inline-flex', 'h-8', 'w-8', 'dark:text-gray-500', 'dark:hover:text-white', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
            btn.setAttribute('data-dismiss-target', '#messager_' + rnd);
            btn.setAttribute('aria-label', 'Close');
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            popup.appendChild(btn);
            let parent = document.getElementById('messager');
            parent.appendChild(popup);
            document.getElementById('received').play();
            new window.Dismiss(popup, btn);
        });
        @endif

        window.Echo.private('character.{{ player()->stdno }}').listen('GameCharacterChannel', (e) => {
            if (e.message.substring(0, 14) == 'task_comments:') {
                task_comments(e.message.substring(14));
                return;
            }
            if (e.message.substring(0, 12) == 'task_notice:') {
                task_notice(e.message.substring(12));
                return;
            }
            if (e.message.substring(0, 10) == 'task_pass:') {
                task_pass(e.message.substring(10));
                return;
            }
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-16', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
            from.innerText = '私人頻道';
            popup.appendChild(from);
            let info = document.createElement('div');
            info.classList.add('ml-3', 'text-sm', 'font-normal');
            info.innerText = e.message;
            popup.appendChild(info);
            let btns = document.createElement('div');
            btns.classList.add('flex', 'gap-2');
            let btn = document.createElement('button');
            btn.classList.add('ml-auto', '-mx-1.5', '-my-1.5', 'bg-white', 'text-gray-400', 'hover:text-gray-900', 'rounded-lg', 'focus:ring-2', 'focus:ring-gray-300', 'p-1.5', 'hover:bg-gray-100', 'inline-flex', 'h-7', 'w-7', 'dark:text-gray-500', 'dark:hover:text-white', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
            btn.setAttribute('data-dismiss-target', '#messager_' + rnd);
            btn.setAttribute('aria-label', 'Close');
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            btns.appendChild(btn);
            popup.appendChild(btns);
            let parent = document.getElementById('messager');
            parent.appendChild(popup);
            document.getElementById('received').play();
            new window.Dismiss(popup, btn);
        });

        window.Echo.private('dialog.{{ player()->stdno }}').listen('GameDialogChannel', (e) => {
            if (e.code == 'invite') {
                received_invite(e);
                return;
            }
            if (e.code == 'accept_invite') {
                accept_invite(e);
                return;
            }
            if (e.code == 'reject_invite') {
                reject_invite(e);
                return;
            }
        });
    });
</script>
@endstudent