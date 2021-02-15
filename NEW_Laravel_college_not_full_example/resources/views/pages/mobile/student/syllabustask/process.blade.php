@extends('layouts.app')

@section('title', __('Proceed with the task'))

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-task-form">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Proceed with the task')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body" style="min-height:500px;overflow:hidden;">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <div v-if="!instructionAccept">
                                @include('student.syllabustask.proceed')
                            </div>


                            <div v-if="errorMessage" :class="{ 'alert-danger': isError, 'alert-success': !isError }" class="alert margin-t20 margin-b20">
                                <div v-html="errorMessage"> </div>
                            </div>


                            <div v-show="loaded && instructionAccept" style="display: none;">

                                @if(count($task->questions) == 0)
                                    {{ __('Test not found') }} <a href="{{ route('userProfile') }}"> {{ __('Back to discipline list') }}</a>
                                @else

                                    <div class="col-md-12" v-if="list.length > 0">
                                        <div class="alert alert-warning col-md-3">
                                            <i class="fas fa-clock" style="font-size:1.5em;"></i>
                                            <span style="font-size:1.5em">@{{timeLimitMinutes}} : @{{timeLimitSeconds}}</span>
                                        </div>
                                        <div class="col-md-12">

                                            <br>
                                            <h6 class="text-first-upper">{{__('Task')}}:</h6>
                                            @if( $task->type == \App\SyllabusTask::TYPE_IMAGE )
                                                <img src="{{ '/images/uploads/syllabustask/' . $task->img_data }}" class="img-thumbnail margin-15" style="display:flex;max-height:300px;" alt="" />
                                            @elseif( $task->type == \App\SyllabusTask::TYPE_LINK )
                                                <a href="{{ $task->link_data }}" target="_blank"></a>
                                            @elseif( $task->type == \App\SyllabusTask::TYPE_AUDIO )
                                                <audio
                                                        style="width: 100%"
                                                        src="{{ '/audio/' . $task->audio_data }}"
                                                        controls></audio>
                                            @elseif( $task->type == \App\SyllabusTask::TYPE_VIDEO )
                                                <iframe width="560" height="315" src="{{ \App\SyllabusTask::getVideoLink($task->video_data) }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            @else
                                                <p>{{ $task->text_data }}</p>
                                            @endif

                                            <h6 class="text-first-upper">{{__('question')}}:</h6>
                                            <div class="col-md-12" v-html="this.list[currentPage - 1].question" style="overflow-x: auto"></div>
                                            {{--
                                            <div v-if="taskList[currentPage - 1].audiofiles && list[currentPage - 1].audiofiles.length > 0">
                                                <audio style="width: 100%"
                                                       height="32"
                                                       controls>
                                                    <source v-bind:src="'/audio/' + taskList[currentPage - 1].audiofiles[0]" type="audio/mpeg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                            --}}
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
                                                            v-if="!list[currentPage - 1].multi"
                                                            class="form-check-input"
                                                            type="radio"
                                                            v-model="list[currentPage - 1].answer"
                                                            v-bind:value="answer.id"
                                                            v-bind:disabled="timeout"
                                                            v-bind:name="'radio-answer-' + answer.id"
                                                    />
                                                    <input
                                                            v-if="list[currentPage - 1].multi"
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            v-model="list[currentPage - 1].answer"
                                                            v-bind:value="answer.id"
                                                            v-bind:disabled="timeout"
                                                            v-bind:name="'radio-answer-' + answer.id"
                                                            @change="testLimitAnswers(currentPage,'radio-answer-' + answer.id)"
                                                            :id="'radio-answer-' + answer.id"
                                                    />
                                                    <span v-html="answer.answer"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 no-padding">
                                        <div class="col-md-12 text-center">
                                            <div class="col-md-12" style="margin: 20px 0; word-break: break-all;">
                                                <a class="btn btn-primary text-white" v-bind:class="{ disabled: isNextBtnDisabled() }" v-on:click="currentPage++"> {{__('next')}}</a>
                                                <a class="btn btn-default" v-bind:class="{ disabled: save}" v-if="nextUnanswered() > 0 || timeout" v-on:click="currentPage = nextUnanswered()"> {{__('unanswered')}} </a>
                                                <button class="btn btn-default" v-bind:disabled="save || timeout" v-on:click="finishTestClick()">{{__('finish test')}}</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 no-padding" v-if="list.length > 1">
                                        <div class="col-md-12 text-center">
                                            <div class="col-md-12 text-center">
                                                <b-pagination v-bind:disabled="timeout" v-bind:total-rows="list.length" v-model="currentPage" :per-page="1">
                                                </b-pagination>
                                            </div>
                                        </div>
                                    </div>

                                @endif

                            </div>


                        </div>
                        <div class="col-md-2"></div>
                    </div>


                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script src="/assets/js/nosleep.min.js"></script>
    <script type="text/javascript">

        window.instanciaVue = new Vue();
        var app = new Vue({
            el: '#main-task-form',
            data: {

                instructionAccept: false,
                loaded: false,
                list: [],
                currentPage: 1,
                process: false,
                timeLimit: null,
                timeLimitStart: null,
                timeLimitMinutes: '',
                timeLimitSeconds: '',
                timeout: false,
                save: false,
                blur: false,
                blurStart: null,
                noSleep: null,
                hasFine: false,
                timerTask: null,
                isError: false,
                errorMessage: ''

            },
            methods: {


                startTest: function(){
                    /*
                     window.addEventListener('blur', this.onBlur);
                     window.addEventListener('focus', this.onFocus);
                     instanciaVue.$on('blur', this.setBlur);
                     instanciaVue.$on('unBlur', this.unsetBlur);
                     */
                    this.instructionAccept = true;
                },
                nextUnanswered: function() {

                    var startIndex = 0;

                    if(this.currentPage < this.list.length -1)
                    {
                        startIndex = this.currentPage + 1;
                    }

                    for(var i = this.currentPage; i <= this.list.length -1; i++) {
                        if(this.list[i].answer == '' || this.list[i].answer.length == 0) {
                            return i+1;
                        }
                    }

                    for(var i = 0; i < this.currentPage; i++) {
                        if(this.list[i].answer == '' || this.list[i].answer.length == 0) {
                            return i+1;
                        }
                    }

                    return 0;
                },
                timeLimitChange: function() {

                    this.timeLimit--;
                },
                finishTest: function() {

                    this.isError = false;
                    this.errorMessage = '';

                    var dataQuestionList = {
                        syllabus_id: '{{ $task->syllabus_id }}',
                        task_id: '{{ $task->id }}',
                        payed: '{{ $task->pay->payed ?? 0 }}',
                        points: '{{ $task->points }}',
                        answers: []
                    };

                    for(var i=0; i<this.list.length; i++)
                    {
                        dataQuestionList.answers.push({
                            answer: this.list[i].answer
                        });
                    }
                    this.save = true;

                    var self = this;
                    axios.post('{{route('sroSaveResult')}}', {
                        "_token": "{{ csrf_token() }}",
                        questionList: dataQuestionList,
                        discipline_id: '{{ $discipline_id }}'
                    })
                        .then(function(response){

                            if( response.data.status ){

                                window.location.href="{{ route('sroGetList',['discipline_id'=>$discipline_id]) }}";
                            } else {

                                self.isError = true;
                                self.errorMessage = response.data.message;
                            }

                        });

                },
                finishTestClick: function() {
                    if(confirm('{{ __('Are you sure you want to finish testing?') }}')) {
                        clearInterval(this.timerTask);
                        this.finishTest();
                    }
                },
                checkMaxCorrect: function(answerId){
                    var elem = this.list[this.currentPage - 1];

                    return elem.answer.length >= elem.correctCount
                        && elem.answer.indexOf(answerId) == -1;
                },
                testHasAudio: function() {
                    for(var i=0; i<this.list.length; i++) {
                        if(this.list[i].audiofiles && this.list[i].audiofiles.length > 0) {
                            return true;
                        }
                    }

                    return false;
                },
                isNextBtnDisabled : function () {

                    return this.currentPage == this.list.length || this.save || this.timeout;
                },
                fine: function() {

                    this.hasFine = true;
                    clearInterval(this.timerTask);
                    this.finishTest();
                },
                testLimitAnswers: function(currentPage,id){

                    if( this.list[currentPage - 1].answer.length > this.list[currentPage - 1].multi ){

                        $('#'+id).prop('checked',false);
                        $('#radio-answer-'+id).prop('checked',false);
                        this.isError = true;
                        this.errorMessage = '{{ __('Error, you have chosen many answers') }}';
                        var cur = this.list[currentPage - 1].answer.pop();
                        return false;

                    } else {

                        this.isError = false;
                        this.errorMessage = '';
                    }

                }
                /*
                 setBlur: function() {

                 this.blurStart = new Date();
                 },
                 setBlurFine: function() {

                 this.blur = true;
                 this.fine();
                 },

                 unsetBlur: function() {

                 var now = new Date().getTime();

                 if(now - this.blurStart.getTime() > 5000)
                 {
                 this.setBlurFine()
                 }
                 },
                 onBlur: function() {

                 instanciaVue.$emit('blur');
                 },
                 onFocus: function() {

                 instanciaVue.$emit('unBlur');
                 }
                 */

            },
            created: function(){

                this.noSleep = new NoSleep();
                this.noSleep.enable();

                @foreach($task->questions as $question)
                    this.list.push({
                    id: {{ $question->id }},
                    task_id: {{ $task->id }},
                    correctCount: {{ $question->getCorrectAnswersCount() }},
                    question: `{!! trim(strip_tags($question->question, '<img><table><tbody><thead><tfoot><tr><th><td>')) !!}`,
                    answer: [],
                    answers: [
                            @foreach( $question->answer as $k => $answer )
                        {
                            id: {{ $answer->id }},
                            answer: `{!! trim(strip_tags($answer->answer, '<img><table><tbody><thead><tfoot><tr><th><td>')) !!}`,
                            points: {{ $answer->points }},
                            correct: {{ $answer->correct }}
                        }@if($k+1 < count($question->answer)),@endif
                        @endforeach
                    ],
                    multi: '{{ !empty($question->multi) ? $question->multi : null }}'
                });
                @endforeach

                    this.timeLimit = {{ $timeLimit }};
                this.timeLimitStart = new Date();
                this.timeLimitMinutes = '00';
                this.timeLimitSeconds = '00';
                this.loaded = true;

            },
            watch: {
                timeLimit: function(newVal, oldVal) {
                    if(newVal <= 0 && !this.process) {
                        clearInterval(this.timerTask);
                        this.timeout = true;
                        this.finishTest();
                    }

                    var timeLimitMinutes = Math.floor( this.timeLimit / 60 );
                    var timeLimitSeconds = this.timeLimit - (timeLimitMinutes * 60);

                    this.timeLimitMinutes = timeLimitMinutes > 9 ? timeLimitMinutes : ('0' + timeLimitMinutes);
                    this.timeLimitSeconds = timeLimitSeconds > 9 ? timeLimitSeconds : ('0' + timeLimitSeconds);
                },
                instructionAccept: function(newVal, oldVal)
                {
                    if(newVal == true)
                    {
                        this.timerTask = setInterval(this.timeLimitChange, 1000);
                    }
                },
                currentPage: function(newVal, oldVal) {
                    /*
                     try
                     {
                     $('audio').load();
                     } catch (e) {

                     }
                     */
                }
            },
            destroyed: function() {
                clearInterval(this.timerTask);
            }
        });

    </script>
@endsection