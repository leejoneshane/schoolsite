@extends('layouts.game')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    管理探險地圖
</div>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mb-5" role="alert">
    <p>
        不接受去背的圖片，圖片格式為 PNG，圖片解析度為直立式 16:9 (2048x1152)。
    </p>
</div>
<div class="dropzone-container">
    <form action="{{ route('game.map_upload') }}" class="dropzone dz-clickable" id="image-upload" method="POST" enctype="multipart/form-data">
        <div id="message" class="dz-message">
            <h1 class="text-5xl">地圖管理</h1>
            <p>請點擊上傳圖片檔，或將圖片檔拖曳到這裡上傳。</p>
        </div>
    </form>
</div>
<script nonce="selfhost">
Dropzone.options.imageUpload = {
    url: '{{ route('game.map_upload') }}',
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    clickable: true,
    maxFiles: 10,
    maxFilesize: 50000,
    acceptedFiles: ".png",
    addRemoveLinks: true,
    timeout: 50000,
    init:function() {
        // Get images
        var myDropzone = this;
        window.axios.get('{{ route('game.map_scanimages') }}')
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
                window.axios.post('{{ route('game.map_removeimage') }}', {
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
