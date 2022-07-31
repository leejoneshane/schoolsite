@if ($items->count() > 0)
<ul class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-56 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label" aria-activedescendant="listbox-option-3">
  @foreach ($items as $item)
  <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9" id="listbox-option-0" role="option">
    <div class="flex items-center">
    @if ($menu->link == '#')
      <span class="font-normal ml-3 block truncate">{{ $item->caption }}</span>
    @else
      <a class="font-normal ml-3 block truncate hover:font-semibold" href="{{ $item->link }}">{{ $item->caption }}</a>
    @endif
    @if ($item->childs->count() > 0)
      <x-menus :id="$item->id"/>
    @endif
    </div>
  </li>
  @endforeach
</ul>
@endif