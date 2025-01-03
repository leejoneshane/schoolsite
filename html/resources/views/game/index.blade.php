@extends('layouts.game')

@section('content')
<div class="sm:p-4 md:p-8">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 gap-4">
        @foreach ($classes as $cls)
        <a href="{{ route('game.room', [ 'room_id' => $cls->id ]) }}"
            class="relative flex h-full flex-col rounded-md bg-gradient-to-r from-teal-400 to-blue-500 hover:from-yellow-400 hover:to-pink-500 p-2.5 sm:rounded-lg sm:p-5">
            <span class="inline-flex text-md mb-2 font-semibold text-gray-900 hover:text-black sm:mb-1.5">
                {{ $cls->name }}
                <span class="ml-10 text-sm text-gray-400 sm:block">
                    <label for="lockdown_{{ $cls->id }}" class="inline-flex relative items-center cursor-pointer">
                        <input type="checkbox" id="lockdown_{{ $cls->id }}" name="lockdown" value="yes" class="sr-only peer"{{ locked($cls->id) ? ' checked' : (is_lock($cls->id) ? ' disabled' : '') }}
                            onchange="lock({{ $cls->id }});">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-gray-900 dark:text-gray-300">開始上課</span>
                    </label>
                </span>  
            </span>
            <span class="text-sm mb-0 font-normal text-gray-300">
                {{ $cls->error ?: '' }}
            </span>
        </a>
        @endforeach
        </div>
    </div>
</div>
<script nonce="selfhost">
    function lock(cls) {
        if (document.getElementById('lockdown_' + cls).checked) {
            var value = 'yes';
        } else {
            var value = 'no';
        }
        window.axios.post('{{ route('game.lock') }}', {
            room_id: cls,
            lockdown: value,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            if (response.data.success == 'locked') {
                alert("已將此班級鎖定，預計 40 分鐘後自動解鎖！");
            }
            if (response.data.success == 'unlocked') {
                alert("已將此班級解鎖！");
            }
        }).catch( (response) => {
            console.log(response.data);
        });
    }
</script>
@endsection
