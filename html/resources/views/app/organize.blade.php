@extends('layouts.main')

@section('content')
<div class="text-slate-500 text-gray-500 text-zinc-500 text-neutral-500 text-stone-500 text-red-500 text-orange-500 text-amber-500 text-yellow-500 text-lime-500 text-green-500 text-emerald-500 text-teal-500 text-cyan-500 text-sky-500 text-blue-500 text-indigo-500 text-violet-500 text-purple-500 text-fuchsia-500 text-pink-500 text-rose-500"></div>
<div class="text-2xl font-bold leading-normal pb-5">
    職務編排
    @if ($year == $current)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.vacancy') }}">
        <i class="fa-solid fa-file-import"></i>職缺設定
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.setting') }}">
        <i class="fa-solid fa-file-import"></i>流程控制
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.arrange') }}">
        <i class="fa-solid fa-file-import"></i>職務編排
    </a>
    @endif
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listvacancy', ['year' => $year]) }}">
        <i class="fa-solid fa-file-export"></i>職缺一覽表
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listresult', ['year' => $year]) }}">
        <i class="fa-solid fa-file-export"></i>職編結果一覽表
    </a>
</div>
<div class="w-full">
    <label for="years">請選擇學年度：</label>
    <select id="years" class="inline w-16 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
        onchange="
        var year = this.value;
        window.location.replace('{{ route('organize') }}' + '/' + year);
        ">
        @foreach ($years as $y)
        <option value="{{ $y }}"{{ ($y == $year) ? ' selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
    <span class="p-2">目前進度：
        @if (!$flow)
        <span class="text-red-700">尚未設定時程，無法填寫意願！</span>
        @else
        <span class="text-green-700">
        {{ ($flow->onSurvey()) ? '寫學經歷資料、年資積分' : '' }}
        {{ ($flow->onFirstStage()) ? '行政與特殊任務意願調查（第一階段）' : '' }}
        {{ ($flow->onPause()) ? '第一階段意願調查已經結束，請等候第二階段意願調查！' : '' }}
        {{ ($flow->onSecondStage()) ? '級科任意願調查（第二階段）' : '' }}
        {{ ($flow->onFinish()) ? '意願調查已經結束！' : '' }}
        </span>
        @endif
    </span>
</div>
@if ($flow && ($flow->onSurvey() || $flow->onFirstStage() || $flow->onSecondStage()))
    @if ($reserved)
<div class="w-full p-4 text-center text-3xl font-semibold">
    您的職務並未開缺，無需填寫意願調查表！
</div>
    @else
<div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">壹、基本資料</div>
<form action="{{ route('organize.survey', ['uuid' => $teacher->uuid]) }}" method="POST">
    <div class="p-2">
        <label for="exp" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">教學經歷：
            <textarea id="exp" name="exp" class="w-120">{{ $teacher->survey->exprience }}</textarea>
        </label>
    </div>
    <div class="p-2">
        <label for="edu_level" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">最高學歷：
            <select name="edu_level">
                <option value="0"{{ ($teacher->survey->edu_level == 0) ? ' selected' : '' }}>研究所畢業(博士)</option>
                <option value="1"{{ ($teacher->survey->edu_level == 1) ? ' selected' : '' }}>研究所畢業(碩士)</option>
                <option value="2"{{ ($teacher->survey->edu_level == 2) ? ' selected' : '' }}>研究所四十學分班結業</option>
                <option value="3"{{ ($teacher->survey->edu_level == 3) ? ' selected' : '' }}>師大及教育學院畢業</option>
                <option value="4"{{ ($teacher->survey->edu_level == 4) ? ' selected' : '' }}>大學院校教育院系畢業</option>
                <option value="5"{{ ($teacher->survey->edu_level == 5) ? ' selected' : '' }}>大學院校一般系科畢業(有教育學分)</option>
                <option value="6"{{ ($teacher->survey->edu_level == 6) ? ' selected' : '' }}>大學院校一般系科畢業(無修習教育學分)</option>
                <option value="7"{{ ($teacher->survey->edu_level == 7) ? ' selected' : '' }}>師範專科畢業</option>
                <option value="8"{{ ($teacher->survey->edu_level == 8) ? ' selected' : '' }}>其他專科畢業</option>
                <option value="9"{{ ($teacher->survey->edu_level == 9) ? ' selected' : '' }}>師範學校畢業</option>
                <option value="10"{{ ($teacher->survey->edu_level == 10) ? ' selected' : '' }}>軍事學校畢業</option>
                <option value="11"{{ ($teacher->survey->edu_level == 11) ? ' selected' : '' }}>其他</option>
            </select>
        </label>
    </div>
    <div class="p-2">
        <label for="edu_school" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">畢業學校：
            <input id="edu_school" name="edu_school" type="text" value="{{ $teacher->survey->edu_school }}" required>
        </label>
        <label for="edu_division" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">畢業科系：
            <input id="edu_division" name="edu_division" type="text" value="{{ $teacher->survey->division }}" required>
        </label>
    </div>
    <div class="text-orange-700 dark:text-orange-200">教學經歷，請填寫您在本校任職期間擔任各項職務的確切年份及累計年資。</div>
    <div class="text-orange-700 dark:text-orange-200">如有意願擔任領域教師，請填寫領域研習時數、獲獎紀錄、證照或其他專長認定文件。</div>

    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">貳、年資積分</div>
    @php
    $school_year = $teacher->seniority->new_school_year;
    if ($school_year < 1) $school_year = $teacher->seniority->school_year;
    $school_month = $teacher->seniority->new_school_month;
    if ($school_month < 1) $school_month = $teacher->seniority->school_month;
    $teach_year = $teacher->seniority->new_teach_year;
    if ($teach_year < 1) $teach_year = $teacher->seniority->teach_year;
    $teach_month = $teacher->seniority->new_teach_month;
    if ($teach_month < 1) $teach_month = $teacher->seniority->teach_month;
    $score = $teacher->seniority->newscore;
    if ($score < 1) $score = $teacher->seniority->score;
    if (!empty($teacher->tutor_class)) {
        $grade = substr($teacher->tutor_class, 0, 1); 
        if ($grade == '5' || $grade == '6') $high = true;
        $total = $score + 2.1;
    } else {
        $hign = false;
        $total = $score;
    }
    @endphp
    <input type="hidden" id="default" value="{{ $score }}">
    <table>
        <tr>
            <td class="w-16 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">本校資歷</td>
            <td style="w-24">
                <input name="in" type="text" size="2" value={{ $school_year }}" readonly>年
                <input name="in" type="text" size="2" value="{{ $school_month }}" readonly>月 X 0.7
            </td>
            <td style="w-8">+</td>
            <td>
                <label for="highgrade" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">
                    <input id="highgrade" name="highgrade" type="checkbox"{{ ($high) ? ' checked' : ' disabled' }}>
                    連續任滿高年級六年以上 2.1
                </label>
            </td>
            <td rowspan="2" class="w-8">=</td>
            <td rowspan="2" style="w-32">
                <label for="highgrade" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">年資積分：
                    <input id="total" name="total" size="2" value="{{ $total }}" readonly>分
                </label>
            </td>
        </tr>
        <tr>
            <td class="w-16 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">外校資歷</td>
            <td colspan="3">
                <input name="out" type="text" size="2" value="{{ $teach_year }}" readonly>年
                <input name="out" type="text" size="2" value="{{ $teach_month }}" readonly>月 X 0.3
            </td>
        </tr>
    </table>

    @if ($flow->onFirstStage())
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">叁、行政職務意願</div>
        @if ($stage1->general->isEmpty())
    <div class="p-2">職缺尚未設定，請洽教務處詢問！</div>
        @else
    <div class="p-2">
        <select id="admins" class="hidden w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option></option>
            @foreach ($stage1->general as $v)
            <option value="{{ $v->id }}">{{ $v->name }}</option>
            @endforeach
        </select>
        <label for="admin1" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第一志願：
            <select id="admin1" name="admin1" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage1->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->admin1 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="admin2" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第二志願：
            <select id="admin2" name="admin2" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage1->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->admin2 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="admin3" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第三志願：
            <select id="admin3" name="admin3" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage1->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->admin3 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="text-orange-700 dark:text-orange-200">括弧內為已表達意願的人數。</div>
        @endif
        @if ($stage1->special->isNotEmpty())
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">肆、特殊任務意願</div>
    <div class="p-2">
            @foreach ($stage1->special as $s)
        <label for="specials[]" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">
            <input name="specials[]" type="checkbox" value="{{ $s->id }}"{{ (in_array($s->id, $teacher->survey->specials)) ? ' checked' : '' }}>
            {{ $s->name }}
        </label>
            @endforeach
    </div>
    <div class="text-orange-700 dark:text-orange-200">括弧內為已表達意願的人數。</div>
        @endif
    <div class="flex justify-center">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            填好了，請幫我提交
        </button>
    </div>
    @endif
    @if ($flow->onSecondStage())
        @if ($stage2->special->isNotEmpty())
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">肆、特殊任務意願</div>
    <div class="p-2">
            @foreach ($stage2->special as $s)
        <label for="specials[]" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">
            <input name="specials[]" type="checkbox" value="{{ $s->id }}"{{ (in_array($s->id, $teacher->survey->specials)) ? ' checked' : '' }}>
            {{ $s->name }}
        </label>
            @endforeach
    </div>
    <div class="text-orange-700 dark:text-orange-200">括弧內為已表達意願的人數。</div>
        @endif
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">伍、級科任意願</div>
        @if ($stage2->general->isEmpty())
    <div class="p-2">職缺尚未設定，請洽教務處詢問！</div>
        @else
    <div class="p-2">
        <select id="teachs" class="hidden w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
            <option></option>
            @foreach ($stage2->general as $v)
            <option value="{{ $v->id }}">{{ $v->name }}</option>
            @endforeach
        </select>
        <label for="teach1" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第一志願：
            <select id="teach1" name="teach1" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->teach1 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="teach2" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第一志願：
            <select id="teach2" name="teach2" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->teach2 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="teach3" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第一志願：
            <select id="teach3" name="teach3" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->teach3 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="teach4" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第一志願：
            <select id="teach4" name="teach4" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->teach4 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="teach5" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第一志願：
            <select id="teach5" name="teach5" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->teach5 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="teach6" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">第一志願：
            <select id="teach6" name="teach6" class="inline w-32 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($teacher->survey->teach6 == $v) ? ' selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="text-orange-700 dark:text-orange-200">請老師選擇級任3個意願、科任3個意願，依志願序選填，括弧內為已表達意願的人數。</div>
        @endif
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">陸、無法如願以償時，希望任教年段</div>
    <div class="p-2">
        <label for="grade" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">
            <input name="grade" type="radio" value="1"{{ ($teacher->survey->grade == 1) ? ' checked' : '' }}>
            低年級
        </label>
        <label for="grade" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">
            <input name="grade" type="radio" value="2"{{ ($teacher->survey->grade == 2) ? ' checked' : '' }}>
            中年級
        </label>
        <label for="grade" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">
            <input name="grade" type="radio" value="3"{{ ($teacher->survey->grade == 3) ? ' checked' : '' }}>
            高年級
        </label>
    </div>
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">柒、超鐘點意願</div>
    <div class="p-2">
        <label for="overcome" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">
            <input name="overcome" type="radio" value="1"{{ ($teacher->survey->grade == 1) ? ' checked' : '' }}>
            同意
        </label>
        <label for="overcome" class="text-lg text-indigo-700 dark:text-indigo-200 font-semibold">
            <input name="overcome" type="radio" value="0"{{ ($teacher->survey->grade == 0) ? ' checked' : '' }}>
            無意願
        </label>
    </div>
    <div class="flex justify-center">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            填好了，請幫我提交
        </button>
    </div>
    @endif
</form>
    @endif
<script>
window.onload = function () {
    document.getElementById("highgrade").addEventListener("click", () => { 
        total = document.getElementById('default').value;
        if (this.checked) {
            document.getElementById('total').value = total + 2.1;
        } else {
            document.getElementById('total').value = total;
        }
    });
    document.getElementById("admin1").addEventListener("click", "seladmin");
    document.getElementById("admin2").addEventListener("click", "seladmin");
    document.getElementById("teach1").addEventListener("click", "selteach");
    document.getElementById("teach2").addEventListener("click", "selteach");
    document.getElementById("teach3").addEventListener("click", "selteach");
    document.getElementById("teach4").addEventListener("click", "selteach");
    document.getElementById("teach5").addEventListener("click", "selteach");
};

function seladmin() {
    var admins = document.getElementById("admins").children;
    var admin1 = document.getElementById("admin1");
    var admin2 = document.getElementById("admin2");
    var admin3 = document.getElementById("admin3");
    for(var i=0; i<admins.length; i++){
        child2 = admin2.children[i];
        child3 = admin3.children[i];
        if (child2.value == admins[i].value) {
            if (child2.value == admin1.value) {
                admin2.removeChild(child2);
            }
        } else {
            if (child2.value != admin1.value) {
                var node = admins[i].cloneNode();
                admin2.insertBrfore(node, child2);
            }
        }
        if (child3.value == admins[i].value) {
            if (child3.value == admin1.value || child3.value == admin2.value) {
                admin3.removeChild(child3);
            }
        } else {
            if (child3.value != admin1.value && child3.value != admin2.value) {
                var node = admins[i].cloneNode();
                admin3.insertBrfore(node, child3);
            }
        }
    }
}

function selteach() {
    var teachs = document.getElementById("teachs").children;
    var teach1 = document.getElementById("teach1");
    var teach2 = document.getElementById("teach2");
    var teach3 = document.getElementById("teach3");
    var teach4 = document.getElementById("teach4");
    var teach5 = document.getElementById("teach5");
    var teach6 = document.getElementById("teach6");
    for(var i=0; i<teachs.length; i++){
        child2 = teach2.children[i];
        child3 = teach3.children[i];
        child4 = teach4.children[i];
        child5 = teach5.children[i];
        child6 = teach6.children[i];
        if (child2.value == teachs[i].value) {
            if (child2.value == teach1.value) {
                teach2.removeChild(child2);
            }
        } else {
            if (child2.value != teach1.value) {
                var node = teachs[i].cloneNode();
                teach2.insertBrfore(node, child2);
            }
        }
        if (child3.value == teachs[i].value) {
            if (child3.value == teach1.value || child3.value == teach2.value) {
                teach3.removeChild(child3);
            }
        } else {
            if (child3.value != teach1.value && child3.value != teach2.value) {
                var node = teachs[i].cloneNode();
                teach3.insertBrfore(node, child3);
            }
        }
        if (child4.value == teachs[i].value) {
            if (child4.value == teach1.value || child4.value == teach2.value || child4.value == teach3.value) {
                teach4.removeChild(child4);
            }
        } else {
            if (child4.value != teach1.value && child4.value != teach2.value && child4.value != teach3.value) {
                var node = teachs[i].cloneNode();
                teach4.insertBrfore(node, child4);
            }
        }
        if (child5.value == teachs[i].value) {
            if (child5.value == teach1.value || child5.value == teach2.value || child5.value == teach3.value || child5.value == teach4.value) {
                teach5.removeChild(child5);
            }
        } else {
            if (child5.value != teach1.value && child5.value != teach2.value && child5.value != teach3.value && child5.value != teach4.value) {
                var node = teachs[i].cloneNode();
                teach5.insertBrfore(node, child5);
            }
        }
        if (child6.value == teachs[i].value) {
            if (child6.value == teach1.value || child6.value == teach2.value || child6.value == teach3.value || child6.value == teach4.value || child6.value == teach5.value) {
                teach6.removeChild(child6);
            }
        } else {
            if (child6.value != teach1.value && child6.value != teach2.value && child6.value != teach3.value && child6.value != teach4.value && child6.value != teach5.value) {
                var node = teachs[i].cloneNode();
                teach6.insertBrfore(node, child6);
            }
        }
    }
}
</script>
@endif
@endsection
