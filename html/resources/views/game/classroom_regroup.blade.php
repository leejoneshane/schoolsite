@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    {{ $room->name }}重新分組
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.party_add') }}">
        <i class="fa-solid fa-circle-plus"></i>新增公會
    </a>
</div>
<div class="w-full h-full flex flex-row gap-x-10">
    <div id="nogroup" droppable="true" class="drop w-60 h-auto bg-teal-100 rounded-lg p-5 space-y-2">
        <div class="text-center text-lg">未分組</div>
        @foreach ($partyless as $s)
        <div id="{{ $s->uuid }}" draggable="true" class="drag w-48 bg-teal-500 p-5 rounded-md text-white">{{ $s->seat }} {{ $s->name }} <span class="text-gray-300">{{$s->profession ? $s->profession->name : '村民'}}</span></div>
        @endforeach
    </div>
    <div class="w-auto h-full grid grid-cols-3 gap-4">
        @foreach ($parties as $p)
        <div id="p{{ $p->id }}" droppable="true" class="drop w-60 min-h-20 bg-white border border-2 border-teal-500 rounded-lg p-5 space-y-2">
            <div class="w-full text-center text-lg">
                {{ $p->group_no }} {{ $p->name }}
                <a class="py-2 pl-6 text-blue-500 hover:text-blue-600"
                    href="{{ route('game.party_edit', ['party_id' => $p->id]) }}" title="編輯">
                    <i class="fa-solid fa-pen"></i>
                </a>
                <button class="py-2 pl-6 text-red-300 hover:text-red-600" title="刪除"
                    onclick="
                        const myform = document.getElementById('remove');
                        myform.action = '{{ route('game.party_remove', ['party_id' => $p->id]) }}';
                        myform.submit();
                ">
                <i class="fa-solid fa-trash"></i>
                </button>
            </div>
            @foreach ($p->members as $s)
            <div id="{{ $s->uuid }}" draggable="true" class="drag w-48 bg-teal-500 p-5 rounded-md text-white">{{ $s->seat }} {{ $s->name }} <span class="text-gray-300">{{$s->profession ? $s->profession->name : '村民'}}</span></div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
<script nonce="selfhost">
    let dragTemp;
    document.querySelectorAll(".drag").forEach( (elem) => {
        elem.addEventListener('dragstart', (e) => {
            dragTemp = e.target;
        });
    });
    document.querySelectorAll(".drop").forEach( (elem) => {
        elem.addEventListener('dragover', (e) => {
            e.preventDefault();
        });
        elem.addEventListener('drop', (e) => {
            elem.appendChild(dragTemp);
            if (elem.id == 'nogroup') {
                var party = 0;
            } else {
                var party = elem.id.substring(1);
            }
            var uuid = dragTemp.id;
            window.axios.post('{{ route('game.change_party') }}', {
                party: party,
                uuid: uuid,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        });
    });
</script>
@endsection
