@extends('layouts.game')

@section('content')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/translations/zh.js"></script>
<div class="w-full flex gap-4">
    <div class="w-80 h-full flex flex-col">
        <div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
            學習任務管理
            <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.worksheets') }}">
                <i class="fa-solid fa-eject"></i>返回上一頁
            </a>
        </div>
        <table class="w-full h-full text-left font-normal">
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">學習單標題</th>
                <td class="p-2">{{ $worksheet->title }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">設計者</th>
                <td class="p-2">{{ $worksheet->teacher_name }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">科目名稱</th>
                <td class="p-2">{{ $worksheet->subject }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">適用年級</th>
                <td class="p-2">{{ $worksheet->grade->name }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <th scope="row" colspan="2" class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg p-2">任務列表：</th>
            </tr>
            <tr id="empty" class="{{ $worksheet->tasks->count() > 0 ? 'hidden ' : '' }}odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td colspan="2" class="p-2">還沒有學習任務！</td>
            </tr>
            @foreach ($worksheet->tasks as $t)
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
                <td colspan="2" class="p-2">
                    <button id="task{{ $t->id }}" onclick="open_task({{ $t->id }})">{{ $t->title }}</button>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    <div id="myCanvas" class="w-full h-full flex justify-center">
        <canvas class="w-[800px] h-[700px] z-0" onmousedown="draw(event)"></canvas>
    </div>
</div>
<div class="sr-only">
    <svg id="dot" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="width:1.5em;height:1.5em;vertical-align:-0.125em;color:darkgrey;">
        <path fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>
    </svg>
</div>
<div id="taskModal" data-modal-placement="center-center" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 id="head" class="text-center text-xl font-semibold text-gray-900 dark:text-white">編輯任務</h3>
            </div>
            <div class="p-2 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <label for="title" class="text-base">標題：</label>
                <input type="text" id="title" name="title" value="" class="block w-80 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none">
                <label class="text-base">故事：</label>
                <div id="story"></div>
                <label class="text-base">任務：</label>
                <div id="task"></div>
                <div class="p-2">
                    <label for="review" class="inline-flex relative items-center cursor-pointer">
                        <input type="checkbox" id="review" name="review" value="yes" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-gray-900 dark:text-gray-300">需要審核</span>
                    </label>
                </div>
                <div class="p-2">
                    <label for="xp" class="text-base">經驗獎勵：</label>
                    <input id="xp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="number" name="xp" min="0" max="500" step="1" value="">
                    <label for="gp" class="text-base">金幣獎勵：</label>
                    <input id="gp" class="w-16 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700"
                        type="number" name="gp" min="0" max="500" step="1" value="">
                    <label for="item" class="text-base">道具獎勵：</label>
                    <select id="item" name="item" class="form-select w-48 m-0 px-3 py-2 text-base font-normal transition ease-in-out rounded border border-gray-300 dark:border-gray-400 bg-white dark:bg-gray-700 text-black dark:text-gray-200">
                        <option value="">無</option>
                        @foreach ($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button onclick="save_task();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        儲存
                    </button>
                    <button onclick="taskModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        取消
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var wid = {{ $worksheet->id }};
    var tid;
    var tasks = [];
    @foreach ($worksheet->tasks as $t)
    tasks[{{ $t->id }}] = {!! $t->toJson(JSON_UNESCAPED_UNICODE); !!};
    @endforeach

    var $targetEl = document.getElementById('taskModal');
    const taskModal = new window.Modal($targetEl);
    var pos;
    const canvas = document.querySelector('canvas');
    const ctx = canvas.getContext("2d");
    const dot = document.getElementById('dot');
    const tmp = document.getElementById('tmp');
    const map = new Image(800, 700);
    map.src = '{{ $worksheet->map->url() }}';
    map.addEventListener("load", (e) => {
        canvas.width = 800;
        canvas.height = 700;
        ctx.drawImage(map, 0, 0, canvas.width, canvas.height);
    });
    window.addEventListener("resize", (event) => {
        var rect = canvas.getBoundingClientRect();
        var nodes = document.querySelectorAll('img[role="task"]');
        if (nodes) {
            nodes.forEach( (node) => {
                node.style.top = parseInt(node.getAttribute('data-y')) + rect.top + 'px';
                node.style.left = parseInt(node.getAttribute('data-x')) + rect.left + 'px';
            });
        }
    });

    function getMousePos(evt) {
        pos = {
            x: parseInt(evt.offsetX - 12),
            y: parseInt(evt.offsetY - 32)
        };
    }

    function draw(evt) {
        var rect = canvas.getBoundingClientRect();
        getMousePos(evt);
        dot.style.color = 'aqua';
        var xml = new XMLSerializer().serializeToString(dot);
        var b64 = 'data:image/svg+xml;base64,' + btoa(xml);
        var tmp = document.createElement('img');
        tmp.setAttribute('role', 'task');
        tmp.setAttribute('data-x', pos.x);
        tmp.setAttribute('data-y', pos.y);
        tmp.setAttribute('id', 0);
        tmp.setAttribute('src', b64);
        tmp.setAttribute('onclick', 'moveto(this)');
        tmp.style.position = 'absolute';
        tmp.style.zIndex = 2;
        tmp.style.top = pos.y + rect.top + 'px';
        tmp.style.left = pos.x + rect.left + 'px';
        tmp.style.width = '24px';
        tmp.style.height = '32px';
        document.body.appendChild(tmp);
//        open_task(0);
    }

    function open_task(tno) {
        tid = tno;
        if (tid == 0) {
            document.getElementById('title').value = '';
            window.story.setData('');
            window.task.setData('');
            document.getElementById('review').checked = true;
            document.getElementById('xp').value = 0;
            document.getElementById('gp').value = 0;
            var items = document.getElementById('item').options;
            items.forEach( item => {
                item.removeAttribute('selected');
            });
            document.getElementById('head').innerHTML = '新增任務';
        } else {
            document.getElementById('title').value = tasks[tid].title;
            window.story.setData(tasks[tid].story);
            window.task.setData(tasks[tid].task);
            document.getElementById('review').checked = tasks[tid].review;
            document.getElementById('xp').value = tasks[tid].xp;
            document.getElementById('gp').value = tasks[tid].gp;
            var items = document.getElementById('item').options;
            items.forEach( item => {
                if (item.value == tasks[tid].item) {
                    item.setAttribute('selected', null);
                } else {
                    item.removeAttribute('selected');
                }
            });
            document.getElementById('head').innerHTML = '編輯任務';
        }
        taskModal.show();
    }

    function save_task() {
        var title = document.getElementById('title').value;
        var story = window.story.getData();
        var task = window.task.getData();
        var node = document.getElementById('review');
        if (node.checked) {
            var review = 'yes'; 
        } else {
            var review = 'no';
        }
        var xp = document.getElementById('xp').value;
        var gp = document.getElementById('gp').value;
        var item = document.getElementById('item').value;
        if (tid == 0) {
            window.axios.post('{{ route('game.task_add') }}', {
                wid: wid,
                title: title,
                x: pos.x,
                y: pos.y,
                story: story,
                task: task,
                review: review,
                xp: xp,
                gp: gp,
                item: item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( (response) => {
                var task = response.data.task;
                tasks[task.id] = task;
            });
        } else {
            window.axios.post('{{ route('game.task_edit') }}', {
                wid: wid,
                story: story,
                task: task,
                review: review,
                xp: xp,
                gp: gp,
                item: item,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( (response) => {
                var task = response.data.task;
                tasks[task.id] = task;
            });
        }
        questionModal.hide();
    }

    function del_task(tno) {
        window.axios.post('{{ route('game.task_remove') }}', {
            tid: tno,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            if (response.data.success) {
                delete tasks[response.data.success];
            }
        });
    }

    function set_coordinate(tno) {
        var answer = document.querySelector('input[name="answer' + qno + '"]:checked');
        window.axios.post('{{ route('game.task_moveto') }}', {
            tid: tno,
            x: answer.value,
            y: answer.value,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
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
            xhr.open( 'POST', '{{ route('game.image_upload') }}', true );
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
        .create( document.querySelector( '#story' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            licenseKey: '',
        })
        .then( editor => {
            window.story = editor;
        })
        .catch( error => {
            console.log( error );
        });
    ClassicEditor
        .create( document.querySelector( '#task' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            licenseKey: '',
        })
        .then( editor => {
            window.task = editor;
        })
        .catch( error => {
            console.log( error );
        });
</script>
@endsection
