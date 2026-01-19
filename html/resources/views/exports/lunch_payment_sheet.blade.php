<table>
    <thead>
        <tr>
            <th colspan="6"><strong>常規用餐</strong></th>
        </tr>
        <tr>
            <th>班級</th>
            <th>次/月</th>
            <th>金額/日</th>
            <th>金額</th>
            <th>人數</th>
            <th>小計</th>
        </tr>
    </thead>
    <tbody>
        @foreach($regular_rows as $row)
            <tr>
                <td>{{ $row['class_name'] }}</td>
                <td>{{ $weeks_per_month }}</td>
                <td>{{ $price_per_day }}</td>
                <td>{{ $monthly_price }}</td>
                <td>{{ $row['count'] }}</td>
                <td>{{ $row['subtotal'] }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="6"></td>
        </tr>

        <tr>
            <th colspan="6"><strong>課照班用餐</strong></th>
        </tr>
        <tr>
            <th>年級</th>
            <th>次/月</th>
            <th>金額/日</th>
            <th>金額</th>
            <th>人數</th>
            <th>小計</th>
        </tr>
        @foreach($after_school_rows as $row)
            <tr>
                <td>{{ $row['grade_name'] }}</td>
                <td>{{ $weeks_per_month }}</td>
                <td>{{ $price_per_day }}</td>
                <td>{{ $monthly_price }}</td>
                <td>{{ $row['count'] }}</td>
                <td>{{ $row['subtotal'] }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="6"></td>
        </tr>

        <tr>
            <th colspan="6"><strong>教師用餐</strong></th>
        </tr>
        <tr>
            <th>項目</th>
            <th>總次數</th>
            <th>金額/日</th>
            <th></th>
            <th></th>
            <th>總金額</th>
        </tr>
        <tr>
            <td>全體教師</td>
            <td>{{ $teacher_total_days }}</td>
            <td>{{ $price_per_day }}</td>
            <td></td>
            <td></td>
            <td>{{ $teacher_total_amount }}</td>
        </tr>
    </tbody>
</table>