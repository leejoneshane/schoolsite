@extends('layouts.game')

@section('content')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/translations/zh.js"></script>
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    學習任務管理
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.worksheets') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            學習單標題
        </th>
        <th scope="col" class="p-2">
            設計者
        </th>
        <th scope="col" class="p-2">
            科目名稱
        </th>
        <th scope="col" class="p-2">
            學習目標
        </th>
        <th scope="col" class="p-2">
            適用年級
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $worksheet->title }}</td>
        <td class="p-2">{{ $worksheet->teacher_name }}</td>
        <td class="p-2">{{ $worksheet->subject }}</td>
        <td class="p-2">{{ $worksheet->description }}</td>
        <td class="p-2">{{ $worksheet->grade->name }}</td>
    </tr>
</table>
<div class="w-full flex gap-4 justify-between">
    <canvas id="myCanvas" class="w-[32rem] h-[32rem]" onmousedown="draw(event)"></canvas>
    <div class="flex bg-white">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-col text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 border-purple-600" data-tabs-inactive-classes="text-gray-500 hover:text-gray-600 border-gray-100 hover:border-gray-300" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="dashboard-tab" data-tabs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Dashboard</button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="settings-tab" data-tabs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Settings</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="contacts-tab" data-tabs-target="#contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Contacts</button>
                </li>
            </ul>
        </div>
        <div id="default-tab-content">
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="w-full rounded-lg shadow dark:bg-blue-700">
                    <h3 id="head" class="text-center text-xl font-semibold text-gray-900 dark:text-white">編輯任務</h3>
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
                            <button onclick="save_task();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                儲存
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Dashboard tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Settings tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
                <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Contacts tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
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

    const canvas = document.getElementById("myCanvas");
    const ctx = canvas.getContext("2d");
    const orphan = document.getElementById("orphan");
    const normal = document.getElementById("normal");
    const map = new Image(512, 512);
    map.src = '{{ $worksheet->map->url() }}';
    map.addEventListener("load", (e) => {
        canvas.width = 512;
        canvas.height = 512;
        ctx.drawImage(map, 0, 0, canvas.width, canvas.height);
    });

    function getMousePos(evt) {
        var rect = canvas.getBoundingClientRect();
        return {
            x: evt.clientX - rect.left,
            y: evt.clientY - rect.top
        };
    }

    function draw(evt) {
        var pos = getMousePos(evt);
        console.log(pos);
    }

    function open_task(tno) {
        tid = tno;
        window.story.setData(tasks[tid].story);
        window.task.setData(tasks[tid].task);
        document.getElementById('review').checked = tasks[tid].review;
        document.getElementById('xp').value = tasks[tid].xp;
        document.getElementById('gp').value = tasks[tid].gp;
        var options = document.querySelector('option');
        options.forEach( opt => {
            if (opt.value == tasks[tid].item) {
                opt.setAttribute('selected', null);
            } else {
                opt.removeAttribute('selected');
            }
        });
        taskModal.show();
    }

    function save_task() {
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
