@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    安排座位
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('seats') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        請先從右側選單，選取學生，然後在左側表格適當的位置點一下。
    </p>
</div>
<div class="flex flex-col">
    <div class="flex flex-row justify-center">
        <label class="p-3">{{ $seats->name }}</label>
    </div>
    <div class="flex flex-row justify-center">
        <div class="p-3">
            <table class="cursor-pointer border border-2 border-slate-300">
                @foreach ($matrix as $i => $cols)
                <tr class="h-10">
                    @foreach ($cols as $j => $data)
                    <td class="w-24 border border-2 border-slate-300 {{ $styles[$data[3]] }}" onclick="set_group(this,{{ $i }}, {{ $j }})">
                        {!! $data[1] !!}
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
        <div class="p-3 h-[440px] overflow-y-scroll">
            請選擇學生：
            <ul id="stu_list">
                @foreach ($without as $i => $l)
                <li id="list{{ $i }}" class="cursor-pointer bg-white" onclick="sel_student(this)">{!! $l[1] !!}　<i id="gp{{ $i }}" class="fa-solid fa-check{{ ($loop->first) ? '' : ' hidden' }}"></i></li>
                @endforeach
            </ul>
        </div>  
    </div>
</div>
<script nonce="selfhost">
    const students = {!! json_encode($students, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!};
    const matrix = {!! json_encode($matrix, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!};
    const sel = {!! json_encode($without, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!};
    var stu = 0;

    function sel_student(obj) {
        stu = parseInt(obj.id.substring(4));
        for(var i=0; i<sel.length; i++) {
            document.getElementById('gp'+i).classList.add('hidden');
        }
        document.getElementById('gp'+stu).classList.remove('hidden');
    }

    function set_group(obj,x,y) {
        if (matrix[x][y][0] == null) return;
        const stu_list = document.getElementById('stu_list');
        var smax = sel.length;
        var old = matrix[x][y][0];
        var newone = sel[stu][0];
        if (stu == 0) { //從座位清除學生
            if (old > 0) {
                //將要清除的學生加入到候選清單和陣列中
                sel[smax] = [ old, students[old].html ];
                var li = document.createElement('li');
                li.id = 'list' + smax;
                li.classList.add('cursor-pointer', 'bg-white');
                li.onclick = function() {sel_student(this);};
                li.innerHTML = sel[smax][1] + '　<i id="gp' + smax + '" class="fa-solid fa-check"></i>';
                stu_list.appendChild(li);
                //將座位清空
                matrix[x][y][0] = 0;
                obj.innerHTML = '&nbsp;';
                //重整候選清單的選取標記
                stu = smax;
                for(var i=0; i<smax; i++) {
                    document.getElementById('gp'+i).classList.add('hidden');
                }
                //移除該學生的資料紀錄
                window.axios.post('{{ route('seats.unassign') }}', {
                    seats_id: '{{$seats->id}}',
                    uuid: students[old].uuid,
                }, {
                    headers: {
                        'Content-Type': 'application/json;charset=utf-8',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            }
        } else if (old == 0) { //將學生排入座位
            //從候選清單移除學生，並重整索引值
            var li = document.getElementById('list' + stu);
            stu_list.removeChild(li);
            for (var i=stu+1; i<smax; i++){
                var li = document.getElementById('list' + i);
                li.id = 'list' + (i - 1);
                document.getElementById('gp' + i).id = 'gp' + (i - 1);
            }
            //將選取學生寫入座位表中
            matrix[x][y][0] = newone;
            matrix[x][y][1] = sel[stu][1];
            obj.innerHTML = sel[stu][1];
            //從候選陣列移除學生
            sel.splice(stu, 1);
            //將選取標記設定成「清除」
            stu = 0;
            document.getElementById('gp0').classList.remove('hidden');
            //新增該學生的資料紀錄
            window.axios.post('{{ route('seats.assign') }}', {
                seats_id: '{{$seats->id}}',
                uuid: students[newone].uuid,
                sequence: matrix[x][y][2],
                group_no: matrix[x][y][3],
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        } else { //交換學生座位
            //將選取中的清單置換為座位表中的學生
            var temp = students[old].html;
            var li = document.getElementById('list' + stu);
            li.innerHTML = temp + '　<i id="gp' + stu + '" class="fa-solid fa-check"></i>';
            //將選取學生排入座位表中
            matrix[x][y][0] = newone;
            obj.innerHTML = students[newone].html;
            //將選取中的陣列置換為座位表中的學生
            sel[stu][0] = old;
            sel[stu][1] = temp;
            //移除舊生的資料紀錄
            window.axios.post('{{ route('seats.unassign') }}', {
                seats_id: '{{$seats->id}}',
                uuid: students[old].uuid,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            //新增新生的資料紀錄
            window.axios.post('{{ route('seats.assign') }}', {
                seats_id: '{{$seats->id}}',
                uuid: students[newone].uuid,
                sequence: matrix[x][y][2],
                group_no: matrix[x][y][3],
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }
</script>
@endsection
