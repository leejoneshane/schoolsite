@extends('layouts.main')

@section('content')
    <div class="text-2xl font-bold leading-normal pb-5">
        教師午餐登記
        <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('lunch') }}">
            <i class="fa-solid fa-eject"></i>返回上一頁
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
        <form action="{{ route('lunch.teacher.store') }}" method="POST">
            @csrf
            <input type="hidden" name="section" value="{{ $section }}">

            <!-- Identity -->
            <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-gray-700">身份確認</h3>
            <div class="mb-6">
                <label class="inline-flex items-center cursor-pointer mr-6">
                    <input type="checkbox" name="tutor" value="1"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        {{ ($teacher && $teacher->tutor) ? 'checked' : '' }}>
                    <span class="ml-2">我是導師（隨班用餐）</span>
                </label>
            </div>

            <!-- Meal Preferences -->
            <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-gray-700">飲食偏好</h3>
            <div class="mb-6 flex gap-6">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="vegen" value="1"
                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                        {{ ($teacher && $teacher->vegen) ? 'checked' : '' }}>
                    <span class="ml-2">素食</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="milk" value="1"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        {{ ($teacher && $teacher->milk) ? 'checked' : '' }}>
                    <span class="ml-2">飲用牛奶（若未勾選，一律飲用豆奶）</span>
                </label>
            </div>

            <!-- Weekly Schedule -->
            <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-gray-700">用餐時間與地點</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="p-2 border-b dark:border-gray-600 w-24">星期</th>
                            <th class="p-2 border-b dark:border-gray-600 w-24">用餐</th>
                            <th class="p-2 border-b dark:border-gray-600">供餐地點</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = ['一', '二', '三', '四', '五'];
                            $en_days = [0, 1, 2, 3, 4];
                        @endphp
                        @foreach($days as $index => $day)
                            <tr>
                                <td class="p-2 border-b dark:border-gray-600 font-bold">星期{{ $day }}</td>
                                <td class="p-2 border-b dark:border-gray-600">
                                    <input type="checkbox" name="weekdays[{{ $index }}]" value="1"
                                        class="js-lunch-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        data-index="{{ $index }}" {{ ($teacher && isset($teacher->weekdays[$index]) && $teacher->weekdays[$index]) ? 'checked' : '' }}>
                                </td>
                                <td class="p-2 border-b dark:border-gray-600">
                                    @if(isset($fixed_days[$index]))
                                        <span class="text-gray-500 font-bold">隨班用餐</span>
                                        <input type="hidden" name="places[{{ $index }}]" value="{{ $fixed_days[$index] }}">
                                    @else
                                        <select name="places[{{ $index }}]"
                                            class="js-lunch-select rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 w-full max-w-xs"
                                            data-index="{{ $index }}">
                                            <option value="">請選擇地點</option>
                                            @foreach($cafeterias as $cafeteria)
                                                <option value="{{ $cafeteria->id }}" {{ ($teacher && isset($teacher->places[$index]) && $teacher->places[$index] == $cafeteria->id) ? 'selected' : '' }}>
                                                    {{ $cafeteria->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition font-bold">
                    儲存設定
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selects = document.querySelectorAll('.js-lunch-select');
            selects.forEach(select => {
                select.addEventListener('change', function () {
                    const index = this.dataset.index;
                    const checkbox = document.querySelector(`.js-lunch-checkbox[data-index="${index}"]`);
                    if (checkbox) {
                        if (this.value !== "") {
                            checkbox.checked = true;
                        } else {
                            checkbox.checked = false;
                        }
                    }
                });
            });
        });
    </script>
@endsection