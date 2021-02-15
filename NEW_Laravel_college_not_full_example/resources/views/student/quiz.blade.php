@extends('layouts.app')

@section('title', ($exam ? __('Exam') : __('Test')) . ' "'. $SD->discipline->name .'"')

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@if($exam) @lang('Exam') @else @lang('Test') @endif "{{$SD->discipline->name}}"</h2></div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            @if(count($questions) == 0)
                                {{ __('Test not found') }} <a href="{{ route('study') }}"> {{ __('Back to discipline list') }}</a>
                            @else
                                <div v-if="!instructionAccept">
                                    <div class="col-md-12">
                                        <h2 style="margin-bottom: 20px">{{ __('Attention!') }}</h2>
                                        <ul>
                                            <li>{{ __('Each question can have from 1 to 4 correct answers.') }}</li>
                                            <li>{{ __('You can skip the question by clicking the "Next" button.') }}</li>
                                            <li>{{ __('You can return to the missed questions by clicking the “Missed” button.') }}</li>
                                            <li>{{ __('The total score is summed from the number of correct answers.') }}</li>
                                            <li>{{ __('Please be advised that the process of passing the test is being recorded, in case of unfair passing the exam, please follow the rules.') }}</li>
                                            @if($hasAudio)
                                                <li>
                                                    {{ __('Check sound please:') }}
                                                    <audio style="width: 100%; margin-top: 10px;" height="32" controls="controls">
                                                        <source src="/audio/audio_test.mp3" type="audio/mpeg">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </li>
                                            @endif
                                        </ul>
                                        <p>{{ __('We wish you a successful exam!') }}</p>
                                    </div>
                                    <div class="col-md-12 text-center" style="margin-top: 20px; margin-bottom: 20px">
                                        <a class="btn btn-info" v-bind:disabled="!loaded" v-on:click="startTesting" style="color: #fff;">@lang('Go to testing')</a>
                                    </div>
                                </div>

                                <div v-show="loaded && instructionAccept" style="display: none;">
                                        <div class="col-md-12" v-if="list.length > 0">
                                            <div class="alert alert-warning col-md-3">
    {{--                                            <i class="ion ion-clock " style="font-size:1.5em;"></i>&nbsp;--}}
                                                <span style="font-size:1.5em">@{{timeLimitMinutes}} : @{{timeLimitSeconds}}</span>
                                            </div>
                                            <div class="col-md-12">
                                                <h6 class="text-first-upper">{{__('question')}}:</h6>
                                                <div class="col-md-12" v-html="list[currentPage - 1].question" style="overflow-x: auto"></div>
                                                <div v-show="list[currentPage - 1].audiofiles && list[currentPage - 1].audiofiles.length > 0">
                                                    <audio style="width: 100%"
                                                           height="32"
                                                           controls
                                                            id="main_audio_player">
                                                        <source src="" type="audio/mpeg">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>
                                                <hr>
                                            </div>
                                            @if(isset($disciplineId))
                                                <input type="hidden" name="disciplineId" value="{{$disciplineId}}">
                                            @endif
                                            <div class="col-md-12">
                                                <h6 class="text-first-upper">{{__('choose answer')}}:</h6>
                                                <div class="col-md-12 radio" style="margin-top: 0px;" v-for="answer in list[currentPage - 1].answers">
                                                    <label>
                                                        <input
                                                                class="form-check-input"
                                                                type="radio"
                                                                v-model="list[currentPage - 1].answer"
                                                                v-if="!list[currentPage - 1].multi"
                                                                v-bind:value="answer.id"
                                                                v-bind:disabled="timeout"
                                                                v-bind:name="'radio-answer-' + answer.id"
                                                        />
                                                        <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                v-model="list[currentPage - 1].answer"
                                                                v-bind:disabled="checkMaxCorrect(answer.id) || timeout"
                                                                v-if="list[currentPage - 1].multi"
                                                                v-bind:value="answer.id"/>
                                                        <span v-html="answer.answer"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 no-padding" v-if="list.length > 1">
                                            <div class="col-md-12 text-center">
                                                <div class="col-md-12" style="margin: 20px 0; word-break: break-all;">
                                                    <a class="btn btn-primary" v-bind:class="{disabled: isNextBtnDisabled()}" v-on:click="currentPage++">@lang('next')</a>
                                                    <a class="btn btn-default" v-bind:class="{disabled: save}" v-if="nextUnanswered() > 0 || timeout" v-on:click="currentPage = nextUnanswered()">@lang('unanswered')</a>
                                                    <button class="btn btn-default" v-bind:disabled="save || timeout" v-on:click="finishTestClick()">@lang('finish test')</button>
                                                </div>
                                                <div class="col-md-12 text-center">
                                                    <b-pagination v-bind:disabled="save || timeout" v-bind:total-rows="list.length" v-model="currentPage" :per-page="1">
                                                    </b-pagination>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @if(count($questions) != 0)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.min.js"></script>
    <script src="/assets/js/nosleep.min.js"></script>
    <script type="text/javascript">
        window.instanciaVue = new Vue();

        @if($exam)
            const SUCCESS_LOCATION = '{{route('studentExamLastResult', ['id' => $disciplineId])}}';
            const FINISH_LOCATION = '{{route('studentExamPost', ['id' => $disciplineId])}}';
        @else
            const SUCCESS_LOCATION = '@if(isset($disciplineId)){{route('studentTest1LastResult', ['id' => $disciplineId])}}@else{{route('studentQuizKgeResult')}}@endif';
            const FINISH_LOCATION = '@if(isset($disciplineId)){{route('studentQuizPost', ['id' => $disciplineId])}}@else{{route('studentQuizKgePost')}}@endif';
        @endif
        const WEBCAM_ACCESS = {{$webCamAccess}};

        const HASH = '{{$hash}}';

        var app = new Vue({
            el: '#main-test-form',
            data: {
                list: [],
                currentPage: 1,
                process: false,
                timeLimit: null,
                timeLimitStart: null,
                timeLimitMinutes: '',
                timeLimitSeconds: '',
                timeout: false,
                loaded: false,
                save: false,
                instructionAccept: false,
                blur: false,
                blurStart: null,
                noSleep: null,
                recordedBlob: '',
                mediaRecorder: '',
                hasFine: false,
                sendingModalDialog: null,
                browser: ''
            },
            methods: {
                nextUnanswered: function () {
                    var startIndex = 0;

                    if (this.currentPage < this.list.length - 1) {
                        startIndex = this.currentPage + 1;
                    }

                    for (var i = this.currentPage; i <= this.list.length - 1; i++) {
                        if (this.list[i].answer == '' || this.list[i].answer.length == 0) {
                            return i + 1;
                        }
                    }

                    for (var i = 0; i < this.currentPage; i++) {
                        if (this.list[i].answer == '' || this.list[i].answer.length == 0) {
                            return i + 1;
                        }
                    }

                    return 0;
                },
                timeLimitChange: function () {
                    /*var now = new Date().getTime();
                    var diff = now - this.timeLimitStart.getTime();

                    if(diff >= 2000)
                    {
                        this.timeLimit = this.timeLimit - Math.floor(diff/1000);
                    } else {*/
                    this.timeLimit--
                    /*}

                    this.timeLimitStart = new Date();*/
                },
                finishTestClick: function () {
                    // Disable buttons
                    this.save = true;

                    var self = this;

                    bootbox.confirm('@lang('Are you sure you want to finish testing?')', function(result) {
                        if (result) {
                            clearInterval(this.timerQuiz);
                            self.finishTest();
                            if(WEBCAM_ACCESS === 1){
                                self.downloadVideo();
                            }
                        } else {
                            // Enable buttons
                            self.save = false;
                        }
                    });
                },
                getQuestionList: function() {
                    var questionList = [];

                    for (var i = 0; i < this.list.length; i++) {
                        questionList.push({
                            id: this.list[i].id,
                            answer: this.list[i].answer
                        });
                    }

                    return questionList;
                },
                finishTest: function () {
                    var self = this;

                    var questionList = this.getQuestionList();

                    if (self.sendingModalDialog == null) {
                        self.sendingModalDialog = bootbox.dialog({
                            message: '<p><i class="fa fa-spin fa-spinner"></i> Ответы обрабатываются...</p>',
                            closeButton: false,
                            centerVertical: true
                        });
                    } else {
                        self.sendingModalDialog.find('.bootbox-body').html('<p><i class="fa fa-spin fa-spinner"></i> Ответы обрабатываются...</p>');
                    }

                    self.sendingModalDialog.init(function() {
                        axios.post(
                            FINISH_LOCATION,
                            {
                                questionList: questionList,
                                blur: self.blur,
                                hash: HASH
                            },
                            {timeout:10000}
                        )
                            .then(function (response) {
                                if (response.data.success) {
                                    self.sendingModalDialog.find('.bootbox-body').html('Ответы обработаны.');
                                    location.replace(SUCCESS_LOCATION);
                                } else {
                                    bootbox.alert('Error.' + response.data.error);

                                    // Enable buttons
                                    self.save = false;
                                }
                            })
                            .catch(err => {
                                if (err.code === 'ECONNABORTED') {
                                    self.sendingModalDialog.find('.bootbox-body').html('Сервер не ответил. Нажмите, пожалуйста, кнопку для повтора. <input type="button" value="Отправить ответы" onclick="app.finishTest();" />');
                                }
                            });
                    });
                },
                checkMaxCorrect: function (answerId) {
                    var elem = this.list[this.currentPage - 1];

                    return elem.answer.length >= elem.correctCount
                        && elem.answer.indexOf(answerId) == -1;
                },
                testHasAudio: function () {
                    for (var i = 0; i < this.list.length; i++) {
                        if (this.list[i].audiofiles && this.list[i].audiofiles.length > 0) {
                            return true;
                        }
                    }

                    return false;
                },
                isNextBtnDisabled: function () {
                    return this.currentPage == this.list.length || this.save || this.timeout;
                },
                fine: function () {
                    this.hasFine = true;
                    clearInterval(this.timerQuiz);
                    this.finishTest();
                },
                setBlur: function () {
                    this.blurStart = new Date();
                },
                setBlurFine: function () {
                    this.blur = true;
                    this.fine();
                },
                unsetBlur: function () {
                    var now = new Date().getTime();

                    if (now - this.blurStart.getTime() > 5000) {
                        this.setBlurFine()
                    }
                },
                onBlur: function () {
                    instanciaVue.$emit('blur');
                },
                onFocus: function () {
                    instanciaVue.$emit('unblur');
                },
                downloadVideo: function() {
                    if (self.sendingModalDialog == null) {
                        self.sendingModalDialog = bootbox.dialog({
                            message: '<p><i class="fa fa-spin fa-spinner"></i> Ответы обрабатываются...</p>',
                            closeButton: false,
                            centerVertical: true
                        });
                    } else {
                        self.sendingModalDialog.find('.bootbox-body').html('<p><i class="fa fa-spin fa-spinner"></i> Ответы обрабатываются...</p>');
                    }
                    if(app.recordedBlob === [] || app.recordedBlob === ''){
                        return;
                    }
                    let blob = new Blob(app.recordedBlob, {type: 'video/webm'});
                    let fd = new FormData();
                    fd.append('video', blob);
                    fd.append('discipline_id', '{{$disciplineId}}')
                    fd.append('test_type', '{{$testType}}')

                    axios({
                        method: 'post',
                        url: '{{route('webcam.index')}}',
                        data: fd,
                    }).then(function(data) {
                        window.stream.getTracks()[0].stop();
                        window.stream.getTracks()[1].stop();
                        app.finishTest();
                    });
                },
                startRecording: function () {
                    navigator.mediaDevices.getUserMedia({ audio: true,video: true})
                        .then((stream) => {
                            console.log('getUserMedia() got stream: ', stream);
                            window.stream = stream;
                            app.recordedBlob = [];
                            try {
                                app.mediaRecorder = new MediaRecorder(window.stream);
                            } catch (e) {

                                return;
                            }
                            console.log('Created MediaRecorder', app.mediaRecorder);
                            app.mediaRecorder.ondataavailable =  function (event) {
                                if (event.data && event.data.size > 0) {
                                    app.recordedBlob.push(event.data);
                                }
                            };
                            app.mediaRecorder.start(10);
                            console.log('MediaRecorder started', app.mediaRecorder);
                        })
                        .catch((error) => {
                            console.log('navigator.getUserMedia error: ', error);
                        });
                },
                checkPermissions: async function () {
                    if(this.browser === 'Firefox'){
                        return {
                            camera: 'granted',
                            microphone: 'granted'
                        }
                    }
                    let percam = await navigator.permissions.query({ name: 'camera' })
                    let permic = await  navigator.permissions.query({ name: 'microphone' })

                    return {
                        camera: percam.state,
                        microphone: permic.state
                    }
                },
                startTesting: function () {
                    if(WEBCAM_ACCESS === 1 ){
                        this.checkPermissions()
                            .then( e => {
                                if(e.camera === "granted" && e.microphone === "granted"){
                                    this.instructionAccept = true
                                    this.startRecording()
                                } else {
                                     this.instructionAccept = true
                                   // bootbox.alert('{{__("Please allow access to it.")}}');
                                }
                        })
                    } else {
                        this.instructionAccept = true
                    }
                        this.instructionAccept = true
                },
                getRights: function () {
                    navigator.mediaDevices.getUserMedia({
                        audio: true,
                        video: true
                    }).then( stream => {
                        stream.getTracks()[0].stop();
                        stream.getTracks()[1].stop();
                    })
                }
            },/*
        computed: {
            actualAudioFile: function()
            {
                if(this.list[this.currentPage - 1] && this.list[this.currentPage - 1].audiofiles) {
                    return '/audio/' + this.list[this.currentPage - 1].audiofiles[0];
                }
                return '';
            }
        },*/
            created: function () {
                this.noSleep = new NoSleep();
                this.noSleep.enable();

                @foreach($questions as $question)
                    this.list.push({
                    id: {{ $question->id }},
                    multi: {{ $question->has_multi_answer ? 'true' : 'false' }},
                    correctCount: {{ $question->getCorrectAnswersCount() }},
                    question: `{!! \App\Services\QuizService::textSafety($question->question) !!}`,
                    answer: [],
                    @if(count($question->audiofiles) > 0)
                    audiofiles: [
                        @foreach($question->audiofiles as $k => $audio)
                            '{{ $audio->filename }}'@if($k+1 < count($question->audiofiles)),@endif
                        @endforeach
                    ],
                    @endif
                    answers: [
                            @foreach($question->answers as $k => $answer)
                        {
                            id: {{ $answer->id }},
                            answer: `{!! \App\Services\QuizService::textSafety($answer->answer) !!}`
                        }@if($k+1 < count($question->answers)),@endif
                        @endforeach
                    ]
                });
                @endforeach

                this.timeLimit = {{ $timeLimit }};
                this.timeLimitStart = new Date();
                //this.timerQuiz = setInterval(this.timeLimitChange, 1000);
                this.timeLimitMinutes = '00';
                this.timeLimitSeconds = '00';

                this.loaded = true

                if (navigator.userAgent.search(/Firefox/) > 0) {
                    this.browser = 'Firefox'
                };

                this.checkPermissions()
                    .then( e => {
                        if(e.camera === "prompt" || e.microphone === "prompt"){
                            this.getRights()
                        }
                    })
            },
            watch: {
                timeLimit: function (newVal, oldVal) {
                    if (newVal <= 0 && !this.process) {
                        clearInterval(this.timerQuiz);
                        this.timeout = true;
                        this.finishTest();
                    }

                    var timeLimitMinutes = Math.floor(this.timeLimit / 60);
                    var timeLimitSeconds = this.timeLimit - (timeLimitMinutes * 60);

                    this.timeLimitMinutes = timeLimitMinutes > 9 ? timeLimitMinutes : ('0' + timeLimitMinutes);
                    this.timeLimitSeconds = timeLimitSeconds > 9 ? timeLimitSeconds : ('0' + timeLimitSeconds);
                },
                instructionAccept: function (newVal, oldVal) {
                    if (newVal == true) {
                        window.addEventListener('blur', this.onBlur);
                        window.addEventListener('focus', this.onFocus);
                        instanciaVue.$on('blur', this.setBlur);
                        instanciaVue.$on('unblur', this.unsetBlur);

                        this.timerQuiz = setInterval(this.timeLimitChange, 1000);

                        if (this.list[0].audiofiles && $('audio')[0])
                        {
                            try {
                                $('#main_audio_player').attr('src', '/audio/' + this.list[0].audiofiles[0]);
                                $('#main_audio_player')[0].load();
                            } catch (e) {
                                console.log(e);
                            }
                        }
                    }
                },
                currentPage: function (newVal, oldVal) {

                    $('#main_audio_player')[0].pause();
                    if (this.list[this.currentPage - 1].audiofiles)
                    {
                        try {
                            $('#main_audio_player').attr('src', '/audio/' + this.list[this.currentPage - 1].audiofiles[0]);
                            $('#main_audio_player')[0].load();
                        } catch (e) {
                            console.log(e);
                        }
                    }
                }
            },
            destroyed: function () {
                clearInterval(this.timerQuiz);
            }
        });
    </script>
    @endif
@endsection