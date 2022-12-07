<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="h-screen">
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    {{ $teacher->realname }}意願調查表
    <button class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600">
        <i class="fa-solid fa-xmark"></i>關閉視窗
    </button>
</div>
<div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">壹、基本資料</div>
<div class="p-2">
    <label class="text-indigo-700 dark:text-indigo-200">教學經歷：
        {{ $teacher->survey->exprience }}
    </label>
</div>
<div class="p-2">
    <label class="text-indigo-700 dark:text-indigo-200">最高學歷：
        @if ($teacher->survey->edu_level == 0)
        研究所畢業(博士)
        @elseif ($teacher->survey->edu_level == 1)
        研究所畢業(碩士)
        @elseif ($teacher->survey->edu_level == 2)
        研究所四十學分班結業
        @elseif ($teacher->survey->edu_level == 3)
        師大及教育學院畢業
        @elseif ($teacher->survey->edu_level == 4)
        大學院校教育院系畢業
        @elseif ($teacher->survey->edu_level == 5)
        大學院校一般系科畢業(有教育學分)
        @elseif ($teacher->survey->edu_level == 6)
        大學院校一般系科畢業(無修習教育學分)
        @elseif ($teacher->survey->edu_level == 7)
        師範專科畢業
        @elseif ($teacher->survey->edu_level == 8)
        其他專科畢業
        @elseif ($teacher->survey->edu_level == 9)
        師範學校畢業
        @elseif ($teacher->survey->edu_level == 10)
        軍事學校畢業
        @elseif ($teacher->survey->edu_level == 11)
        其他
        @endif
    </label>
</div>
<div class="p-2">
    <label class="text-indigo-700 dark:text-indigo-200">畢業學校：
        {{ $teacher->survey->edu_school }}
    </label>
    <label class="text-indigo-700 dark:text-indigo-200">畢業科系：
        {{ $teacher->survey->edu_division }}
    </label>
</div>
<div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">貳、年資積分</div>
<label class="text-indigo-700 dark:text-indigo-200">校內外積分合計：
    {{ $teacher->survey->score }}
</label>
<div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">叁、行政職務意願</div>
<div class="p-2">
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第一志願：
        @foreach ($stage1->general as $v)
            @if ($teacher->survey->admin1 == $v->id)
            {{ $v->name }}
            @endif
        @endforeach
    </label>
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第二志願：
            @foreach ($stage1->general as $v)
                @if ($teacher->survey->admin2 == $v->id)
                {{ $v->name }}
                @endif
            @endforeach
    </label>
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第三志願：
            @foreach ($stage1->general as $v)
                @if ($teacher->survey->admin3 == $v->id)
                {{ $v->name }}
                @endif
            @endforeach
    </label>
</div>
<div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">肆、特殊任務意願</div>
<div class="p-2">
    @foreach ($stage1->special as $s)
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">
        @if ($teacher->survey->special && in_array($s->id, $teacher->survey->special))
        {{ $s->name }}
        @endif
    </label>
    @endforeach
    @foreach ($stage2->special as $s)
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">
        @if ($teacher->survey->special && in_array($s->id, $teacher->survey->special))
        {{ $s->name }}
        @endif
    </label>
    @endforeach
</div>
<div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">伍、級科任意願</div>
<div class="p-2">
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第一志願：
        @foreach ($stage2->general as $v)
            @if ($teacher->survey->teach1 == $v->id)
            {{ $v->name }}
            @endif
        @endforeach
    </label>
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第二志願：
        @foreach ($stage2->general as $v)
            @if ($teacher->survey->teach2 == $v->id)
            {{ $v->name }}
            @endif
        @endforeach
    </label>
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第三志願：
        @foreach ($stage2->general as $v)
            @if ($teacher->survey->teach3 == $v->id)
            {{ $v->name }}
            @endif
        @endforeach
    </label>
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第四志願：
        @foreach ($stage2->general as $v)
            @if ($teacher->survey->teach4 == $v->id)
            {{ $v->name }}
            @endif
        @endforeach
    </label>
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第五志願：
        @foreach ($stage2->general as $v)
            @if ($teacher->survey->teach5 == $v->id)
            {{ $v->name }}
            @endif
        @endforeach
    </label>
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">第六志願：
        @foreach ($stage2->general as $v)
            @if ($teacher->survey->teach6 == $v->id)
            {{ $v->name }}
            @endif
        @endforeach
    </label>
</div>
<div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">陸、無缺額時，希望任教年段</div>
<div class="p-2">
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">
        @if ($teacher->survey->grade == 1)
        低年級
        @elseif ($teacher->survey->grade == 2)
        中年級
        @elseif ($teacher->survey->grade == 3)
        高年級
        @endif
    </label>
</div>
<div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">柒、超鐘點意願</div>
<div class="p-2">
    <label class="pr-6 text-indigo-700 dark:text-indigo-200">
        @if ($teacher->survey->overcome)
        同意
        @else
        無意願
        @endif
    </label>
</div>
</body>
</html>