@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    職務編排系統
    @if ($year == $current)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.vacancy') }}">
        <i class="fa-solid fa-chair"></i>職缺設定
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.setting') }}">
        <i class="fa-regular fa-calendar-days"></i>流程控制
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.arrange') }}">
        <i class="fa-solid fa-puzzle-piece"></i>職務編排
    </a>
    @endif
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listvacancy', ['year' => $year]) }}">
        <i class="fa-solid fa-square-poll-horizontal"></i>職缺一覽表
    </a>
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('organize.listresult', ['year' => $year]) }}">
        <i class="fa-solid fa-user-check"></i>職編結果一覽表
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
    <span class="p-2">
        @if (!$flow)
        <span class="text-red-700">尚未設定時程，請洽教務處詢問！</span>
        @elseif (!$teacher->seniority())
        <span class="text-red-700">尚未統計年資，請洽教務處詢問！</span>
        @else
        目前進度：
        <span class="text-green-700">
        {{ ($flow->notStart()) ? '意願調查尚未開始！' : '' }}
        {{ ($flow->onSurvey()) ? '填寫學經歷資料、年資積分' : '' }}
        {{ ($flow->onFirstStage()) ? '行政與特殊任務意願調查（第一階段）' : '' }}
        {{ ($flow->onPause()) ? '第一階段意願調查已經結束，請等候第二階段意願調查！' : '' }}
        {{ ($flow->onSecondStage()) ? '級科任意願調查（第二階段）' : '' }}
        {{ ($flow->onFinish()) ? '意願調查已經結束！' : '' }}
        </span>
        <span class="pl-4 text-green-500">
            {{ ($flow->onSurvey()) ? $flow->survey_at->format('Y-m-d') . '~' . $flow->first_stage->subDay()->format('Y-m-d') : '' }}
            {{ ($flow->onFirstStage()) ? $flow->first_stage->format('Y-m-d') . '~' . $flow->pause_at->subDay()->format('Y-m-d') : '' }}
            {{ ($flow->onPause()) ? $flow->pause_at->format('Y-m-d') . '~' . $flow->second_stage->subDay()->format('Y-m-d') : '' }}
            {{ ($flow->onSecondStage()) ? $flow->second_stage->format('Y-m-d') . '~' . $flow->close_at->format('Y-m-d') : '' }}
        </span>
        @endif
    </span>
</div>
@if ($year == $current && $teacher->seniority() && $flow && ($flow->onSurvey() || $flow->onFirstStage() || $flow->onSecondStage()))
    @if ($reserved)
<div class="w-full p-4 text-center text-3xl font-semibold">
    您的職務並未開缺，無需填寫意願調查表！
</div>
    @else
<form action="{{ route('organize.survey', ['uuid' => $teacher->uuid]) }}" method="POST">
    @csrf
    @if ($flow->onSurvey() || $flow->onFirstStage() || $flow->onSecondStage())
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">壹、基本資料</div>
    <div class="p-2">
        <label for="exp" class="text-indigo-700 dark:text-indigo-200">教學經歷：
            <textarea id="exp" name="exp" cols="80" rows="5">{{ ($survey) ? $survey->exprience : (($teacher->last_survey()) ? $teacher->last_survey()->exprience : '' ) }}</textarea>
        </label>
    </div>
    <div class="p-2">
        <label for="edu_level" class="text-indigo-700 dark:text-indigo-200">最高學歷：
            <select id="edu_level" name="edu_level">
                <option value="0"{{ ($survey && $survey->edu_level == 0) ? ' selected' : '' }}>研究所畢業(博士)</option>
                <option value="1"{{ ($survey && $survey->edu_level == 1) ? ' selected' : '' }}>研究所畢業(碩士)</option>
                <option value="2"{{ ($survey && $survey->edu_level == 2) ? ' selected' : '' }}>研究所四十學分班結業</option>
                <option value="3"{{ ($survey && $survey->edu_level == 3) ? ' selected' : '' }}>師大及教育學院畢業</option>
                <option value="4"{{ ($survey && $survey->edu_level == 4) ? ' selected' : '' }}>大學院校教育院系畢業</option>
                <option value="5"{{ ($survey && $survey->edu_level == 5) ? ' selected' : '' }}>大學院校一般系科畢業(有教育學分)</option>
                <option value="6"{{ ($survey && $survey->edu_level == 6) ? ' selected' : '' }}>大學院校一般系科畢業(無修習教育學分)</option>
                <option value="7"{{ ($survey && $survey->edu_level == 7) ? ' selected' : '' }}>師範專科畢業</option>
                <option value="8"{{ ($survey && $survey->edu_level == 8) ? ' selected' : '' }}>其他專科畢業</option>
                <option value="9"{{ ($survey && $survey->edu_level == 9) ? ' selected' : '' }}>師範學校畢業</option>
                <option value="10"{{ ($survey && $survey->edu_level == 10) ? ' selected' : '' }}>軍事學校畢業</option>
                <option value="11"{{ ($survey && $survey->edu_level == 11) ? ' selected' : '' }}>其他</option>
            </select>
        </label>
    </div>
    <div class="p-2">
        <label for="edu_school" class="text-indigo-700 dark:text-indigo-200">畢業學校：
            <input class="w-64" id="edu_school" name="edu_school" type="text" value="{{ $survey ? $survey->edu_school : (($teacher->last_survey()) ? $teacher->last_survey()->edu_school : '' ) }}" required>
        </label>
        <label for="edu_division" class="text-indigo-700 dark:text-indigo-200">畢業科系：
            <input class="w-64" id="edu_division" name="edu_division" type="text" value="{{ $survey ? $survey->edu_division : (($teacher->last_survey()) ? $teacher->last_survey()->edu_division : '' ) }}" required>
        </label>
    </div>
    <div class="p-2 text-orange-700 dark:text-orange-200">教學經歷，請填寫您在本校任職期間擔任各項職務的確切年份及累計年資。</div>
    <div class="p-2 text-orange-700 dark:text-orange-200">如有意願擔任領域教師，請填寫領域研習時數、獲獎紀錄、證照或其他專長認定文件。</div>

    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">貳、年資積分</div>
    <table>
        <tr>
            <td class="w-24 text-indigo-700 dark:text-indigo-200">本校資歷</td>
            <td class="w-52">
                <input name="in" type="text" size="2" value="{{ $score['syear'] }}" readonly>年
                <input name="in" type="text" size="2" value="{{ $score['smonth'] }}" readonly>月 ✖️ 0.7
            </td>
            <td class="w-8 p-4 text-lg font-semibold"> ➕ </td>
            <td>
                <label for="highgrade" class="inline-flex relative items-center cursor-pointer text-indigo-700 dark:text-indigo-200">
                    <input type="checkbox" id="highgrade" name="highgrade" value="yes" class="sr-only peer"{{ ($score['highgrade']) ? ($survey && $survey->high ? ' checked' : '') : ' disabled' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    連續任滿高年級六年以上 2.1
                </label>
            </td>
            <td rowspan="2" class="w-8 p-4 text-lg font-semibold"> 🟰 </td>
            <td rowspan="2">
                <label for="highgrade" class="text-indigo-700 dark:text-indigo-200">年資積分：
                    <input id="total" name="total" size="5" value="{{ $score['total'] }}" readonly>分
                </label>
            </td>
        </tr>
        <tr>
            <td class="w-24 text-indigo-700 dark:text-indigo-200">外校資歷</td>
            <td colspan="3">
                <input name="out" type="text" size="2" value="{{ $score['tyear'] }}" readonly>年
                <input name="out" type="text" size="2" value="{{ $score['tmonth'] }}" readonly>月 ✖️ 0.3
            </td>
        </tr>
    </table>
    @endif
    @if ($flow->onFirstStage())
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">叁、行政職務意願</div>
        @if ($stage1->general->isEmpty())
    <div class="p-2">職缺尚未設定，請洽教務處詢問！</div>
        @else
    <div class="p-2">
        <label for="admin1" class="pr-6 text-indigo-700 dark:text-indigo-200">第一志願：
            <select id="admin1" name="admin1" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage1->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->admin1 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('admin1') }}）</option>
                @endforeach
            </select>
        </label>
        <label for="admin2" class="pr-6 text-indigo-700 dark:text-indigo-200">第二志願：
            <select id="admin2" name="admin2" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage1->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->admin2 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('admin2') }}）</option>
                @endforeach
            </select>
        </label>
        <label for="admin3" class="pr-6 text-indigo-700 dark:text-indigo-200">第三志願：
            <select id="admin3" name="admin3" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage1->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->admin3 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('admin3') }}）</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="p-2 text-orange-700 dark:text-orange-200">括弧內為已表達意願的人數。</div>
        @endif
        @if ($stage1->special->isNotEmpty())
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">肆、特殊任務意願</div>
    <div class="p-2">
            @foreach ($stage1->special as $s)
        <label class="pr-6 text-indigo-700 dark:text-indigo-200">
            <input name="specials[]" type="checkbox" value="{{ $s->id }}"{{ ($survey && $survey->special && in_array($s->id, $survey->special)) ? ' checked' : '' }}>
            {{ $s->name }}（{{ $s->count_survey() }}）
        </label>
            @endforeach
    </div>
    <div class="p-2 text-orange-700 dark:text-orange-200">括弧內為已表達意願的人數。</div>
        @endif
    @endif
    @if ($flow->onSecondStage())
        @if ($stage2->special->isNotEmpty())
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">肆、特殊任務意願</div>
    <div class="p-2">
            @foreach ($stage2->special as $s)
        <label for="specials[]" class="pr-6 text-indigo-700 dark:text-indigo-200">
            <input name="specials[]" type="checkbox" value="{{ $s->id }}"{{ ($survey && $survey->special && in_array($s->id, $survey->special)) ? ' checked' : '' }}>
            {{ $s->name }}（{{ $s->count_survey() }}）
        </label>
            @endforeach
    </div>
    <div class="p-2 text-orange-700 dark:text-orange-200">括弧內為已表達意願的人數。</div>
        @endif
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">伍、級科任意願</div>
        @if ($stage2->general->isEmpty())
    <div class="p-2">職缺尚未設定，請洽教務處詢問！</div>
        @else
    <div class="p-2">
        <label for="teach1" class="pr-6 text-indigo-700 dark:text-indigo-200">第一志願：
            <select id="teach1" name="teach1" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->teach1 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('teach1') }}）</option>
                @endforeach
            </select>
        </label>
        <label for="teach2" class="pr-6 text-indigo-700 dark:text-indigo-200">第二志願：
            <select id="teach2" name="teach2" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->teach2 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('teach2') }}）</option>
                @endforeach
            </select>
        </label>
        <label for="teach3" class="pr-6 text-indigo-700 dark:text-indigo-200">第三志願：
            <select id="teach3" name="teach3" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->teach3 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('teach3') }}）</option>
                @endforeach
            </select>
        </label>
        <label for="teach4" class="pr-6 text-indigo-700 dark:text-indigo-200">第四志願：
            <select id="teach4" name="teach4" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->teach4 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('teach4') }}）</option>
                @endforeach
            </select>
        </label>
        <label for="teach5" class="pr-6 text-indigo-700 dark:text-indigo-200">第五志願：
            <select id="teach5" name="teach5" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->teach5 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('teach5') }}）</option>
                @endforeach
            </select>
        </label>
        <label for="teach6" class="pr-6 text-indigo-700 dark:text-indigo-200">第六志願：
            <select id="teach6" name="teach6" class="inline w-40 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                <option></option>
                @foreach ($stage2->general as $v)
                <option value="{{ $v->id }}"{{ ($survey && $survey->teach6 == $v->id) ? ' selected' : '' }}>{{ $v->name }}（{{ $v->count_survey('teach6') }}）</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="p-2 text-orange-700 dark:text-orange-200">請老師選擇級任3個意願、科任3個意願，依志願序選填，括弧內為已表達意願的人數。</div>
        @endif
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">陸、無缺額時，希望任教年段</div>
    <div class="p-2">
        <label class="pr-6 text-indigo-700 dark:text-indigo-200">
            <input name="grade" type="radio" value="1"{{ ($survey && $survey->grade == 1) ? ' checked' : '' }}>
            低年級
        </label>
        <label class="pr-6 text-indigo-700 dark:text-indigo-200">
            <input name="grade" type="radio" value="2"{{ ($survey && $survey->grade == 2) ? ' checked' : '' }}>
            中年級
        </label>
        <label class="pr-6 text-indigo-700 dark:text-indigo-200">
            <input name="grade" type="radio" value="3"{{ ($survey && $survey->grade == 3) ? ' checked' : '' }}>
            高年級
        </label>
    </div>
    <div class="py-4 text-lg text-indigo-700 dark:text-indigo-200 font-semibold">柒、超鐘點意願</div>
    <div class="p-2">
        <label class="pr-6 text-indigo-700 dark:text-indigo-200">
            <input name="overcome" type="radio" value="1"{{ ($survey && $survey->overcome) ? ' checked' : '' }}>
            同意
        </label>
        <label class="pr-6 text-indigo-700 dark:text-indigo-200">
            <input name="overcome" type="radio" value="0"{{ ($survey && !($survey->overcome)) ? ' checked' : '' }}>
            無意願
        </label>
    </div>
    @endif
    <div class="p-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            填好了，請幫我提交
        </button>
    </div>
</form>
    @endif
<script nonce="selfhost">
window.onload = function () {
    var elm = document.getElementById("highgrade");
    if (elm) {
        elm.addEventListener("click", () => {
            if (this.checked) {
                document.getElementById('total').value = '{{ $score['high'] }}';
            } else {
                document.getElementById('total').value = '{{ $score['org'] }}';
            }
        });
    }
    var elm = document.getElementById("admin1");
    if (elm) {
        elm.addEventListener("change", seladmin);
    }
    var elm = document.getElementById("admin2");
    if (elm) {
        elm.addEventListener("change", seladmin);
    }
    var elm = document.getElementById("teach1");
    if (elm) {
        elm.addEventListener("change", selteach);
    }
    var elm = document.getElementById("teach2");
    if (elm) {
        elm.addEventListener("change", selteach);
    }
    var elm = document.getElementById("teach3");
    if (elm) {
        elm.addEventListener("change", selteach);
    }
    var elm = document.getElementById("teach4");
    if (elm) {
        elm.addEventListener("change", selteach);
    }
    var elm = document.getElementById("teach5");
    if (elm) {
        elm.addEventListener("change", selteach);
    }
};

function seladmin() {
    var admin1 = document.getElementById("admin1");
    var admin2 = document.getElementById("admin2");
    var admin3 = document.getElementById("admin3");
    for(var i=0; i<admin1.children.length; i++){
        child2 = admin2.children[i];
        child3 = admin3.children[i];
        if (child2.value == admin1.value) {
            child2.setAttribute("disabled", "");
            if (admin2.value == child2.value) { admin2.value = ""; }
        } else {
            child2.removeAttribute("disabled");
        }
        if (child3.value == admin1.value || child3.value == admin2.value) {
            child3.setAttribute("disabled", "");
            if (admin3.value == child3.value) { admin3.value = ""; }
        } else {
            child3.removeAttribute("disabled");
        }
    }
}

