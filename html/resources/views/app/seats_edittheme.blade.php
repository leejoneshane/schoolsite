@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    編輯座位表版型
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seats.theme') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        請先從右側選單，選取要設定的組別，然後在左側表格適當的位置點一下。
    </p>
</div>
<form id="edit-theme" action="{{ route('seats.edittheme', [ 'id' => $template->id ]) }}" method="POST">
    @csrf
    <input type="hidden" id="matrix" name="matrix" value="{{ json_encode($template->matrix) }}">
    <div class="flex flex-col">
        <div class="flex flex-row justify-center">
            <table class="p-3">
                <tr>
                    <td>
                        <label for="title" class="inline">版型名稱（教室或地點）：</label>
                        <input type="text" id="title" name="title" value="{{ $template->name }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" maxlength="100" required>
                        <button type="submit" class="inline text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            下表設定完成後，按這裡儲存！
                        </button>
                    </td>
                </tr>
            </table>
        </div>
        <div class="flex flex-row justify-center">
            <div class="p-3">
                <table class="cursor-pointer border border-2 border-slate-300">
                    @foreach ($template->matrix as $i => $cols)
                    <tr class="h-10">
                        @foreach ($cols as $j => $group)
                        <td class="w-16 border border-2 border-slate-300 {{ $styles[$group] }}" onclick="set_group(this,{{ $i }}, {{ $j }})">&nbsp;</td>
                        @endforeach
                    </tr>
                    @endforeach
                </table>
                <table>
                    <tr class="h-10">
                        <td class="w-48"></td>
                        <td class="w-32 border border-black border-2 bg-teal-300 text-center">講　　　　桌</td>
                        <td class="w-48"></td>
                    </tr>
                </table>
            </div>
            <div class="p-3">
                請選擇組別：
                <ul>
                    <li class="cursor-pointer bg-white" onclick="sel_group(0)">清　除　　<i id="gp0" class="fa-solid fa-check hidden"></i></li>
                    <li class="cursor-pointer bg-gray-200" onclick="sel_group(1)">第一組　　<i id="gp1" class="fa-solid fa-check hidden"></i></li>
                    <li class="cursor-pointer bg-amber-300" onclick="sel_group(2)">第二組　　<i id="gp2" class="fa-solid fa-check hidden"></i></li>
                    <li class="cursor-pointer bg-lime-300" onclick="sel_group(3)">第三組　　<i id="gp3" class="fa-solid fa-check hidden"></i></li>
                    <li class="cursor-pointer bg-emerald-300" onclick="sel_group(4)">第四組　　<i id="gp4" class="fa-solid fa-check hidden"></i></li>
                    <li class="cursor-pointer bg-cyan-300" onclick="sel_group(5)">第五組　　<i id="gp5" class="fa-solid fa-check hidden"></i></li>
                    <li class="cursor-pointer bg-blue-300" onclick="sel_group(6)">第六組　　<i id="gp6" class="fa-solid fa-check hidden"></i></li>
                    <li class="cursor-pointer bg-violet-300" onclick="sel_group(7)">第七組　　<i id="gp7" class="fa-solid fa-check hidden"></i></li>
                    <li class="cursor-pointer bg-pink-300" onclick="sel_group(8)">第八組　　<i id="gp8" class="fa-solid fa-check hidden"></i></li>
                </ul>
            </div>  
        </div>
    </div>
</form>
<script nonce="selfhost">
    const styles = {!! json_encode($styles, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!};
    const matrix = {!! json_encode($template->matrix, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!};

    var ngroup = 0;

    function sel_group(no) {
        ngroup=no;
        for(var i=0;i<=8;i++) {
            document.getElementById('gp'+i).classList.add('hidden');
        }
        document.getElementById('gp'+no).classList.remove('hidden');
    }

    function set_group(obj,x,y) {
        obj.classList.remove(styles[matrix[x][y]]);
        obj.classList.add(styles[ngroup]);
        matrix[x][y] = ngroup;
        document.getElementById('matrix').value = JSON.stringify(matrix);
    }
</script>
@endsection
