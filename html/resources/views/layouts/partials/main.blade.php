<main class="w-full h-full min-h-screen bg-game-map50 bg-cover bg-center bg-no-repeat">
@yield('content')
</main>
<audio id="received" muted autoplay>
    <source src="{{ asset('sound/notify.mp3') }}" type="audio/mpeg">
</audio>
@student
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
<script nonce="selfhost">
    var me = '{{ player()->uuid }}';

    function reply(uuid) {
        var tell = prompt('您要告訴對方什麼？');
        if (tell) {
            window.axios.post('{{ route('game.private') }}', {
                from: me,
                to: uuid,
                message: tell,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function(event) { 
        window.Echo.channel('classroom.{{ player()->class_id }}').listen('GameRoomChannel', (e) => {
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-12', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
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
            new window.Dismiss(popup, { triggerEl: btn });
        });

        @if (player() && player()->party)
        window.Echo.channel('party.{{ player()->party_id }}').listen('GamePartyChannel', (e) => {
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-12', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
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
            new window.Dismiss(popup, { triggerEl: btn });
        });
        @endif

        @if (player())
        window.Echo.private('character.{{ player()->student->stdno }}').listen('GameCharacterChannel', (e) => {
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
            let rnd = Math.floor(Math.random() * 100000);
            let popup = document.createElement('div');
            popup.id = 'messager_' + rnd;
            popup.classList.add('flex', 'items-center', 'p-4', 'max-w-xs', 'text-gray-500', 'bg-white', 'rounded-lg', 'shadow', 'dark:text-gray-400', 'dark:bg-gray-80');
            popup.role = 'alert';
            let from = document.createElement('div');
            from.classList.add('inline-flex', 'flex-shrink-0', 'justify-center', 'items-center', 'w-12', 'h-8', 'text-blue-500', 'bg-blue-100', 'rounded-lg', 'dark:bg-blue-800', 'dark:text-blue-200');
            from.innerText = e.from.name;
            popup.appendChild(from);
            let info = document.createElement('div');
            info.classList.add('ml-3', 'text-sm', 'font-normal');
            info.innerText = e.message;
            popup.appendChild(info);
            let btns = document.createElement('div');
            btns.classList.add('flex', 'gap-2');
            let reply = document.createElement('button');
            reply.classList.add('ml-auto', '-mx-1.5', '-my-1.5', 'bg-white', 'text-gray-400', 'hover:text-gray-900', 'rounded-lg', 'focus:ring-2', 'focus:ring-gray-300', 'p-1.5', 'hover:bg-gray-100', 'inline-flex', 'h-7', 'w-7', 'dark:text-gray-500', 'dark:hover:text-white', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
            reply.setAttribute("onclick","reply('" + e.from.uuid + "');");
            reply.innerHTML = '<i class="fa-solid fa-reply"></i>';
            btns.appendChild(reply);
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
            new window.Dismiss(popup, { triggerEl: btn });
        });
        @endif
    });
</script>
@endstudent