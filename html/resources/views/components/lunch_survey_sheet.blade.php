<table>
    <tr>
        <th rowspan="2" style="font-weight:700;background-color:#f3f4f6;text-align:center;">班級</th>
        <th rowspan="2" style="font-weight:700;background-color:#f3f4f6;text-align:center;">座號</th>
        <th rowspan="2" style="font-weight:700;background-color:#f3f4f6;text-align:center;">姓名</th>
        <th colspan="2" style="font-weight:700;background-color:#f3f4f6;text-align:center;">參加午餐</th>
        <th colspan="2" style="font-weight:700;background-color:#f3f4f6;text-align:center;">乳品</th>
        <th colspan="2" style="font-weight:700;background-color:#f3f4f6;text-align:center;">不參加午餐</th>
    </tr>
    <tr>
        <th style="font-weight:700;background-color:#f3f4f6;text-align:center;">葷食</th>
        <th style="font-weight:700;background-color:#f3f4f6;text-align:center;">素食</th>
        <th style="font-weight:700;background-color:#f3f4f6;text-align:center;">要飲用</th>
        <th style="font-weight:700;background-color:#f3f4f6;text-align:center;">改成水果</th>
        <th style="font-weight:700;background-color:#f3f4f6;text-align:center;">家長親送</th>
        <th style="font-weight:700;background-color:#f3f4f6;text-align:center;">蒸飯設備</th>
    </tr>
    @foreach ($surveys as $s)
    <tr>
        <td style="text-align:center;">{{ $s->student->classroom->name }}</td>
        <td style="text-align:center;">{{ $s->student->seat }}</td>
        <td style="text-align:center;">{{ $s->student->realname }}</td>
        <td style="text-align:center;">{{ ($s->by_school && !($s->vegen)) ? 1 : 0  }}</td>
        <td style="text-align:center;">{{ ($s->by_school && $s->vegen) ? 1 : 0  }}</td>
        <td style="text-align:center;">{{ ($s->by_school && $s->milk) ? 1 : 0  }}</td>
        <td style="text-align:center;">{{ ($s->by_school && !($s->milk)) ? 1 : 0  }}</td>
        <td style="text-align:center;">{{ ($s->by_parent) ? 1 : 0  }}</td>
        <td style="text-align:center;">{{ ($s->boxed_meal) ? 1 : 0  }}</td>
    </tr>
    @endforeach
</table>