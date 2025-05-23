@extends('layouts.game')

@section('content')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/translations/zh.js"></script>
<div class="text-2xl font-bold leading-normal pb-5 drop-shadow-md">
    試題管理
    <a class="text-sm py-2 pl-6 rounded text-blue-500 hover:text-blue-600" href="{{ route('game.evaluates') }}">
        <i class="fa-solid fa-eject"></i>返回上一頁
    </a>
</div>
<table class="w-full px-4 text-left font-normal">
    <tr class="bg-gray-300 dark:bg-gray-500 font-semibold text-lg">
        <th scope="col" class="p-2">
            試卷名稱
        </th>
        <th scope="col" class="p-2">
            科目名稱
        </th>
        <th scope="col" class="p-2">
            出題範圍
        </th>
        <th scope="col" class="p-2">
            適用年級
        </th>
    </tr>
    <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-700 dark:even:bg-gray-600">
        <td class="p-2">{{ $evaluate->title }}</td>
        <td class="p-2">{{ $evaluate->subject }}</td>
        <td class="p-2">{{ $evaluate->range }}</td>
        <td class="p-2">{{ $evaluate->grade->name }}</td>
    </tr>
</table>
<div class="w-full border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2 mt-5" role="alert">
    <label>注意事項：</label>
    <ul>
        <li>
            　　1. 此試卷用途為學生自主練習，題目和選項順序隨機排列，每次練習會留下歷程紀錄，方便教師追蹤結果。
        </li>
        <li>
            　　2. 試卷僅支援單一選擇題，不支援複選，是非題請改用選擇題格式，由於填充題無法自動閱卷，因此不提供支援。
        </li>
        <li>
            　　3. 選項沒有限制數量，若為正確答案，請點擊後面的選項按鈕！
        </li>
    </ul>
</div>
<table class="w-full p-4 bg-white text-left font-normal">
<tbody id="qlist">
@foreach ($evaluate->questions as $q)
    <tr id="q{{ $q->id }}" class="bg-teal-100 text-black font-semibold text-lg">
        <td class="w-12 p-2">{{ $q->sequence }}</td>
        <td id="caption{{ $q->id }}" class="p-2">{!! $q->question !!}</td>
        <td id="score{{ $q->id }}" class="p-2">配分：{{ $q->score }}</td>
        <td class="w-48 p-2">
            <button class="mx-3 text-blue-300 hover:text-blue-600" title="新增選項" onclick="open_option({{ $q->id }}, 0);">
                <i class="fa-solid fa-circle-plus"></i>
            </button>
            <button class="mx-3 text-blue-300 hover:text-blue-600" title="編輯" onclick="open_question({{ $q->id }});">
                <i class="fa-solid fa-pen"></i>
            </button>
            <button class="mx-3 text-red-300 hover:text-red-600" title="刪除" onclick="del_question({{ $q->id }});">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
        <td class="w-1/2"></td>
    </tr>
    <tr class="bg-white dark:bg-gray-700">
        <td class="p-2" colspan="4"></td>
        <td>
            <table class="w-full float-right text-left font-normal">
                <tbody id="olist{{ $q->id }}">
                    @foreach ($q->options as $o)
                    <tr class="odd:bg-white even:bg-gray-100">
                        <td class="w-12 p-2">{{ $o->sequence }}</td>
                        <td id="option{{ $o->id }}" class="p-2">{!! $o->option !!}</td>
                        <td class="w-48 p-2">
                            <input type="radio" name="answer{{ $q->id }}" value="{{ $o->id }}"{{ $q->answer == $o->id ? ' checked' : '' }} onchange="set_answer({{ $q->id }});" class="mx-3" title="設為答案">
                            <button class="mx-3 text-blue-300 hover:text-blue-600" title="編輯" onclick="open_option({{ $q->id }}, {{ $o->id }});">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="mx-3 text-red-300 hover:text-red-600" title="刪除" onclick="del_option({{ $o->id }});">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
@endforeach
    <tr id="latest">
        <td colspan="4">
            <button class="py-2 pr-6 text-blue-300 hover:text-blue-600" onclick="open_question(0);">
                <i class="fa-solid fa-circle-plus"></i>新增題目
            </button>
        </td>
    </tr>
