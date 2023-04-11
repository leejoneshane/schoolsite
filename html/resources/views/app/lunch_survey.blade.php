@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    午餐調查表
    @if ($manager && $section == current_section())
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('lunch.config') }}">
        <i class="fa-regular fa-clock"></i>設定調查期程
    </a>
    @endif
    @if ($user->user_type == 'Teacher' || $manager)
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('lunch.download') }}">
        <i class="fa-solid fa-file-export"></i>匯出調查結果
    </a>
    @endif
</div>
<label for="section">請選擇學期：</label>
<select id="section" class="inline w-32 p-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
    onchange="
    var section = this.value;
    window.location.replace('{{ route('lunch') }}' + '/' + section);
    ">
    @foreach ($sections as $s)
    <option value="{{ $s }}"{{ ($s == $section) ? ' selected' : '' }}>{{ substr($s, 0, -1) . '學年第' . substr($s, -1) . '學期' }}</option>
    @endforeach
</select>
<div class="flex flex-col justify-center">
@if (empty($settings) || time() < strtotime($settings->survey_at))
<div class="w-full border-red-500 bg-red-100 dark:bg-red-700 border-b-2 mb-5" role="alert">
    <p>午餐調查尚未開始，請稍候再試或聯絡學務處處理！</p>
</div>
@elseif ($user->user_type == 'Student')
    @if (time() > strtotime($settings->expired_at))
    <div class="w-full border-green-500 bg-green-100 dark:bg-green-700 border-b-2 mb-5" role="alert">
        @if ($survey)
        <p>
            @if ($survey->by_school)
            我要參加學校營養午餐，餐費：{{ $settings->money }}元/日，{{ $survey->lunch_type }}。
            @elseif ($survey->by_parent)
            我不參加學校營養午餐，由家長親送午餐！
            @elseif ($survey->boxed_meal)
            我不參加學校營養午餐，將自備便當並使用學校蒸飯設備！
            @endif
        </p>
        @else
        <p>午餐調查已經結束！</p>
        @endif
    </div>
    @else
    <table class="p-3 w-3/4">
        <tr>
            <td class="text-base">{!! $settings->description !!}</td>
        </tr>
        <tr>
            <td>-----------------------------------------------------------------------------------------------------------</td>
        </tr>
        <tr><td>
        <form id="lunch-survey" action="{{ route('lunch.survey') }}" method="POST">
        @csrf
        <div class="p-3">
            <label for="school" class="inline-flex relative items-center cursor-pointer">
                <input type="radio" id="school" name="meal" value="by_school" class="sr-only peer" onchange="show(this)"{{ ($survey && $survey->by_school) ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-xl font-bold text-gray-900 dark:text-gray-300">參加學校營養午餐</span>
            </label>
            <div id="by_school" class="{{ ($survey && $survey->by_school) ? '' : 'hidden' }}">
                <div class="p-3">餐費：{{ $settings->money }}元/日，費用於開學後發放三聯單後繳交。</div>
                <div class="p-3">午　餐：　　
                    <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                    type="radio" name="type" value="meat"{{ ($survey && !($survey->vegen)) ? ' checked' : '' }}>葷食　　
                    <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                    type="radio" name="type" value="vegen"{{ ($survey && $survey->vegen) ? ' checked' : '' }}>素食
                </div>
                <div class="p-3">能否飲用乳製品？　　
                    <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                    type="radio" name="milk" value="yes"{{ ($survey && $survey->milk) ? ' checked' : '' }}>是　　
                    <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                    type="radio" name="milk" value="no"{{ ($survey && !($survey->milk)) ? ' checked' : '' }}>否，水果取代鮮奶（乳糖不耐症學童可選擇水果）
                </div>
            </div>
        </div>
        <div class="p-3">
            <label for="parent" class="inline-flex relative items-center cursor-pointer">
                <input type="radio" id="parent" name="meal" value="by_parent" class="sr-only peer" onchange="show(this)"{{ ($survey && $survey->by_parent) ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-xl font-bold text-gray-900 dark:text-gray-300">不參加學校營養午餐，由家長親送！</span>
            </label>
            <div id="by_parent" class="hidden"{{ ($survey && $survey->by_parent) ? '' : 'hidden' }}>
                <div class="p-3">請家長親送至學校正門口警衛室，並<span class="font-bold">標註班級、姓名</span>。</div>
            </div>
        </div>
        <div class="p-3">
            <label for="boxed" class="inline-flex relative items-center cursor-pointer">
                <input type="radio" id="boxed" name="meal" value="boxed_meal" class="sr-only peer" onchange="show(this)"{{ ($survey && $survey->boxed_meal) ? ' checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-xl font-bold text-gray-900 dark:text-gray-300">不參加學校營養午餐，使用學校蒸飯設備！</span>
            </label>
            <div id="boxed_meal" class="hidden"{{ ($survey && $survey->boxed_meal) ? '' : 'hidden' }}>
                <div class="p-3">請家長自備<span class="font-bold">不鏽鋼材質耐高溫 150度C 以上</span>之便當盒，需有扣環且完整密封包裝不致鬆脫，並<span class="font-bold">標註班級姓名</span>，蒸飯設備採高溫蒸氣加熱，恕不接受<span class="font-bold">玻璃或塑膠製品</span>。</div>
            </div>
        </div>
        <div class="p-3">
            備　註：本校依規定禁用一次性餐具，凡訂購學校營養午餐或自行送餐者，皆需<span class="font-bold">自備便當盒、湯碗和餐具</span>。
        </div>
        <div class="inline p-6">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                提交
            </button>
        </div>
        </form>
        </td></tr>
    </table>
    @endif
@else
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>已調查班級數：{{ $count->classes }}　累計調查人數：{{ $count->students }}</p>
</div>
{{ $surveys->links('pagination::tailwind') }}
<table class="border border-1 p-3 w-3/4">
    <tr>
        <th scope="col" rowspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">班級</th>
        <th scope="col" rowspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">座號</th>
        <th scope="col" rowspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">姓名</th>
        <th scope="col" colspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">參加午餐</th>
        <th scope="col" colspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">乳品</th>
        <th scope="col" colspan="2" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">不參加午餐</th>
    </tr>
    <tr>
        <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">葷食</th>
        <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">素食</th>
        <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">要飲用</th>
        <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">改成水果</th>
        <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">家長親送</th>
        <th scope="col" class="border border-1 text-sm font-bold bg-gray-100 dark:bg-gray-500">蒸飯設備</th>
    </tr>
    @foreach ($surveys as $s)
    <tr>
        <td class="border border-1 text-sm text-center">{{ $s->student->classroom->name }}</td>
        <td class="border border-1 text-sm text-center">{{ $s->student->seat }}</td>
        <td class="border border-1 text-sm text-center">{{ $s->student->realname }}</td>
        <td class="border border-1 text-sm text-center">{!! ($s->by_school && !($s->vegen)) ? '<i class="fa-solid fa-check"></i>' : ''  !!}</td>
        <td class="border border-1 text-sm text-center">{!! ($s->by_school && $s->vegen) ? '<i class="fa-solid fa-check"></i>' : ''  !!}</td>
        <td class="border border-1 text-sm text-center">{!! ($s->by_school && $s->milk) ? '<i class="fa-solid fa-check"></i>' : ''  !!}</td>
        <td class="border border-1 text-sm text-center">{!! ($s->by_school && !($s->milk)) ? '<i class="fa-solid fa-check"></i>' : ''  !!}</td>
        <td class="border border-1 text-sm text-center">{!! ($s->by_parent) ? '<i class="fa-solid fa-check"></i>' : ''  !!}</td>
        <td class="border border-1 text-sm text-center">{!! ($s->boxed_meal) ? '<i class="fa-solid fa-check"></i>' : ''  !!}</td>
    </tr>
    @endforeach
</table>
{{ $surveys->links('pagination::tailwind') }}
@endif
<div>
<script>
    function show(elem) {
        var target1 = document.getElementById('by_school');
        var target2 = document.getElementById('by_parent');
        var target3 = document.getElementById('boxed_meal');
        if (elem.value == 'by_school') {
            if (elem.checked) {
                target1.classList.remove('hidden');
                target2.classList.add('hidden');
                target3.classList.add('hidden');
            }
        }
        if (elem.value == 'by_parent') {
            if (elem.checked) {
                target1.classList.add('hidden');
                target2.classList.remove('hidden');
                target3.classList.add('hidden');
            }
        }
        if (elem.value == 'boxed_meal') {
            if (elem.checked) {
                target1.classList.add('hidden');
                target2.classList.add('hidden');
                target3.classList.remove('hidden');
            }
        }
    }
</script>
@endsection