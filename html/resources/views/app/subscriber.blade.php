@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    訂閱電子報
</div>
<p><div class="p-3">
    <label for="email" class="inline">您的郵件信箱：</label>
    <input type="text" id="email" name="email" value="{{ ($email) ?: '' }}" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" onchange="check(this.value)">
    <button type="button" class="inline text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" onclick="refresh()">
        查詢訂閱情形
    </button>
    <div id="notice" class="hidden text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>偵測到 email 已變更，請務必按「查閱訂閱情形」按鈕，重新整理頁面！</div>
</div></p>
<table class="w-full py-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            訂閱
        </th>
        <th scope="col" class="p-2">
            名稱
        </th>
        <th scope="col" class="p-2">
            出刊時間
        </th>
    </tr>
    @foreach ($news as $n)
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">
            <label for="news_{{$n->id}}" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="news_{{$n->id}}" name="news_{{$n->id}}" value="yes" class="sr-only peer" onclick="subscribe(this, '{{$n->id}}')"{{ $subscriber ? ($subscriber->subscripted($n->id) ? ' checked' : '') : ' disabled' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            </label>
        </td>
        <td class="p-2">{{ $n->name }}</td>
        <td class="p-2">{{ $n->job }}</td>
        </td>
    </tr>
    @endforeach
</table>
<script>
    function check(email) {
        if (email != '{{ $email }}') {
            document.getElementById('notice').classList.remove('hidden');
        } else {
            document.getElementById('notice').classList.add('hidden');
        }
    }
    function refresh() {
        const email = document.getElementById('email').value;
        window.location.replace('{{ route('subscriber') }}' + '/' + email);
    }
    function subscribe(obj, id) {
        if (obj.checked) {
            window.axios.post('{{ route('subscriber.subscription') }}/' + id, {
                email: '{{ $email }}',
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        } else {
            window.axios.post('{{ route('subscriber.cancel') }}/' + id, {
                email: '{{ $email }}',
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