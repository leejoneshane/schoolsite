@extends('layouts.main')

@section('content')
    <div class="text-2xl font-bold leading-normal pb-5">
        午餐調查管理
        @if ($manager)
            <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600"
                href="{{ route('lunch.config', ['section' => $section]) }}">
                <i class="fa-regular fa-clock"></i>設定調查期程
            </a>
            <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('lunch.cafeterias') }}">
                <i class="fa-solid fa-utensils"></i>管理供餐地點
            </a>
            <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600"
                href="{{ route('lunch.downloadAll', ['section' => $section]) }}">
                <i class="fa-solid fa-file-export"></i>匯出調查結果
            </a>
            <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600"
                href="{{ route('lunch.downloadGrade', ['section' => $section]) }}">
                <i class="fa-solid fa-file-excel"></i>年級用餐確認表
            </a>
            <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600"
                href="{{ route('lunch.downloadLocation', ['section' => $section]) }}">
                <i class="fa-solid fa-file-excel"></i>各地點用餐名錄
            </a>
            <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600"
                href="{{ route('lunch.downloadPayment', ['section' => $section]) }}">
                <i class="fa-solid fa-file-invoice-dollar"></i>收費明細對帳單
            </a>
        @endif
    </div>
    <label for="section">請選擇學期：</label>
    <select id="section"
        class="inline w-32 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
        onchange="
                var section = this.value;
                window.location.replace('{{ route('lunch.manage') }}' + '/' + section + '?class={{ ($classroom) ? $classroom->id : '' }}');
                ">
        @foreach ($sections as $s)
            <option value="{{ $s }}" {{ ($s == $section) ? ' selected' : '' }}>
                {{ substr($s, 0, -1) . '學年第' . substr($s, -1) . '學期' }}
            </option>
        @endforeach
    </select>

    <div class="flex flex-col justify-center mt-4">
        <div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5 p-2" role="alert">
            <p>已調查班級數：{{ $count->classes }}　累計調查人數：{{ $count->students }}</p>
        </div>
        @if ($manager)
            <div>
                <label for="classes" class="inline">請選擇班級：</label>
                <select id="classes"
                    class="inline rounded w-32 py-2 mr-6 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    onchange="
                                        var cls = this.value;
                                        window.location.replace('{{ route('lunch.manage', ['section' => $section]) }}' + '?class=' + cls);
                                        ">
                    <option></option>
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}" {{ ($classroom && $classroom->id == $cls->id) ? ' selected' : '' }}>
                            {{ $cls->name }}
                        </option>
                    @endforeach
                </select>
                <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600"
                    href="{{ route('lunch.download', ['section' => $section, 'class_id' => $classroom ? $classroom->id : '']) }}">
                    <i class="fa-solid fa-file-export"></i>匯出此班級調查結果
                </a>
            </div>
        @elseif ($classroom)
            <div>
                班級：{{ $classroom->name }}
            </div>
        @else
            <div class="font-bold">非級任導師無法查看午餐調查結果！</div>
        @endif
        @if ($classroom)
            <table class="border border-1 p-3 w-3/4 mt-4">
                <tr>
                    <th scope="col" rowspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">班級</th>
                    <th scope="col" rowspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">座號</th>
                    <th scope="col" rowspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">姓名</th>
                    <th scope="col" colspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">參加午餐</th>
                    <th scope="col" colspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">乳品</th>
                    <th scope="col" colspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">不參加午餐
                    </th>
                </tr>
                <tr>
                    <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">葷食</th>
                    <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">素食</th>
                    <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">要飲用</th>
                    <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">改成豆乳</th>
                    <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">家長親送</th>
                    <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">蒸飯設備</th>
                </tr>
                @foreach ($surveys as $s)
                    <tr>
                        <td class="border border-1 text-sm text-center">{{ $s->student->classroom->name }}</td>
                        <td class="border border-1 text-sm text-center">{{ $s->student->seat }}</td>
                        <td class="border border-1 text-sm text-center">{{ $s->student->realname }}</td>
                        <td class="border border-1 text-sm text-center">
                            {!! ($s->by_school && !($s->vegen)) ? '<i class="fa-solid fa-check"></i>' : '' !!}
                        </td>
                        <td class="border border-1 text-sm text-center">
                            {!! ($s->by_school && $s->vegen) ? '<i class="fa-solid fa-check"></i>' : '' !!}
                        </td>
                        <td class="border border-1 text-sm text-center">
                            {!! ($s->by_school && $s->milk) ? '<i class="fa-solid fa-check"></i>' : '' !!}
                        </td>
                        <td class="border border-1 text-sm text-center">
                            {!! ($s->by_school && !($s->milk)) ? '<i class="fa-solid fa-check"></i>' : '' !!}
                        </td>
                        <td class="border border-1 text-sm text-center">
                            {!! ($s->by_parent) ? '<i class="fa-solid fa-check"></i>' : '' !!}
                        </td>
                        <td class="border border-1 text-sm text-center">
                            {!! ($s->boxed_meal) ? '<i class="fa-solid fa-check"></i>' : '' !!}
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection