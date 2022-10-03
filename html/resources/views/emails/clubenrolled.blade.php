<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite
</head>
<body>
    <div class="flex min-h-screen p-5 bg-blue-100 min-w-screen">
        <div class="max-w-xl p-8 justify-items-center text-gray-800 bg-white shadow-xl lg:max-w-3xl rounded-3xl lg:p-12">
            <img src="{{ asset('images/logo.jpg') }}" class="m-5">
            <h3 class="text-2xl">敬愛的家長{{ $enroll->parent }}，您好：</h3>
            <div class="mt-4">
                您為貴子弟（{{ $enroll->student->class->name }}{{ $enroll->student->seat }}號{{ $enroll->student->name }}）報名參加{{ $enroll->club->name }}，
                已獲錄取，社團活動費新台幣{{ $enroll->club->cash }}元整，將計入下學期四聯單於開學時隨學費同時收繳。
                社團相關資訊如下：
                <table class="w-full py-4 text-left font-normal">
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            負責單位
                        </th>
                        <td class="p-2">
                            {{ $enroll->club->unit->name }}
                        </td>
                    </tr>
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            上課時間
                        </th>
                        <td class="p-2">
                            {{ $enroll->club->studytime }}
                        </td>
                    </tr>
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            指導老師
                        </th>
                        <td class="p-2">
                            {{ $enroll->club->teacher }}
                        </td>
                    </tr>
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            授課地點
                        </th>
                        <td class="p-2">
                            {{ $enroll->club->location }}
                        </td>
                    </tr>
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            招生人數
                        </th>
                        <td class="p-2">
                            {{ $enroll->club->total }}
                        </td>
                    </tr>
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            已報名
                        </th>
                        <td class="p-2">
                            截至 {{ Carbon::now()->toDateString('Y-m-d H:i:s') }} 為止，共{{ $enroll->club->count_enrolls() }}人
                        </td>
                    </tr>
                </table>
                報名時填寫內容如下：
                <table class="w-full py-4 text-left font-normal">
                    @if ($enroll->club->has_lunch)
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            午餐選擇
                        </th>
                        <td class="p-2">
                            {{ ($enroll->need_lunch == 0) ? '自理' : '' }}{{ ($enroll->need_lunch == 1) ? '葷食' : '' }}{{ ($enroll->need_lunch == 2) ? '素食' : '' }}
                        </td>
                    </tr>
                    @endif
                    @if ($enroll->club->self_defined)
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            自選上課日
                        </th>
                        <td class="p-2">
                            每週{{ $enroll->weekday }}
                        </td>
                    </tr>
                    @endif
                    @if ($enroll->identity > 0)
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            身份註記
                        </th>
                        <td class="p-2">
                            {{ ($enroll->identity == 1) ? '低收入戶' : '' }}{{ ($enroll->identity == 2) ? '身心障礙' : '' }}
                        </td>
                    </tr>
                    @endif
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            聯絡人
                        </th>
                        <td class="p-2">
                            {{ $enroll->parent }}
                        </td>
                    </tr>
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            連絡信箱
                        </th>
                        <td class="p-2">
                            {{ $enroll->email }}
                        </td>
                    </tr>
                    <tr class="bg-gray-300 dark:bg-gray-500">
                        <th scope="row" class="p-2">
                            聯絡電話
                        </th>
                        <td class="p-2">
                            {{ $enroll->mobile }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="m-5 border-red-500 bg-red-100 dark:bg-red-700 border-b-2" role="alert">
                這是系統自動寄發的電子郵件，請勿直接回覆！若您並未報名，請忽略此信件！
            </div>
        </div>
    </div>
</body>
</html>