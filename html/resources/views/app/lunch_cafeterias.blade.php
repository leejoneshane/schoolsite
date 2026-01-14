@extends('layouts.main')

@section('content')
    <div class="text-2xl font-bold leading-normal pb-5">
        供餐地點管理
        <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('lunch') }}">
            <i class="fa-solid fa-eject"></i>返回上一頁
        </a>
    </div>

    <div class="flex flex-col gap-5 p-4">
        <!-- Add Form -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-4 border-b pb-2 dark:border-gray-700">新增供餐地點</h3>
            <form action="{{ route('lunch.cafeterias.store') }}" method="POST" class="flex gap-4 items-center">
                @csrf
                <label for="new_description" class="font-medium">地點說明：</label>
                <input type="text" id="new_description" name="description" placeholder="輸入供餐地點名稱" required
                    class="flex-1 rounded-md border border-gray-300 p-2 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition">新增</button>
            </form>
        </div>

        <!-- List -->
        <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
            <div class="p-4 border-b bg-gray-50 dark:bg-gray-700 dark:border-gray-600 font-bold text-lg">
                現有供餐地點列表
            </div>
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="p-4 border-b dark:border-gray-600 w-16">ID</th>
                        <th class="p-4 border-b dark:border-gray-600">地點名稱/說明</th>
                        <th class="p-4 border-b dark:border-gray-600 w-32">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cafeterias as $cafeteria)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="p-4 border-b dark:border-gray-600">{{ $cafeteria->id }}</td>
                            <td class="p-4 border-b dark:border-gray-600">
                                <form action="{{ route('lunch.cafeterias.update', $cafeteria->id) }}" method="POST"
                                    class="flex gap-2 items-center">
                                    @csrf
                                    <input type="text" name="description" value="{{ $cafeteria->description }}" required
                                        class="w-full max-w-md rounded border border-gray-300 p-2 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-200">
                                    <button type="submit" class="text-green-600 hover:text-green-800 p-2" title="儲存更新">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="p-4 border-b dark:border-gray-600">
                                <form action="{{ route('lunch.cafeterias.remove', $cafeteria->id) }}" method="POST"
                                    onsubmit="return confirm('確定要刪除此地點嗎？');">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium" title="刪除">
                                        <i class="fa-solid fa-trash-can"></i> 刪除
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($cafeterias->isEmpty())
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500 dark:text-gray-400">目前沒有設定任何供餐地點。</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection