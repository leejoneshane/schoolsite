@extends('layouts.admin')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    選單管理
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ empty($current) ? route('menus.add') : route('menus.add', ['menu' => $current]) }}">
        <i class="fa-solid fa-circle-plus"></i>新增項目
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        URL 若設定為「#」，該選單項目會轉變成「次選單」，您也可以直接從，<span class="font-semibold">類別選單</span>選取。
        外部 URL 應包含完整路徑，若省略 https://host 則視為本機連結，本機連結建議使用路由名稱「route.名稱」的格式自動產生，以避免繁複修改。
        所有路由名稱列表如右：{{ implode('、', $routes) }}。
    </p>
</div>
<select id="menus" class="block w-full py-2.5 px-0 font-semibold text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 bg-white dark:bg-gray-700"
    onchange="
        var url = this.value;
        window.location.replace(url);
    ">
    <option {{ empty($current) ? 'selected ' : ''}}value="{{ route('menus') }}">頂層</option>
    @foreach ($menus as $m)
    <option {{ ($current == $m->id) ? 'selected ' : ''}}value="{{ route('menus', ['menu' => $m->id]) }}">{{ $m->caption }}</option>
    @endforeach
</select>
<form id="edit-menu" action="{{ empty($current) ? route('menus') : route('menus', ['menu' => $current]) }}" method="POST">
    @csrf
    <table class="w-full py-4 text-left font-normal">
        <tr class="font-semibold text-lg">
            <th scope="col" class="p-2">
                識別碼
            </th>
            <th scope="col" class="p-2">
                選單名稱
            </th>
            @if (!empty($current))
            <th scope="col" class="p-2">
                上層選單
            </th>
            @endif
            <th scope="col" class="p-2">
                類別
            </th>
            <th scope="col" class="p-2">
                URL
            </th>
            @if (!empty($current))
            <th scope="col" class="p-2">
                權重
            </th>
            @endif
        </tr>
        @foreach ($items as $i)
        <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
            <td class="p-2">
                <input class="rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                type="text" name="ids[{{ $i->id }}]" value="{{ $i->id }}">
            </td>
            <td class="p-2">
                <input class="rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" name="captions[{{ $i->id }}]" value="{{ $i->caption }}">
            </td>
            @if (!empty($current))
            <td class="p-2">
                <select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    name="parents[{{ $i->id }}]">
                @foreach ($menus as $m)
                    <option {{ ($m->id == $i->parent_id) ? 'selected ' : ''}}value="{{ $m->id }}">{{ $m->caption }}</option>
                @endforeach
                </select>
            </td>
            @endif
            <td class="p-2">
                <select class="form-select w-24 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    name="kind[{{ $i->id }}]" onchange="
                        var kind = this.value;
                        var myp = document.getElementById('url_{{ $i->id }}');
                        if (kind == 'sub') {
                            myp.value = '#';
                            myp.classList.add('hidden');
                        } else {
                            myp.value = '{{ $i->url }}';
                            myp.classList.remove('hidden');
                        }
                    ">
                    <option {{ ($i->url == '#') ? 'selected ' : ''}}value="sub">次選單</option>
                    <option {{ ($i->url != '#') ? 'selected ' : ''}}value="item">超連結</option>
                </select>
            </td>
            <td class="p-2">
                <input class="{{ ($i->url == '#') ? 'hidden ' : '' }}rounded w-48 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    id="url_{{ $i->id }}" type="text" name="urls[{{ $i->id }}]" value="{{ $i->url }}">
            </td>
            @if (!empty($current))
            <td class="p-2">
                <input class="rounded w-32 px-3 py-2 border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
                    type="text" name="weights[{{ $i->id }}]" value="{{ $i->weight }}">
            </td>
            @endif
            <td class="p-2">
                <a class="py-2 pr-6 text-red-300 hover:text-red-600" href="#"
                    onclick="
                        const myform = document.getElementById('remove');
                        myform.action = '{{ route('menus.remove', ['menu' => $i->id]) }}';
                        myform.submit();
                ">
                    <i class="fa-solid fa-trash"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </table>
    <div class="py-2 px-6 mb-6">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            修改
        </button>
    </div>
</form>
<form class="hidden" id="remove" action="" method="POST">
    @csrf
</form>
@endsection
