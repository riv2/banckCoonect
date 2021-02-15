<div id="task_r2">

    <h4>Задания</h4>

    <div class="pull-right">
        <button @click="syllabusShowedModal" :disabled="syllabusTaskProcessRequest" class="btn btn-primary" type="button" data-toggle="modal" data-target="#syllabusEditModal">Добавить задание</button>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="col-md-12 padding-t30">

        <div v-if="syllabusTaskMessage" :class="{ 'alert-danger': syllabusTaskError, 'alert-success': !syllabusTaskError }" class="alert margin-b20">
            <div v-html="syllabusTaskMessage"> </div>
        </div>

        <div class="accordion" id="accordionTask">
            <template v-for="(itemModel, indexT) in syllabusTaskModels">
            <div class="card padding-15" style="border: 1px solid #dddddd;">
                {{-- Header --}}
                <div :id="'heading' + itemModel.id" class="card-header">
                    <h5 class="mb-0">
                        <button :data-target="'#collapseT' + indexT" class="btn btn-link" type="button" data-toggle="collapse" aria-expanded="true" :aria-controls="'collapseT' + indexT">
                            Задание @{{ indexT + 1 }}
                        </button>
                        <div class="pull-right">
                            <button @click="syllabusShowedModal(itemModel)" :disabled="syllabusTaskProcessRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                            <button @click="SyllabusRemoveTask(itemModel)" :disabled="syllabusTaskProcessRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                        </div>
                    </h5>
                </div>
                {{-- panel --}}
                <div :id="'collapseT' + indexT" class="collapse show" :aria-labelledby="'heading' + itemModel.id" data-parent="#accordionTask">
                    <div class="card-body">

                        <table class="table margin-b20">
                            <tr>
                                <th width="250"> Тип: </th>
                                <td v-html="syllabusTaskTypeSelectListTranslate[itemModel.type]"></td>
                            </tr>
                            <tr v-if="itemModel.week">
                                <th> Неделя: </th>
                                <td> <p> @{{ itemModel.week }} </p> </td>
                            </tr>
                            <tr>
                                <th> Баллы: </th>
                                <td> @{{ itemModel.points }} </td>
                            </tr>
                            <tr v-if="itemModel.type == 'text' || itemModel.type == 'essay'">
                                <th> Задание: </th>
                                <td> <p> @{{ itemModel.text_data }} </p> </td>
                            </tr>
                            <tr v-if="itemModel.type == 'img'">
                                <th> Задание: </th>
                                <td> <img v-if="itemModel.img_data" :src="'/images/uploads/syllabustask/' + itemModel.img_data" class="img-thumbnail margin-15" style="display:flex;max-height:300px;"> </td>
                            </tr>
                            <tr v-if="itemModel.type == 'link'">
                                <th> Задание: </th>
                                <td> <a :href="itemModel.link_data" target="_blank">ссылка</a> </td>
                            </tr>
                            <tr v-if="itemModel.type == 'audio'">
                                <th> Задание: </th>
                                <td> <audio v-if="itemModel.audio_data" :src="'/audio/' + itemModel.audio_data" controls></audio> </td>
                            </tr>
                            <tr v-if="itemModel.type == 'video'">
                                <th> Задание: </th>
                                <td> <a :href="itemModel.video_data" target="_blank">ссылка</a> </td>
                            </tr>
                            <tr v-if="itemModel.type == 'event'">
                                <th> Время мероприятия: </th>
                                <td> @{{ itemModel.event_date }} </td>
                            </tr>

                        </table>

                            <div class="padding-10" style="border: 1px solid #dddddd;">

                                <h5 class="margin-b15">Вопросы
                                    <div class="pull-right">
                                        <button @click="syllabusShowedQuestionModal(null,itemModel.id)" :disabled="syllabusTaskProcessRequest" class="btn btn-primary" type="button" data-toggle="modal" data-target="#syllabusEditQuestionModal">Добавить вопрос</button>
                                    </div>
                                </h5>

                                <div class="clearfix">&nbsp;</div>

                                {{-- questions --}}
                                <div class="accordion margin-t15" id="accordionQuestions">
                                    <template v-for="(itemQuestion, indexQ) in itemModel.questions">
                                        <div class="card card-default">
                                            {{-- Header --}}
                                            <div :id="'heading' + itemQuestion.id" class="card-header">
                                                <h5 class="mb-0">
                                                    <button :data-target="'#collapseQ' + indexQ" class="btn btn-link" type="button" data-toggle="collapse" aria-expanded="true" :aria-controls="'collapseQ' + indexQ">
                                                        Вопрос @{{ indexQ + 1 }}
                                                    </button>
                                                    <div class="pull-right">

                                                        <button @click="syllabusShowedQuestionModal(itemQuestion,itemQuestion.task_id)" :disabled="syllabusTaskProcessRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="syllabusRemoveQuestion(itemQuestion)" :disabled="syllabusTaskProcessRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </div>
                                                </h5>
                                            </div>
                                            {{-- panel --}}
                                            <div :id="'collapseQ' + indexQ" class="collapse show" :aria-labelledby="'heading' + itemModel.id" data-parent="#accordionQuestions">
                                                <div class="card-body">

                                                    <table class="table">
                                                        <tr>
                                                            <th width="250"> Баллы: </th>
                                                            <td> @{{ itemQuestion.points }} </td>
                                                        </tr>

                                                        <tr>
                                                            <th> Задание: </th>
                                                            <td v-html="itemQuestion.question"></td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>
                                                                <button @click="syllabusShowedAnswersModal(itemQuestion.answer,itemQuestion.id,itemModel.isNotAccess)" :disabled="syllabusTaskProcessRequest" class="btn btn-primary" type="button" data-toggle="modal">Ответы</button>

                                                                &nbsp;&nbsp;&nbsp;   всего: @{{ itemQuestion.answer_count }} &nbsp;&nbsp; правильные: @{{ itemQuestion.answer_correct }} &nbsp;&nbsp; неправильные: @{{ itemQuestion.answer_uncorrect }}

                                                            </td>
                                                        </tr>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                            </div>

                    </div>
                </div>
            </div>
            </template>
        </div>


        {{-- add/edit task --}}
        <div :class="{'show': syllabusIsEditModal}" v-if="syllabusIsEditModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button @click="syllabusIsEditModal = false" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title">Добавить задание</h5>
                    </div>
                    <div class="modal-body">

                        <div v-if="syllabusTaskModalMessage" :class="{ 'alert-danger': syllabusTaskModalError, 'alert-success': !syllabusTaskModalError }" class="alert">
                            <div v-html="syllabusTaskModalMessage"> </div>
                        </div>

                        {{-- week --}}
                        <div class="form-group">
                            <label for="syllabusTaskModel_week"> Номер недели </label>
                            <select v-model="syllabusTaskModel.week" id="syllabusTaskModel_week" class="form-control">
                                <option v-for="num in 15" :value="num">
                                    @{{ num }}
                                </option>
                            </select>
                        </div>
                        {{-- type --}}
                        <div class="form-group">
                            <label for="syllabusTaskModel_type"> Тип задания </label>
                            <select v-model="syllabusTaskModel.type" id="syllabusTaskModel_type" class="form-control">
                                <option v-for="option in syllabusTaskTypeSelectList" :value="option.value">
                                    @{{ option.text }}
                                </option>
                            </select>
                        </div>
                        {{-- point --}}
                        <div class="form-group">
                            <label for="syllabusTaskModel_points"> Баллы </label>
                            <input v-model="syllabusTaskModel.points" id="syllabusTaskModel_points" class="form-control" type="number" min="1" max="20" />
                        </div>
                        {{-- text --}}
                        <template v-if="syllabusTaskModel.type == 'text' || syllabusTaskModel.type == 'essay'">
                        <div class="form-group">
                            <label for="syllabusTaskModel_text"> Задание </label>
                            <textarea
                                v-model="syllabusTaskModel.text_data"
                                id="syllabusTaskModel_text"
                                class="form-control"
                                rows="5"
                            ></textarea>
                        </div>
                        </template>
                        {{-- img --}}
                        <div v-if="syllabusTaskModel.type == 'img'" class="form-group">
                            <label for="syllabusTaskModel_img"> Задание </label>
                            <img v-if="syllabusTaskModel.img_data" :src="'/images/uploads/syllabustask/' + syllabusTaskModel.img_data" class="img-thumbnail margin-15" style="display:flex;max-height:300px;" />
                            <input @change="processTaskImgFile($event)" type="file" accept="image/jpeg" />
                        </div>
                        {{-- link --}}
                        <div v-if="syllabusTaskModel.type == 'link'" class="form-group">
                            <label for="syllabusTaskModel_link"> Ссылка </label>
                            <input v-model="syllabusTaskModel.link_data" id="syllabusTaskModel_link" class="form-control" type="text" />
                        </div>
                        {{-- audio --}}
                        <div v-if="syllabusTaskModel.type == 'audio'" class="form-group">
                            <label for="syllabusTaskModel_audio"> Задание </label>
                            <audio
                                    v-if="syllabusTaskModel.audio_data"
                                    style="width: 100%"
                                    :src="'/audio/' + syllabusTaskModel.audio_data"
                                    controls></audio>
                            <input @change="processTaskAudioFile($event)" type="file" accept="audio/*" />
                        </div>
                        {{-- video --}}
                        <div v-if="syllabusTaskModel.type == 'video'" class="form-group">
                            <label for="syllabusTaskModel_video"> Ссылка </label>
                            <input v-model="syllabusTaskModel.video_data" id="syllabusTaskModel_video" class="form-control" type="text" />
                        </div>
                        {{-- event date
                        <div v-if="syllabusTaskModel.type == 'event'" class="form-group">
                            <label for="syllabusTaskModel_eventdate"> Время события </label>
                            <input v-model="syllabusTaskModel.event_date" id="syllabusTaskModel_eventdate" class="form-control" type="date" />
                        </div>
                        --}}
                        {{-- event place
                        <div v-if="syllabusTaskModel.type == 'event'" class="form-group">
                            <label for="syllabusTaskModel_eventplace"> Место события </label>
                            <summernote
                                v-model="syllabusTaskModel.event_place"
                            ></summernote>
                        </div>
                        --}}

                    </div>
                    <div class="modal-footer">
                        <button @click="SyllabusEditTask" :disabled="syllabusTaskProcessRequest" type="button" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- add/edit question --}}
        <div :class="{'show': syllabusIsEditQuestionModal}" v-if="syllabusIsEditQuestionModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button @click="syllabusIsEditQuestionModal = false" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title">Добавить вопрос</h5>
                    </div>
                    <div class="modal-body">

                        <div v-if="syllabusTaskModalMessage" :class="{ 'alert-danger': syllabusTaskModalError, 'alert-success': !syllabusTaskModalError }" class="alert">
                            <div v-html="syllabusTaskModalMessage"> </div>
                        </div>

                        {{-- point --}}
                        <div class="form-group">
                            <label for="syllabusTaskQuestionModel_points"> Баллы </label>
                            <input v-model="syllabusTaskQuestionModel.points" id="syllabusTaskQuestionModel_points" class="form-control" type="number" min="1" max="20" />
                        </div>
                        {{-- question --}}
                        <div class="form-group">
                            <label for="syllabusTaskQuestionModel_question"> Вопрос </label>
                            <summernote
                                v-model="syllabusTaskQuestionModel.question"
                            ></summernote>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button @click="SyllabusEditQuestion" :disabled="syllabusTaskProcessRequest" type="button" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- show answers --}}
        <div :class="{'show': syllabusIsAnswerModal}" v-if="syllabusIsAnswerModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button @click="syllabusIsAnswerModal = false" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title"> Ответы </h5>
                    </div>
                    <div class="modal-body" style="overflow-y:scroll;height:700px;">

                        <button @click="syllabusShowedEditAnswerModal(null,syllabusTaskCurAnswerId)" :disabled="syllabusTaskProcessRequest" class="btn btn-primary btn-sm margin-t15 margin-b15" type="button"> Добавить ответ</button>

                        <template v-for="(itemAnswer, indexAns) in syllabusTaskAnswerModels">
                            <table class="table margin-t15 margin-b15">
                                <tr>
                                    <th width="250"> Ответ: </th>
                                    <th> Баллы: </th>
                                    <th> Правильный: </th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td v-html="itemAnswer.answer"></td>
                                    <td> @{{ itemAnswer.points }} </td>
                                    <td> @{{ (itemAnswer.correct == 1)?'Да':'Нет' }} </td>
                                    <td>
                                        <button @click="syllabusShowedEditAnswerModal(itemAnswer,itemAnswer.question_id)" :disabled="syllabusTaskProcessRequest" class="btn btn-success btn-sm" type="button"><i class="fa fa-edit"></i></button>
                                        <button @click="syllabusRemoveAnswer(itemAnswer)" :disabled="syllabusTaskProcessRequest" class="btn btn-danger btn-sm" type="button"><i class="fa fa-minus-circle"></i></button>
                                    </td>
                                </tr>
                            </table>
                        </template>

                    </div>

                </div>
            </div>
        </div>

        {{-- add/edit answer --}}
        <div :class="{'show': syllabusIsEditAnswerModal}" v-if="syllabusIsEditAnswerModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button @click="syllabusIsEditAnswerModal = false" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title"> Редактировать ответ </h5>
                    </div>
                    <div class="modal-body">

                        <div v-if="syllabusTaskModalMessage" :class="{ 'alert-danger': syllabusTaskModalError, 'alert-success': !syllabusTaskModalError }" class="alert">
                            <div v-html="syllabusTaskModalMessage"> </div>
                        </div>

                        {{-- point --}}
                        <div class="form-group">
                            <label for="syllabusTaskAnswerModel_points"> Баллы </label>
                            <input v-model="syllabusTaskAnswerModel.points" :disabled="!syllabusTaskAnswerModel.correct" id="syllabusTaskAnswerModel_points" class="form-control" type="number" min="1" max="20" />
                        </div>
                        {{-- correct --}}
                        <div class="form-group">
                            <label>
                                <input type="checkbox" v-model="syllabusTaskAnswerModel.correct" @change="SyllabusChangeAnswerCorrect(syllabusTaskAnswerModel)" /> Правильный
                            </label>
                        </div>
                        {{-- answer --}}
                        <div class="form-group">
                            <label for="syllabusTaskAnswerModel_answer"> Ответ </label>
                            <summernote
                                    v-model="syllabusTaskAnswerModel.answer"
                            ></summernote>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button @click="SyllabusEditAnswer" :disabled="syllabusTaskProcessRequest" type="button" class="btn btn-primary">Сохранить</button>
                    </div>

                </div>
            </div>
        </div>


    </div>


