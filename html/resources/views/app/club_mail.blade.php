@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    通知報名家長
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs.admin', ['kid' => $club->kind_id]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            營隊全名
        </th>
        <th scope="col" class="p-2">
            招生年級
        </th>
        <th scope="col" class="p-2">
            指導教師
        </th>
        <th scope="col" class="p-2">
            授課地點
        </th>
        <th scope="col" class="p-2">
            上課時間
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2 {{ $club->kind->style }}">{{ $club->name }}</td>
        <td class="p-2">{{ $club->grade }}</td>
        <td class="p-2">{{ $club->section()->teacher }}</td>
        <td class="p-2">{{ $club->section()->location }}</td>
        <td class="p-2">{{ $club->section()->studytime }}</td>
    </tr>
</table>
<form id="mail-club" action="{{ route('clubs.mail', ['club_id' => $club->id]) }}" method="POST">
    @csrf
    <p><div class="p-3">
        <label for="enrolls" class="inline">請勾選郵寄對象：</label>
        <table class="w-full py-4 text-left font-normal">
            <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
                <th scope="col" class="p-2 w-8">
                    <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="checkbox" name="all" value="all" onclick="
                        const collection = document.getElementsByName('enrolls[]');
                        if (this.checked) {
                            for (let i = 0; i < collection.length; i++) {
                                collection[i].checked = true;
                            }
                        } else {
                            for (let i = 0; i < collection.length; i++) {
                                collection[i].checked = false;
                            }
                        }
                    ">
                </th>
                <th scope="col" class="p-2">
                    年班座號
                </th>
                <th scope="col" class="p-2">
                    姓名
                </th>
                <th scope="col" class="p-2">
                    聯絡人
                </th>
                <th scope="col" class="p-2">
                    聯絡信箱
                </th>
                <th scope="col" class="p-2">
                    報名順位（時間）
                </th>
                <th scope="col" class="p-2">
                    已錄取
                </th>
            </tr>
            @forelse ($enrolls as $order => $enroll)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td class="p-2">
                    <input class="rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="checkbox" name="enrolls[]" value="{{ $enroll->id }}">
                </td>
                <td class="p-2">
                    <span class="text-sm">{{ $enroll->student->class_id }}{{ $enroll->student->seat }}</span>
                </td>
                <td class="p-2">
                    <span class="text-sm">{{ $enroll->student->realname }}</span>
                </td>
                <td class="p-2">
                    <span class="text-sm">{{ $enroll->parent }}</span>
                </td>
                <td class="p-2">
                    <span class="text-sm">{{ $enroll->email }}</span>
                </td>
                <td class="p-2">
                    <span class="text-sm">{{ $order + 1 }}（{{ $enroll->created_at }}）</span>
                </td>
                <td class="p-2">
                    @if ($enroll->accepted)
                    <i class="fa-solid fa-check"></i>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-xl font-bold">目前還沒有人報名！</td>
            </tr>
            @endforelse
        </table>
    </div></p>
    <p><div class="p-3">
        <label for="message" class="inline">郵件內容：</label>
        <textarea name="message" rows="10" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="{{ $club->memo }}" required></textarea>
    </div></p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                送出
            </button>
        </div>
    </p>
</form>
<script>
    function check_self(ele) {
        if (ele.checked) {
            document.getElementById('selfdefine').checked = false;
        }
    }
</script>
@endsection
