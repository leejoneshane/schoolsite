@if ($items->count() > 0)
  <ul class="list-none">
  @foreach ($items as $item)
    <li class="">
    @if ($menu->link == '#')
      <span class="">{{ $item->caption }}</span>
    @else
      <a class="" href="{{ $item->link }}">{{ $item->caption }}</a></li>
    @endif
    @if ($item->childs->count() > 0)
      @include('components.menus', ['menu' => $item, 'items' => $item->childs])
    @endif
    </li>
  @endforeach
  </ul>
@endif