</tbody>
</table>
<div class="py-8"> </div>
<div id="questionModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">編輯題目</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <p><div class="p-3">
                    <label class="text-base">題目：</label>
                    <div id="question"></div>
                </div></p>
                <p><div class="p-3">
                    <label for="score" class="text-base">配分：</label>
                    <input type="text" id="score" value="" class="w-1/3 rounded border border-gray-300 focus:border-blue-700 focus:ring-1 focus:ring-blue-700 focus:outline-none active:outline-none dark:border-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600  bg-white dark:bg-gray-700" required>
                </div></p>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="save_question();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    儲存
                </button>
                <button onclick="questionModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div id="optionModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[60] hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-auto h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-blue-700">
            <div class="p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-center text-xl font-semibold text-gray-900 dark:text-white">編輯選項</h3>
            </div>
            <div class="p-6 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <p><div class="p-3">
                    <label class="text-base">選項：</label>
                    <div id="option"></div>
                </div></p>
            </div>
            <div class="w-full inline-flex justify-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="save_option();" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    儲存
                </button>
                <button onclick="optionModal.hide();" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<script nonce="selfhost">
    var eid = {{ $evaluate->id }};
    var qid;
    var oid;
    var questions = [];
    var options = [];
    @foreach ($evaluate->questions as $q)
    questions[{{ $q->id }}] = {!! $q->toJson(JSON_UNESCAPED_UNICODE); !!};
    @foreach ($q->options as $o)
    options[{{ $o->id }}] = {!! $o->toJson(JSON_UNESCAPED_UNICODE); !!};
    @endforeach
    @endforeach

    var $targetEl = document.getElementById('questionModal');
    const questionModal = new window.Modal($targetEl);
    var $targetEl = document.getElementById('optionModal');
    const optionModal = new window.Modal($targetEl);

    function open_question(qno) {
        qid = qno;
        var question = document.getElementById('question');
        var score = document.getElementById('score');
        if (qid == 0) {
            window.question.setData('');
            score.value = '';
        } else {
            window.question.setData(questions[qid].question);
            score.value = questions[qid].score;
        }
        questionModal.show();
    }

    function save_question() {
        var question = window.question.getData();
        var score = document.getElementById('score');
        if (qid == 0) {
            window.axios.post('{{ route('game.question_add') }}', {
                eid: eid,
                question: question,
                score: score.value,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( (response) => {
                qid = response.data.id;
                questions[qid] = response.data.question;
                var qlist = document.getElementById('qlist');
                var last = document.getElementById('latest');
                var tr = document.createElement('tr');
                tr.setAttribute('id', 'q' + qid);
                tr.classList.add('bg-teal-100','font-semibold','text-lg');
                var td = document.createElement('td');
                td.classList.add('w-12', 'p-2');
                td.innerHTML = questions[qid].sequence;
                tr.appendChild(td);
                td = document.createElement('td');
                td.setAttribute('id', 'caption' + qid);
                td.classList.add('p-2');
                td.innerHTML = questions[qid].question;
                tr.appendChild(td);
                td = document.createElement('td');
                td.setAttribute('id', 'score' + qid);
                td.classList.add('p-2');
                td.innerHTML = '配分：' + questions[qid].score;
                tr.appendChild(td);
                td = document.createElement('td');
                td.classList.add('p-2');
                var btn = document.createElement('button');
                btn.classList.add('mx-3','text-blue-300','hover:text-blue-600');
                btn.setAttribute('title', '新增選項');
                btn.setAttribute('onclick', 'open_option(' + qid + ',0);');
                btn.innerHTML = '<i class="fa-regular fa-circle-dot"></i>';
                td.appendChild(btn);
                var btn = document.createElement('button');
                btn.classList.add('mx-3','text-blue-300','hover:text-blue-600');
                btn.setAttribute('title', '編輯');
                btn.setAttribute('onclick', 'open_question(' + qid + ');');
                btn.innerHTML = '<i class="fa-solid fa-pen"></i>';
                td.appendChild(btn);
                var btn = document.createElement('button');
                btn.classList.add('mx-3','text-red-300','hover:text-red-600');
                btn.setAttribute('title', '刪除');
                btn.setAttribute('onclick', 'del_question(' + qid + ');');
                btn.innerHTML = '<i class="fa-solid fa-trash"></i>';
                td.appendChild(btn);
                tr.appendChild(td);
                td = document.createElement('td');
                td.classList.add('w-1/2');
                tr.appendChild(td);
                qlist.insertBefore(tr, last);
                var tr = document.createElement('tr');
                tr.classList.add('bg-white');
                var td = document.createElement('td');
                td.classList.add('p-2');
                td.setAttribute('colspan', 4);
                tr.appendChild(td);
                td = document.createElement('td');
                var table = document.createElement('table');
                table.classList.add('w-full','float-right','text-left','font-normal');
                var tbody = document.createElement('tbody');
                tbody.setAttribute('id', 'olist' + qid);
                table.appendChild(tbody);
                td.appendChild(table);
                tr.appendChild(td);
                qlist.insertBefore(tr, last);
            });
        } else {
            window.axios.post('{{ route('game.question_edit') }}', {
                qid: qid,
                question: question,
                score: score.value,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( (response) => {
                questions[qid] = response.data.question;
                var caption = document.getElementById('caption' + qid);
                caption.innerHTML = questions[qid].question;
                var score = document.getElementById('score' + qid);
                score.innerHTML = questions[qid].score;
            });
        }
        questionModal.hide();
    }

    function del_question(qno) {
        window.axios.post('{{ route('game.question_remove') }}', {
            qid: qno,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            if (response.data.success) {
                var qlist = document.getElementById('qlist');
                var quest = document.getElementById('q' + qno);
                qlist.removeChild(quest);
            }
        });
    }

    function set_answer(qno) {
        var answer = document.querySelector('input[name="answer' + qno + '"]:checked');
        window.axios.post('{{ route('game.question_answer') }}', {
            qid: qno,
            oid: answer.value,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }

    function open_option(qno, ono) {
        qid = qno;
        oid = ono;
        var option = document.getElementById('option');
        if (oid == 0) {
            window.option.setData('');
        } else {
            window.option.setData(options[oid].option);
        }
        optionModal.show();
    }

    function save_option() {
        var option = window.option.getData();
        if (oid == 0) {
            window.axios.post('{{ route('game.option_add') }}', {
                qid: qid,
                option: option,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( (response) => {
                oid = response.data.id;
                options[oid] = response.data.option;
                var olist = document.getElementById('olist' + qid);
                var tr = document.createElement('tr');
                tr.classList.add('odd:bg-white','even:bg-gray-100');
                var td = document.createElement('td');
                td.classList.add('p-2');
                td.innerHTML = options[oid].sequence;
                tr.appendChild(td);
                td = document.createElement('td');
                td.setAttribute('id', 'option' + oid);
                td.classList.add('p-2');
                td.innerHTML = options[oid].option;
                tr.appendChild(td);
                td = document.createElement('td');
                td.classList.add('p-2');
                var input = document.createElement('input');
                input.classList.add('mx-3');
                input.setAttribute('type', 'radio');
                input.setAttribute('name', 'answer' + qid);
                input.setAttribute('value', oid);
                input.setAttribute('onchange', 'set_answer(' + qid + ');');
                input.setAttribute('title', '設為答案');
                td.appendChild(input);
                var btn = document.createElement('button');
                btn.classList.add('mx-3','text-blue-300','hover:text-blue-600');
                btn.setAttribute('title', '編輯');
                btn.setAttribute('onclick', 'open_option(' + qid + ',' + oid + ');');
                btn.innerHTML = '<i class="fa-solid fa-pen"></i>';
                td.appendChild(btn);
                var btn = document.createElement('button');
                btn.classList.add('mx-3','text-red-300','hover:text-red-600');
                btn.setAttribute('title', '刪除');
                btn.setAttribute('onclick', 'del_option(' + oid + ');');
                btn.innerHTML = '<i class="fa-solid fa-trash"></i>';
                td.appendChild(btn);
                tr.appendChild(td);
                olist.appendChild(tr);
            });
        } else {
            window.axios.post('{{ route('game.option_edit') }}', {
                oid: oid,
                option: option,
            }, {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then( (response) => {
                options[oid] = response.data.option;
                var option = document.getElementById('option' + oid);
                option.innerHTML = options[oid].option;
            });
        }
        optionModal.hide();
    }

    function del_option(ono) {
        window.axios.post('{{ route('game.option_remove') }}', {
            oid: ono,
        }, {
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then( (response) => {
            if (response.data.success) {
                var qlist = document.getElementById('qlist');
                var opt = document.getElementById('o' + ono);
                qlist.removeChild(opt);
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
        .create( document.querySelector( '#question' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            licenseKey: '',
        })
        .then( editor => {
            window.question = editor;
        })
        .catch( error => {
            console.log( error );
        });
    ClassicEditor
        .create( document.querySelector( '#option' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            licenseKey: '',
        })
        .then( editor => {
            window.option = editor;
        })
        .catch( error => {
            console.log( error );
        });
</script>
@endsection