</div>

@section('scripts_js')
    <script type="text/javascript">

        $("*[data-name='picture']").remove();

        var task = new Vue({
            el: '#task_r2',
            data: {

                syllabusTaskModels: [],
                syllabusTaskModel: {},
                syllabusTaskQuestionModel: {},
                syllabusTaskAnswerModels: [],
                syllabusTaskAnswerModel: {},
                syllabusTaskMessage: '',
                syllabusTaskModalMessage: '',
                syllabusTaskError: false,
                syllabusTaskModalError: false,
                syllabusTaskProcessRequest: false,
                syllabusIsEditModal: false,
                syllabusIsEditQuestionModal: false,
                syllabusIsEditAnswerModal: false,
                syllabusIsAnswerModal: false,
                syllabusTaskCurAnswerId: false,
                syllabusTaskTypeSelectList: [
                    { text: 'Текст', value: '{{ \App\SyllabusTask::TYPE_TEXT }}' },
                    { text: 'Картинка', value: '{{ \App\SyllabusTask::TYPE_IMAGE }}' },
                    { text: 'Ссылка', value: '{{ \App\SyllabusTask::TYPE_LINK }}' },
                    { text: 'Аудио', value: '{{ \App\SyllabusTask::TYPE_AUDIO }}' },
                    { text: 'Видео', value: '{{ \App\SyllabusTask::TYPE_VIDEO }}' },
                    { text: 'Эссе', value: '{{ \App\SyllabusTask::TYPE_ESSAY }}' },
                    //{ text: 'Событие',  value: '{{ \App\SyllabusTask::TYPE_EVENT }}' }
                ],
                previewSyllabusTaskImgData: null,
                syllabusTaskTypeSelectListTranslate: [],
                syllabusTaskImgSource: '',
                syllabusTaskAudioSource: '',
                isNotAccess: false

            },
            methods: {

                syllabusTaskInitType: function(){

                    this.syllabusTaskTypeSelectListTranslate['{{ \App\SyllabusTask::TYPE_TEXT }}' ] = 'Текст';
                    this.syllabusTaskTypeSelectListTranslate['{{ \App\SyllabusTask::TYPE_IMAGE }}'] = 'Картинка';
                    this.syllabusTaskTypeSelectListTranslate['{{ \App\SyllabusTask::TYPE_LINK }}' ] = 'Ссылка';
                    this.syllabusTaskTypeSelectListTranslate['{{ \App\SyllabusTask::TYPE_AUDIO }}'] = 'Аудио';
                    this.syllabusTaskTypeSelectListTranslate['{{ \App\SyllabusTask::TYPE_VIDEO }}'] = 'Видео';
                    this.syllabusTaskTypeSelectListTranslate['{{ \App\SyllabusTask::TYPE_ESSAY }}'] = 'Эссе';
                    this.syllabusTaskTypeSelectListTranslate['{{ \App\SyllabusTask::TYPE_EVENT }}'] = 'Событие';
                },
                syllabusGetList: function(){

                    if( ('{{ $discipline->id }}' != '') && ('{{ $syllabus->language }}' != '')  ){
                        var self = this;
                        axios.post('{{ route('adminSyllabusTaskGetList') }}',{
                            "_token": "{{ csrf_token() }}",
                            "discipline_id": '{{ $discipline->id }}',
                            "language": '{{ $syllabus->language }}'
                        })
                            .then(function(response){

                                if( response.data.status ){

                                    self.syllabusTaskModels = response.data.models;

                                } else {

                                    self.syllabusTaskError = true;
                                    self.syllabusTaskMessage = '{{ __('Request error') }}';
                                }
                            });
                    }

                },
                syllabusShowedModal: function(model){

                    this.syllabusTaskModalMessage = '';
                    this.syllabusTaskModalError = false;
                    this.syllabusTaskModel = {};
                    if(model.syllabus_id){

                        this.syllabusTaskModel = model;
                        this.syllabusTaskModel.type = model.type;
                    } else {

                        this.SyllabusCreateTask();
                    }
                    this.syllabusIsEditModal = true;

                },
                syllabusShowedQuestionModal: function(modelQ,task_id){

                    this.syllabusTaskModalMessage = '';
                    this.syllabusTaskModalError = false;
                    this.syllabusTaskQuestionModel = {};

                    if(modelQ){

                        this.syllabusTaskQuestionModel = modelQ;
                        this.syllabusTaskModel.task_id = modelQ.task_id;
                    } else {

                        this.SyllabusCreateQuestion(task_id);
                    }
                    this.syllabusIsEditQuestionModal = true;

                },
                syllabusShowedAnswersModal: function(modelsAnswer,question_id,isNotAccess){

                    this.syllabusTaskModalMessage = '';
                    this.syllabusTaskModalError = false;
                    this.syllabusTaskAnswerModels = [];
                    this.syllabusTaskCurAnswerId = false;
                    this.syllabusTaskCurAnswerId = question_id;
                    this.isNotAccess = isNotAccess;


                    if( modelsAnswer ){

                        this.syllabusTaskAnswerModels = modelsAnswer;
                    }

                    this.syllabusIsAnswerModal = true;

                },
                syllabusShowedEditAnswerModal: function(modelAnswer,question_id){

                    this.syllabusTaskModalMessage = '';
                    this.syllabusTaskModalError = false;
                    this.syllabusTaskAnswerModel = {};
                    this.syllabusIsAnswerModal = false;
                    if(modelAnswer){

                        this.syllabusTaskAnswerModel = modelAnswer;
                        this.syllabusTaskAnswerModel.question_id = question_id;
                    } else {

                        this.SyllabusCreateAnswer(question_id);
                    }
                    this.syllabusIsEditAnswerModal = true;

                },
                SyllabusEditTask: function(){

                    this.syllabusTaskModalMessage = '';
                    this.syllabusTaskModalError = false;

                    // check error
                    if( !this.syllabusTaskModel.syllabus_id || ( this.syllabusTaskModel.syllabus_id == '' ) ){

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Нет привязки к силлабусу';
                        return;
                    }
                    if( !this.syllabusTaskModel.points || ( this.syllabusTaskModel.points == 0 ) || (this.syllabusTaskModel.points < 1) ) {

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Баллы должны быть положительным числом';
                        return;
                    }

                    this.syllabusTaskProcessRequest = true;
                    var self = this;
                    axios.post('{{ route('adminSyllabusEditTask') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": self.syllabusTaskModel.id,
                        "model": self.syllabusTaskModel,
                        "img_source": self.syllabusTaskImgSource,
                        "audio_source": self.syllabusTaskAudioSource
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.syllabusIsEditModal = false;

                        } else {

                            self.syllabusTaskModalError = true;
                            self.syllabusTaskModalMessage = response.data.message;
                        }

                        self.syllabusGetList();

                    })
                    .catch( error => {

                        self.syllabusTaskModalError = true,
                        self.syllabusTaskModalMessage = 'Request error',
                        console.log(error)
                    })
                    .finally(() => ( self.syllabusTaskProcessRequest = false ));

                },
                SyllabusEditQuestion: function(){


                    this.syllabusTaskModalMessage = '';
                    this.syllabusTaskModalError = false;

                    // check error
                    if( !this.syllabusTaskQuestionModel.task_id || ( this.syllabusTaskQuestionModel.task_id == '' ) ){

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Нет привязки к заданию';
                        return;
                    }
                    if( !this.syllabusTaskQuestionModel.points || ( this.syllabusTaskQuestionModel.points == 0 ) || (this.syllabusTaskQuestionModel.points < 1) ) {

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Баллы должны быть положительным числом';
                        return;
                    }
                    if( !this.syllabusTaskQuestionModel.question || ( this.syllabusTaskQuestionModel.question == '' ) ) {

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Вопрос обязателен для ввода';
                        return;
                    }

                    this.syllabusTaskProcessRequest = true;
                    var self = this;
                    axios.post('{{ route('adminSyllabusTaskEditQuestion') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": self.syllabusTaskQuestionModel.id,
                        "model": self.syllabusTaskQuestionModel
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.syllabusIsEditQuestionModal = false;

                            } else {

                                self.syllabusTaskModalError = true;
                                self.syllabusTaskModalMessage = response.data.message;
                            }

                            self.syllabusGetList();

                        })
                    .catch( error => {

                        self.syllabusTaskModalError = true,
                        self.syllabusTaskModalMessage = 'Request error',
                        console.log(error)
                    })
                    .finally(() => ( self.syllabusTaskProcessRequest = false ));

                },
                SyllabusEditAnswer: function(){

                    this.syllabusTaskModalMessage = '';
                    this.syllabusTaskModalError = false;

                    // check error
                    if( !this.syllabusTaskAnswerModel.question_id || ( this.syllabusTaskAnswerModel.question_id == '' ) ){

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Нет привязки к вопросу';
                        return;
                    }
                    if( this.syllabusTaskAnswerModel.points < 0 ) {

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Баллы должны быть положительным числом';
                        return;
                    }
                    if( !this.syllabusTaskAnswerModel.answer || ( this.syllabusTaskAnswerModel.answer == '' ) ) {

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Ответ обязателен для ввода';
                        return;
                    }
                    if( (this.syllabusTaskAnswerModel.points == 0) && this.syllabusTaskAnswerModel.correct ) {

                        this.syllabusTaskModalError = true;
                        this.syllabusTaskModalMessage = 'Ошибка! Правильный ответ не может быть с 0 баллами';
                        return;
                    }

                    this.syllabusTaskProcessRequest = true;
                    var self = this;
                    axios.post('{{ route('adminSyllabusTaskEditAnswer') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": self.syllabusTaskAnswerModel.id,
                        "model": self.syllabusTaskAnswerModel
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.syllabusIsEditAnswerModal = false;
                                self.syllabusGetList();

                            } else {

                                self.syllabusTaskModalError = true;
                                self.syllabusTaskModalMessage = response.data.message;
                            }

                        })
                        .catch( error => {

                            self.syllabusTaskModalError = true,
                            self.syllabusTaskModalMessage = 'Request error',
                            console.log(error)
                        })
                        .finally(() => ( self.syllabusTaskProcessRequest = false ));


                },
                SyllabusRemoveTask: function(modelT){

                    this.syllabusTaskMessage = '';
                    this.syllabusTaskError = false;
                    this.syllabusTaskProcessRequest = true;

                    var self = this;
                    axios.post('{{ route('adminSyllabusTaskDelete') }}',{
                        "_token": "{{ csrf_token() }}",
                        "task_id": modelT.id
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.syllabusGetList();

                            } else {

                                self.syllabusTaskError = true;
                                self.syllabusTaskMessage = response.data.message;
                            }

                        })
                        .catch( error => {

                            self.syllabusTaskError = true,
                            self.syllabusTaskMessage = 'Request error',
                            console.log(error)
                        })
                        .finally(() => ( self.syllabusTaskProcessRequest = false ));

                },
                syllabusRemoveQuestion: function(modelQ){

                    this.syllabusTaskMessage = '';
                    this.syllabusTaskError = false;
                    this.syllabusTaskProcessRequest = true;

                    var self = this;
                    axios.post('{{ route('adminSyllabusTaskRemoveQuestion') }}',{
                        "_token": "{{ csrf_token() }}",
                        "question_id": modelQ.id
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.syllabusGetList();

                            } else {

                                self.syllabusTaskError = true;
                                self.syllabusTaskMessage = response.data.message;
                            }

                        })
                        .catch( error => {

                            self.syllabusTaskError = true,
                            self.syllabusTaskMessage = 'Request error',
                            console.log(error)
                        })
                        .finally(() => ( self.syllabusTaskProcessRequest = false ));

                },
                syllabusRemoveAnswer: function(modelAnswer){

                    this.syllabusTaskModalMessage = '';
                    this.syllabusTaskModalError = false;
                    this.syllabusTaskProcessRequest = true;

                    var self = this;
                    axios.post('{{ route('adminSyllabusTaskRemoveAnswer') }}',{
                        "_token": "{{ csrf_token() }}",
                        "answer_id": modelAnswer.id
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.syllabusGetList();
                                self.syllabusIsAnswerModal      = false;
                                self.syllabusIsEditAnswerModal  = false;

                            } else {

                                self.syllabusTaskModalError = true;
                                self.syllabusTaskModalMessage = response.data.message;
                            }

                        })
                        .catch( error => {

                            self.syllabusTaskModalError = true,
                            self.syllabusTaskModalMessage = 'Request error',
                            console.log(error)
                        })
                        .finally(() => ( self.syllabusTaskProcessRequest = false ));

                },
                processTaskImgFile: function(event){
                    var file = event.target.files[0];
                    if(file) {
                        var self = this;
                        self.syllabusTaskModel.img_data = file.name;
                        self.syllabusTaskImgSource = '';

                        var reader = new FileReader();
                        reader.readAsBinaryString(file);
                        reader.onload = function (evt) {
                            self.syllabusTaskImgSource = btoa(evt.target.result);
                        }
                    }
                },
                processTaskAudioFile: function(event){
                    var file = event.target.files[0];
                    if(file) {
                        var self = this;
                        self.syllabusTaskModel.audio_data = file.name;
                        self.syllabusTaskAudioSource = '';

                        var reader = new FileReader();
                        reader.readAsBinaryString(file);
                        reader.onload = function (evt) {
                            self.syllabusTaskAudioSource = btoa(evt.target.result);
                        }
                    }
                },
                uploadSyllabusTaskImgData(e){
                    const image = e.target.files[0];
                    const reader = new FileReader();
                    reader.readAsDataURL(image);
                    reader.onload = e =>{
                        this.previewSyllabusTaskImgData = e.target.result;
                    }
                },
                uploadSyllabusTaskAudioData(e){
                    const audio = e.target.files[0];
                    const reader = new FileReader();
                    reader.readAsDataURL(audio);
                    reader.onload = e =>{
                        this.previewSyllabusTaskImgData = e.target.result;
                    }
                },
                SyllabusCreateTask: function(){

                    this.syllabusTaskModel = {
                        id: 0,
                        syllabus_id: '{{ $syllabus->id }}',
                        discipline_id: '{{ $discipline->id }}',
                        language: '{{ $syllabus->language }}',
                        type: '{{ \App\SyllabusTask::TYPE_TEXT }}',
                        points: 0
                    };
                },
                SyllabusCreateQuestion: function(task_id){

                    this.syllabusTaskQuestionModel = {
                        id: 0,
                        task_id: task_id,
                        points: 0,
                        question: ''
                    };
                },
                SyllabusCreateAnswer: function(question_id){

                    this.syllabusTaskAnswerModel = {
                        id: 0,
                        question_id: question_id,
                        answer: '',
                        points: 0,
                        correct: 0
                    };
                },
                SyllabusChangeAnswerCorrect: function(model){

                    if(!model.correct){
                        this.syllabusTaskAnswerModel.points = 0;
                    }
                }

            },
            created: function(){

                this.syllabusTaskInitType();
                this.syllabusGetList();
            }
        });

    </script>
@endsection

