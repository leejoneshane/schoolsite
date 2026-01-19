<table>
    <thead>
        <tr>
            <th>教師姓名</th>
            <th>週一</th>
            <th>週二</th>
            <th>週三</th>
            <th>週四</th>
            <th>週五</th>
            <th>素食</th>
            <th>豆奶</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['mon'] }}</td>
                <td>{{ $row['tue'] }}</td>
                <td>{{ $row['wed'] }}</td>
                <td>{{ $row['thu'] }}</td>
                <td>{{ $row['fri'] }}</td>
                <td>{{ $row['vegen'] }}</td>
                <td>{{ $row['soy_milk'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td><strong>用餐合計</strong></td>
            <td>{{ $totals['mon'] }}</td>
            <td>{{ $totals['tue'] }}</td>
            <td>{{ $totals['wed'] }}</td>
            <td>{{ $totals['thu'] }}</td>
            <td>{{ $totals['fri'] }}</td>
            <td><strong>素食合計</strong></td>
            <td>{{ $totals['vegen'] }}</td>
        </tr>
        <tr>
            <td><strong>豆奶合計</strong></td>
            <td>{{ $totals['soy_milk'] }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>