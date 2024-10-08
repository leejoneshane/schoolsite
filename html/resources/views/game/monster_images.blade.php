@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    管理怪物圖片
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.monsters') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<h1 class="text-xl">怪物種族：
    <select class="form-select w-48 m-0 px-3 py-2 text-xl font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200"
    name="profession" onchange="
        var selection = this.value;
        window.location.replace('{{ route('game.monster_images') }}/' + selection );
    ">
        @foreach ($monsters as $m)
        <option{{ ($m->id == $monster->id) ? ' selected' : ''}} value="{{ $m->id }}">{{ $m->name }}</option>
        @endforeach
    </select>
</h1>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        僅接受去背的圖片，圖片格式為 PNG 或 GIF，圖片解析度為直立式 3:4 (900x1200)。
    </p>
</div>
<div class="dropzone-container">
    <form action="{{ route('game.monster_upload', [ 'monster_id' => $monster->id ]) }}" class="dropzone dz-clickable" id="image-upload" method="POST" enctype="multipart/form-data">
        <div id="message" class="dz-message">
            <h1 class="text-5xl">怪物圖片管理</h1>
            <p>請點擊上傳圖片檔，或將圖片檔拖曳到這裡上傳。</p>
        </div>
    </form>
</div>
<script nonce="selfhost">
Dropzone.options.imageUpload = {
    url: '{{ route('game.monster_upload', [ 'monster_id' => $monster->id ]) }}',
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    clickable: true,
    maxFiles: 10,
    maxFilesize: 50000,
    acceptedFiles: ".png,.gif",
    addRemoveLinks: true,
    timeout: 50000,
    init:function() {
        // Get images
        var myDropzone = this;
        window.axios.get('{{ route('game.monster_scanimages', [ 'monster_id' => $monster->id ]) }}')
        .then( (response) => {
            response.data.forEach( (element) => {
                var file = {name: element.name, size: element.size};
                myDropzone.options.addedfile.call(myDropzone, file);
                myDropzone.options.thumbnail.call(myDropzone, file, element.path);
                myDropzone.emit("complete", file);
            });
        });
    },
    removedfile: function(file) {
        if (this.options.dictRemoveFile) {
            return Dropzone.confirm("您確定要刪除此圖片嗎？", function() {
                window.axios.post('{{ route('game.class_removeimage') }}', {
                    filename: file.name,
                }, {
                    headers: {
                        'Content-Type': 'application/json;charset=utf-8',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then( (response) => {
                    alert(response.data.success + " 圖片檔已經成功刪除！");
                }).catch( (response) => {
                    console.log(response.data);
                });
                document.getElementById('image-upload').removeChild(file.previewElement);
            });
        }
    },
    thumbnail: function (file, dataUrl) {
        if (file.previewElement) {
            file.previewElement.classList.remove("dz-file-preview");
            var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
            for (var i = 0; i < images.length; i++) {
                var thumbnailElement = images[i];
                thumbnailElement.alt = file.name;
                thumbnailElement.src = dataUrl;
            }
            setTimeout(function () {
                file.previewElement.classList.add("dz-image-preview");
            }, 1);
        }
    },
    success: function(file, response) {
            file.previewElement.id = response.success;
            //console.log(file); 
            // set new images names in dropzone’s preview box.
            var olddatadzname = file.previewElement.querySelector("[data-dz-name]");   
            file.previewElement.querySelector("img").alt = response.success;
            olddatadzname.innerHTML = response.success;
    },
    error: function(file, response) {
           if($.type(response) === "string")
                var message = response; //dropzone sends it's own error messages in string
            else
                var message = response.message;
            file.previewElement.classList.add("dz-error");
            _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
            _results = [];
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i];
                _results.push(node.textContent = message);
            }
            return _results;
    }
};
</script>
@endsection
