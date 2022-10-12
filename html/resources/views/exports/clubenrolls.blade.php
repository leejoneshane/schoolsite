<table>
    <thead>
        <tr>
            <th colspan="7">{{ $club->name }}通訊錄</th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr>
            <th>學生社團全名</th>
            <th>分類</th>
            <th>負責單位</th>
            <th>招生年級</th>
            <th>指導老師</th>
            <th>授課地點</th>
            <th>上課時間</th>
        </tr>
        <tr>
            <th>{{ $club->name }}</th>
            <th>{{ $club->kind->name }}</th>
            <th>{{ $club->unit->name }}</th>
            <th>{{ $club->grade }}</th>
            <th>{{ $club->teacher }}</th>
            <th>{{ $club->location }}</th>
            <th>{{ $club->studytime }}</th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td >編號</td>
            <td>年班座號</td>
            <td>學生姓名</td>
        @if ($club->has_lunch)
            <td>營養午餐</td>
        @endif
        @if ($club->self_defined)
            <td>自選上課日</td>
        @endif
            <td>聯絡人</td>
            <td>聯絡信箱</td>
            <td>聯絡電話</td>
            <td>備註</td>
        </tr>
        @foreach ($enrolls as $key => $en)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $en->student->class_id . (($en->student->seat < 10) ? '0'.$en->student->seat : $en->student->seat)}}</td>
            <td>{{ $en->student->realname }}</td>
        @if ($club->has_lunch)
            <td>{{ $en->lunch }}</td>
        @endif
        @if ($club->self_defined)
            <td>{{ $en->weekday }}</td>
        @endif
            <td>{{ $en->parent }}</td>
            <td>{{ $en->email }}</td>
            <td>{{ $en->mobile }}</td>
            <td>{{ $en->mark }}</td>
        </tr>
        @endforeach
    </tbody>
</table>