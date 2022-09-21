<div class="px-3">
@auth
  <label for="user_id" class="inline">傳訊息給：</label>
  <select class="w-28 inline-block mt-2 lg:mt-0 px-4 lg:px-2 py-1 leading-none border rounded border-white hover:border-transparent"
      id="user_id" name="user_id">
      <option>－請選擇－</option>
      @foreach ($users as $u)
      @if ($u->id != auth()->user()->id)
      <option value="{{ $u->id }}">{{ $u->profile['realname'] }}</option>
      @endif
      @endforeach
  </select>
  <div class="inline">
    <button class="text-white bg-blue-500 hover:bg-blue-700 focus:outline-none rounded-full text-sm px-2 py-1 text-center mr-2"
      onclick="
        var uid = document.getElementById('user_id').value;
        if (uid) {
            var me = {{ auth()->user()->id }};
            var tell = prompt('您要告訴對方什麼？');
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
      ">
        傳送
    </button>
  </div>
@endauth
  <div id="messager" class="inline">
    <span id="from"></span><span id="notify"></span>
  </div>
</div>
<script type="module">
  document.addEventListener("DOMContentLoaded", function(event) { 
      window.Echo.channel('public')
          .listen('PublicMessage', (e) => {
              let from = document.getElementById('from');
              from.innerHTML = '公開頻道：';
              let notify = document.getElementById('notify');
              notify.innerHTML = e.message;
          });
@auth
      window.Echo.private('private.{{ auth()->user()->id }}')
          .listen('PrivateMessage', (e) => {
              let from = document.getElementById('from');
              from.innerHTML = '來自' + e.from_user;
              let notify = document.getElementById('notify');
              notify.innerHTML = '訊息：' + e.message;
          });
@endauth
@admin
      window.Echo.channel('admin')
          .listen('AdminMessage', (e) => {
              let from = document.getElementById('from');
              from.innerHTML = '系統訊息：';
              let notify = document.getElementById('notify');
              notify.innerHTML = e.message;
          });
@endadmin
  });
</script>