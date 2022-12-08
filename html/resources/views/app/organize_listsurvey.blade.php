<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="h-screen">
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    {{ $teacher->realname . $year }}學年度意願調查表
    <button class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600">
        <i class="fa-solid fa-xmark"></i>關閉視窗
    </button>
</div>
@if ($survey)
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">年齡：</span>
    {{ $survey->age }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">教學經歷：</span>
    {{ $survey->exprience }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">最高學歷：</span>
    @if ($survey->edu_level == 0)
    研究所畢業(博士)
    @elseif ($survey->edu_level == 1)
    研究所畢業(碩士)
    @elseif ($survey->edu_level == 2)
    研究所四十學分班結業
    @elseif ($survey->edu_level == 3)
    師大及教育學院畢業
    @elseif ($survey->edu_level == 4)
    大學院校教育院系畢業
    @elseif ($survey->edu_level == 5)
    大學院校一般系科畢業(有教育學分)
    @elseif ($survey->edu_level == 6)
    大學院校一般系科畢業(無修習教育學分)
    @elseif ($survey->edu_level == 7)
    師範專科畢業
    @elseif ($survey->edu_level == 8)
    其他專科畢業
    @elseif ($survey->edu_level == 9)
    師範學校畢業
    @elseif ($survey->edu_level == 10)
    軍事學校畢業
    @elseif ($survey->edu_level == 11)
    其他
    @endif
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">畢業學校及科系：</span>
    {{ $survey->edu_school }} {{ $survey->edu_division }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">年資積分：</span>
    {{ $survey->score }}
</div>
@php
    $first = $second = $third = '';
    foreach ($stage1->general as $v) {
        if ($survey->admin1 == $v->id) $first = $v->name;
        if ($survey->admin2 == $v->id) $second = $v->name;
        if ($survey->admin3 == $v->id) $third = $v->name;
    }
@endphp
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">行政職務意願：</span>
    @if (!empty($first))
    1.{{ $first }}　
    @endif
    @if (!empty($second))
    2.{{ $second }}　
    @endif
    @if (!empty($third))
    3.{{ $third }}　
    @endif
</div>
@php
    $specials = [];
    foreach ($stage1->special as $v) {
        if (in_array($v->id, $survey->special)) $specials[] = $v->name;
    }
    foreach ($stage2->special as $v) {
        if (in_array($v->id, $survey->special)) $specials[] = $v->name;
    }
@endphp
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">特殊任務意願：</span>
    @foreach ($specials as $s)
    {{ $s }}　
    @endforeach
</div>
@php
    $first = $second = $third = $four = $five = $six = '';
    foreach ($stage2->general as $v) {
        if ($survey->teach1 == $v->id) $first = $v->name;
        if ($survey->teach2 == $v->id) $second = $v->name;
        if ($survey->teach3 == $v->id) $third = $v->name;
        if ($survey->teach4 == $v->id) $four = $v->name;
        if ($survey->teach5 == $v->id) $five = $v->name;
        if ($survey->teach6 == $v->id) $six = $v->name;
    }
@endphp
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">級科任意願：</span>
    @if (!empty($first))
    1.{{ $first }}　
    @endif
    @if (!empty($second))
    2.{{ $second }}　
    @endif
    @if (!empty($third))
    3.{{ $third }}　
    @endif
    @if (!empty($four))
    4.{{ $four }}　
    @endif
    @if (!empty($five))
    5.{{ $five }}　
    @endif
    @if (!empty($six))
    6.{{ $six }}　
    @endif
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">希望任教年段：</span>
    @if ($survey->grade == 1)
    低年級
    @elseif ($survey->grade == 2)
    中年級
    @elseif ($survey->grade == 3)
    高年級
    @endif
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">超鐘點意願：</span>
    @if ($survey->overcome)
    同意
    @else
    無意願
    @endif
</div>
@endif
</body>
</html>