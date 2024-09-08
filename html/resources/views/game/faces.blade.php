@extends('layouts.game')

@section('content')
<div class="dropzone-container">
    <form method="POST" action="{{ route('game.faces') }}" class="dropzone dz-clickable" id="face-upload" enctype="multipart/form-data">
        @csrf
        <div class="dz-message">
            <h1 class="text-5xl">角色臉孔管理</h1>
            <p>請點擊這裡選取要上傳的圖片，或將圖片拖曳到這個區域。</p>
        </div>
    </form>
</div>
<script>
    Dropzone.options.faceUpload = {
        url: '{{ route('game.faces') }}',
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        maxFiles: 5, 
        maxFilesize: 4,
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        addRemoveLinks: true,
        timeout: 50000,
        init:function() {
            // Get images
            var myDropzone = this;
            window.axios.get('{{ route('game.faces_gallery') }}')
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
                    if (file.previewElement.id != "") {
                        var name = file.previewElement.id;
                    } else {
                        var name = file.name;
                    }
                    window.axios.post('{{ route('game.faces_remove') }}', {
                        filename: name,
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
                    var fileRef;
                    return (fileRef = file.previewElement) != null ? fileRef.parentNode.removeChild(file.previewElement) : void 0;
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
