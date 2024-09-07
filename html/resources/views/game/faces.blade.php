@extends('layouts.game')

@section('content')
<div id="dropzone">
    <form action="{{ route('game.upload_faces') }}" class="dropzone max-w-lg mt-12 mx-auto rounded-xl border-2 border-dotted border-cyan-500 bg-white" id="file-upload" enctype="multipart/form-data">
        @csrf
        <div class="dz-message">
            請拖曳要上傳的圖片，放在這裡。<br>
        </div>
    </form>
</div>
<div class="dz-preview dz-file-preview">
    <div class="dz-details">
      <div class="dz-filename"><span data-dz-name></span></div>
      <div class="dz-size" data-dz-size></div>
      <img data-dz-thumbnail />
    </div>
    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
    <div class="dz-success-mark"><span>✔</span></div>
    <div class="dz-error-mark"><span>✘</span></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
  </div>
<div id="gallery">
@foreach ($faces as $face)
    <img src="{{ $face->url() }}" class="border-2 border-black"/>
@endforeach
</div>
<script>
    var dropzone = new Dropzone('#file-upload', {
        previewTemplate: document.querySelector('#preview-template').innerHTML,
        parallelUploads: 3,
        thumbnailHeight: 150,
        thumbnailWidth: 150,
        maxFilesize: 5,
        filesizeBase: 1500,
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
        }
    });

    var minSteps = 6,
        maxSteps = 60,
        timeBetweenSteps = 100,
        bytesPerStep = 100000;

    dropzone.uploadFiles = function (files) {
        var self = this;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));
            for (var step = 0; step < totalSteps; step++) {
                var duration = timeBetweenSteps * (step + 1);
                setTimeout(function (file, totalSteps, step) {
                    return function () {
                        file.upload = {
                            progress: 100 * (step + 1) / totalSteps,
                            total: file.size,
                            bytesSent: (step + 1) * file.size / totalSteps
                        };
                        self.emit('uploadprogress', file, file.upload.progress, file.upload
                            .bytesSent);
                        if (file.upload.progress == 100) {
                            file.status = Dropzone.SUCCESS;
                            self.emit("success", file, 'success', null);
                            self.emit("complete", file);
                            self.processQueue();
                        }
                    };
                }(file, totalSteps, step), duration);
            }
        }
    }
</script>
@endsection
