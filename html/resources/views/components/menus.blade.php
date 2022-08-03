@if ($items->count() > 0)
<ul class="z-10 mt-1 w-full h-auto rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label" aria-activedescendant="listbox-option-3">
  @foreach ($items as $item)
  <li class="text-white cursor-default select-none relative py-2 pl-3 pr-9" id="listbox-option-0" role="option">
    <div>
    @if ($item->link == '#')
      <span class="font-normal ml-3 block truncate">{{ $item->caption }}</span>
      <x-menus :id="$item->id"/>
    @else
      <a class="font-normal ml-3 block truncate hover:font-semibold" href="{{ $item->link }}">{{ $item->caption }}</a>
    @endif
    </div>
  </li>
  @endforeach
</ul>
@endif