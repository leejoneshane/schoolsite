@if ($items->count() > 0)
<ul id="mc_{{ $menu->id }}" class="{{ $display }} z-10 mt-1 w-full h-auto rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label" aria-activedescendant="listbox-option-3">
  @foreach ($items as $item)
  <li class="text-white cursor-default select-none relative py-2 pl-3 pr-9" id="listbox-option-0" role="option">
    <div>
    @if ($item->link == '#')
      <span class="font-normal ml-3 block truncate" onclick="
        element = document.getElementById('mc_{{ $item->id }}');
        element.classList.toggle('hidden');
        if (element.classList.contains('hidden')) {
          var icon = document.getElementById('{{ $item->id }}_up');
          icon.classList.remove('hidden');
          var icon = document.getElementById('{{ $item->id }}_down');
          icon.classList.add('hidden');
        } else {
          var icon = document.getElementById('{{ $item->id }}_up');
          icon.classList.add('hidden');
          var icon = document.getElementById('{{ $item->id }}_down');
          icon.classList.remove('hidden');
        }
      ">{{ $item->caption }}
      <i id="{{ $item->id }}_up" class="pl-2 fa-solid fa-angle-up"></i>
      <i id="{{ $item->id }}_down" class="hidden pl-2 fa-solid fa-angle-down"></i>
      </span>
      @if (Request::is($url.'/'.$item->id.'/*'))
        <x-menus id="{{ $url }}/{{ $item->id }}" display="show"/>
      @else
        <x-menus id="{{ $url }}/{{ $item->id }}" display="hidden"/>
      @endif
    @else
      <a class="font-normal ml-3 block truncate hover:font-semibold" href="{{ $item->link }}">{{ $item->caption }}</a>
    @endif
    </div>
  </li>
  @endforeach
</ul>
@endif