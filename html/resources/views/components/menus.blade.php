@if ($menu->childs->count() > 0)
<ul class="space-y-2">
    @foreach ($menu->childs->sortBy('weight') as $item)
    <li>
        @if ($item->link == '#')
        <button type="button" class="flex items-center p-2 w-full text-base font-semibold text-teal-200 rounded-lg transition duration-75 group hover:bg-white hover:bg-opacity-25" aria-controls="mc_{{ $item->id }}" data-collapse-toggle="mc_{{ $item->id }}">
            <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>{{ $item->caption }}</span>
            <i sidebar-toggle-item class="fa-solid fa-angle-down"></i>
        </button>
        @php
        $prefix = '';
        if ($menu->id == 'admin') $prefix = 'admin/';
        $subitems = $item->childs->sortBy('weight');
        $show = false;
        if (Request::is($prefix.$item->id.'/*')) $show = true;
        foreach ($subitems as $subitem) {
            if (Request::is($prefix.$subitem->id.'*')) $show = true;
        }
        @endphp
        <ul id="mc_{{ $item->id }}" class="py-2 space-y-2{{ ($show) ? '' : ' hidden' }}">
        @foreach ($subitems as $subitem)
            <li>
                <a class="flex items-center p-2 text-base font-normal text-teal-200 rounded-lg hover:bg-white hover:bg-opacity-25"
                    href="{{ $subitem->link }}">
                    <span class="ml-3">{{ $subitem->caption }}</span>
                </a>
            </li>
        @endforeach
        </ul>
        @else
        <a class="flex items-center p-2 text-base font-normal text-teal-200 rounded-lg hover:bg-white hover:bg-opacity-25"
            href="{{ $item->link }}">
            <span class="ml-3">{{ $item->caption }}</span>
        </a>
        @endif
    </li>
    @endforeach
</ul>
@endif