@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal">
    匯出公開課成果報告
    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public', ['section' => $section]) }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="p-3 font-bold">
    <label class="inline align-top">各領域公開課統計：</label>
    <table class="border-collapse text-sm text-center">
        <thead>
            <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
                <th scope="col" class="p-2">
                    教學領域
                </th>
                <th scope="col" class="p-2">
                    公開課次數
                </th>
                <th scope="col" class="p-2">
                    已上傳教案
                </th>
                <th scope="col" class="p-2">
                    已上傳會談紀錄
                </th>
                <th scope="col" class="p-2">
                    成果報告
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($domains as $dom)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td class="p-2">{{ $dom->name }}</td>
                <td class="p-2">{{ $dom->count }}</td>
                <td class="p-2">{{ $dom->eduplan }}</td>
                <td class="p-2">{{ $dom->discuss }}</td>
                <td class="p-2">
                    @if ($dom->eduplan > 0 && $dom->discuss > 0)
                    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public.downloadWord', ['section' => $section, 'domain_id' => $dom->id]) }}">匯出成Docx</a>
                    <a class="text-sm py-2 px-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('public.downloadPDF', ['section' => $section, 'domain_id' => $dom->id]) }}">匯出成PDF</a>
                    @endif
                </td>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
