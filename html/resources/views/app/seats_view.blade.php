@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    分組座位表
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seats') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="flex flex-col">
    <div class="flex flex-row justify-center">
        <label class="p-3">{{ $seats->name }}</label>
    </div>
    <div class="flex flex-row justify-center">
        <div class="p-3">
            <table class="border border-2 border-slate-300">
                @foreach ($seats->matrix() as $i => $cols)
                <tr class="h-10">
                    @foreach ($cols as $j => $data)
                    <td class="w-24 border border-2 border-slate-300 {{ $styles[$data->group] }}">
                        @if ($data->student)
                        @if ($data->student->gender == 1)
                        <label class="text-blue-700">{{ ($data->student->seat >= 10) ? $data->student->seat : '0'.$data->student->seat }}　{{ $data->student->realname }}</label>
                        @else
                        <label class="text-red-700">{{ ($data->student->seat >= 10) ? $data->student->seat : '0'.$data->student->seat }}　{{ $data->student->realname }}</label>
                        @endif
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </table>
            <table>
                <tr class="h-10">
                    <td class="w-72"></td>
                    <td class="w-48 border border-black border-2 bg-teal-300 text-center">講　　　　桌</td>
                    <td class="w-72"></td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection