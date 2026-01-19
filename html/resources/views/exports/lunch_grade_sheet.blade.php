<table>
    <thead>
        <tr>
            <th>班級</th>
            <th>學生葷食人數</th>
            <th>學生素食座號</th>
            <th>學生總人數</th>
            <th>乳糖不耐症學生座號</th>
            <th>未訂餐學生座號</th>
            <th>請老師填寫第一學期全班不用餐人數</th>
            <th>導師用餐（隨班用餐）</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['class_name'] }}</td>
                <td>{{ $row['meat_count'] }}</td>
                <td>{{ $row['vegen_seats'] }}</td>
                <td>{{ $row['total_students'] }}</td>
                <td>{{ $row['milk_seats'] }}</td>
                <td>{{ $row['no_order_seats'] }}</td>
                <td></td>
                <td>{{ $row['tutor_dining'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>