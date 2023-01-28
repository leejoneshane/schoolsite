@extends('layouts.main')

@section('content')
<div class="text-2xl font-bold leading-normal pb-5">
    新增修繕項目
    <a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('repair') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<script src="/js/ckeditor.js"></script>
<form id="add-kind" action="{{ route('repair.addkind') }}" method="POST">
    @csrf
    <p class="p-3">
        <label for="title" class="inline">名稱：</label>
        <input type="text" id="title" name="title" class="inline w-64 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700 text-black dark:text-gray-200" required>
    </p>
    <p class="p-3">
        <label for="roles" class="inline">二級維修人員：</label>
        <div id="nassign">
        </div>
        <button id="nassign" type="button" class="py-2 px-6 rounded text-blue-300 hover:text-blue-600"
            onclick="add_teacher()"><i class="fa-solid fa-circle-plus"></i>
        </button>
    </p>
    <p class="p-3">
        <label for="description" class="inline">詳細描述：</label>
        <textarea id="description" name="description" rows="4" class="inline block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        ></textarea>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請列舉設備種類、維修流程、負責處室、其它聯絡方式...等資訊！</span>
    </p>
    <p class="p-3">
        <label for="selftest" class="inline">一級維修檢測方式：</label>
        <textarea id="selftest" name="selftest" rows="4" class="inline block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        ></textarea>
        <br><span class="text-teal-500"><i class="fa-solid fa-circle-exclamation"></i>請列舉不同故障情形的檢測方式、報修前應紀錄的重點...等資訊！</span>
    </p>
    <p class="p-6">
        <div class="inline">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-6 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                新增
            </button>
        </div>
    </p>
</form>
<script>
    function remove_teacher(elem) {
        const parent = elem.parentNode;
        const brother = elem.previousElementSibling;
        parent.removeChild(brother);
        parent.removeChild(elem);
    }

    function add_teacher() {
        var target = document.getElementById('nassign');
        var my_cls = '<select class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200" name="teachers[]">';
        @foreach ($teachers as $t)
        @php
            $gap = '';
            $rname = '';
            if ($t->role_name) $rname = $t->role_name;
            for ($i=0;$i<6-mb_strlen($rname);$i++) {
                $gap .= '　';
            }
            $display = $t->role_name . $gap . $t->realname;
        @endphp
        my_cls += '<option value="{{ $t->uuid }}">{{ $display }}</option>';
        @endforeach
        my_cls += '</select>';
        const elemc = document.createElement('select');
        target.parentNode.insertBefore(elemc, target);
        elemc.outerHTML = my_cls;
        my_btn = '<button type="button" class="py-2 pl-0 pr-6 rounded text-red-300 hover:text-red-600" onclick="remove_teacher(this);"><i class="fa-solid fa-circle-minus"></i></button>';
        const elemb = document.createElement('button');
        target.parentNode.insertBefore(elemb, target);
        elemb.outerHTML = my_btn;
    }

    class MyUploadAdapter {
        constructor( loader ) {
            // The file loader instance to use during the upload.
            this.loader = loader;
        }

        // Starts the upload process.
        upload() {
            return this.loader.file
                .then( file => new Promise( ( resolve, reject ) => {
                    this._initRequest();
                    this._initListeners( resolve, reject, file );
                    this._sendRequest( file );
                } ) );
        }

        // Aborts the upload process.
        abort() {
            if ( this.xhr ) {
                this.xhr.abort();
            }
        }

        // Initializes the XMLHttpRequest object using the URL passed to the constructor.
        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();
            // Note that your request may look different. It is up to you and your editor
            // integration to choose the right communication channel. This example uses
            // a POST request with JSON as a data structure but your configuration
            // could be different.
            xhr.open( 'POST', '{{ route('repair.imageupload') }}', true );
            xhr.responseType = 'json';
        }

        // Initializes XMLHttpRequest listeners.
        _initListeners( resolve, reject, file ) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `無法上傳檔案：${ file.name }`;
            xhr.addEventListener( 'error', () => reject( genericErrorText ) );
            xhr.addEventListener( 'abort', () => reject() );
            xhr.addEventListener( 'load', () => {
                const response = xhr.response;
                // This example assumes the XHR server's "response" object will come with
                // an "error" which has its own "message" that can be passed to reject()
                // in the upload promise.
                //
                // Your integration may handle upload errors in a different way so make sure
                // it is done properly. The reject() function must be called when the upload fails.
                if ( !response || response.error ) {
                    return reject( response && response.error ? response.error.message : genericErrorText );
                }
                // If the upload is successful, resolve the upload promise with an object containing
                // at least the "default" URL, pointing to the image on the server.
                // This URL will be used to display the image in the content. Learn more in the
                // UploadAdapter#upload documentation.
                resolve( {
                    default: response.url
                } );
            } );

            // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
            // properties which are used e.g. to display the upload progress bar in the editor
            // user interface.
            if ( xhr.upload ) {
                xhr.upload.addEventListener( 'progress', evt => {
                    if ( evt.lengthComputable ) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                } );
            }
        }

        // Prepares the data and sends the request.
        _sendRequest( file ) {
            // Prepare the form data.
            const data = new FormData();
            data.append( 'upload', file );
            // Important note: This is the right place to implement security mechanisms
            // like authentication and CSRF protection. For instance, you can use
            // XMLHttpRequest.setRequestHeader() to set the request headers containing
            // the CSRF token generated earlier by your application.
            data.append( '_token', '{{ csrf_token() }}');
            // Send the request.
            this.xhr.send( data );
        }
    }

    function MyCustomUploadAdapterPlugin( editor ) {
        editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
            // Configure the URL to the upload script in your back-end here!
            return new MyUploadAdapter( loader );
        };
    }

    ClassicEditor
        .create( document.querySelector( '#description' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            licenseKey: '',
        } )
        .catch( error => {
            console.error( error );
        } );
    ClassicEditor
        .create( document.querySelector( '#selftest' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            licenseKey: '',
        } )
        .catch( error => {
            console.error( error );
        } );
</script>
@endsection
