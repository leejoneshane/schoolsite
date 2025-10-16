<table class="border-2 border-slate-300">
    @foreach ($template->matrix as $cols)
    <tr class="h-10">
        @foreach ($cols as $group)
        <td class="w-16 border-2 border-slate-300 {{ $styles[$group] }}">&nbsp;</td>
        @endforeach
    </tr>
    @endforeach
</table>
<table>
    <tr class="h-10">
        <td class="w-48"></td>
        <td class="w-32 border-2 border-black bg-teal-300 text-center">講　　　　桌</td>
        <td class="w-48"></td>
    </tr>
</table>