function selteach() {
    var teach1 = document.getElementById("teach1");
    var teach2 = document.getElementById("teach2");
    var teach3 = document.getElementById("teach3");
    var teach4 = document.getElementById("teach4");
    var teach5 = document.getElementById("teach5");
    var teach6 = document.getElementById("teach6");
    for(var i=0; i<teach1.children.length; i++){
        child2 = teach2.children[i];
        child3 = teach3.children[i];
        child4 = teach4.children[i];
        child5 = teach5.children[i];
        child6 = teach6.children[i];
        if (child2.value == teach1.value) {
            child2.setAttribute("disabled", "");
            if (teach2.value == child2.value) { teach2.value = ""; }
        } else {
            child2.removeAttribute("disabled");
        }
        if (child3.value == teach1.value || child3.value == teach2.value) {
            child3.setAttribute("disabled", "");
            if (teach3.value == child3.value) { teach3.value = ""; }
        } else {
            child3.removeAttribute("disabled");
        }
        if (child4.value == teach1.value || child4.value == teach2.value || child4.value == teach3.value) {
            child4.setAttribute("disabled", "");
            if (teach4.value == child4.value) { teach4.value = ""; }
        } else {
            child4.removeAttribute("disabled");
        }
        if (child5.value == teach1.value || child5.value == teach2.value || child5.value == teach3.value || child5.value == teach4.value) {
            child5.setAttribute("disabled", "");
            if (teach5.value == child5.value) { teach5.value = ""; }
        } else {
            child5.removeAttribute("disabled");
        }
        if (child6.value == teach1.value || child6.value == teach2.value || child6.value == teach3.value || child6.value == teach4.value || child6.value == teach5.value) {
            child6.setAttribute("disabled", "");
            if (teach6.value == child6.value) { teach6.value = ""; }
        } else {
            child6.removeAttribute("disabled");
        }
    }
}
</script>
@endif
@endsection
