@auth
<div class="px-3">
    <label for="user_id" class="inline text-teal-200">傳訊息給：</label>
    <select class="w-28 inline-block mt-2 lg:mt-0 px-4 lg:px-2 py-1 leading-none border rounded border-white hover:border-transparent"
        id="user_id" name="user_id">
        @if ($broadcast)
        <option value="">－廣播－</option>
        @else
        <option value="">－請選擇－</option>
        @endif
    </select>
    <div class="inline">
        <button class="text-white bg-blue-500 hover:bg-blue-700 focus:outline-none rounded-full text-sm px-2 py-1 text-center mr-2"
            onclick="send()">
            傳送
        </button>
    </div>
</div>
<script>
function send() {
    var uid = document.getElementById('user_id').value;
    if (uid) {
        var me = {{ auth()->user()->id }};
        var tell = prompt('您要告訴對方什麼？');
        if (tell) {
            window.axios.post('{{ route('messager.send') }}', {
                from: me,
                to: uid,
                message: tell,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }
@if ($broadcast)
    if (!uid) {
        var tell = prompt('要廣播什麼訊息？');
        if (tell) {
            window.axios.post('{{ route('messager.broadcast') }}', {
                message: tell,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    }
@endif
}

function doRefresh() {
    window.axios.get('{{ route("messager.list") }}')
        .then( (response) => {
            const selection = document.getElementById('user_id');
            var options = selection.children;
            for (let i=1; i<options.length; i++) {
                var check = true;
                response.data.forEach(function (item, i) {
                        if (options[i].value == item.id) check = false;
                });
                if (check) {
                    selection.removeChild(options[i]);
                }
            }
            response.data.forEach(function (item, i) {
                if (item.id != {{ auth()->user()->id }}) {
                    var check = true;
                    for (let i=1; i<options.length; i++) {
                        if (options[i].value == item.id) check = false;
                    }
                    if (check) {
                        var option = document.createElement('option');
                        option.value = item.id;
                        option.innerText = item.profile.realname;
                        selection.appendChild(option);
                    }
                }
            });
        })
        .catch( (error) => console.log(error));
}
setInterval(doRefresh, 300000);
</script>
@endauth