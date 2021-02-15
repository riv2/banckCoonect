@extends("admin.admin_app")

@section('title', 'Темы дисциплины "'. $oDiscipline->name .'"')

@section("content")
<div id="main">
    <div id="main-app">
        <div class="page-header">

            <p class="sillabus-title"> Дисциплина: {{ $oDiscipline->name }}, Кредиты: {{ $oDiscipline->ects }} </p>
            <div class="clearfix"></div>

            @if(\App\Services\Auth::user()->hasRight('themes','create'))
                <div class="pull-right">
                    <!-- Button trigger modal -->
                    <button class="btn btn-primary " v-on:click="createRequirements">
                        Загрузка документов
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="candidateRequirementsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Загрузка Документов</h4>
                                    <div class="has-success" v-if="success_message != null">
                                        <div class="alert bg-success padding-10 border-r d-flex">
                                            <p v-html="success_message">
                                            </p>
                                        </div>
                                    </div>
                                     <div class="has-error" v-if="error_message != null">
                                        <div class="alert bg-danger padding-10">
                                            <p class="help-block"v-html="error_message">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <ul class="nav nav-tabs">
                                        <li role="presentation" class="tab-ru" v-bind:class="{'active': language=='ru'}" v-on:click="language='ru'"><a href="#">Русский</a></li>
                                        <li role="presentation" class="tab-kz" v-bind:class="{'active': language=='kz'}" v-on:click="language='kz'"><a href="#">Казахский</a></li>
                                        <li role="presentation" class="tab-en" v-bind:class="{'active': language=='en'}" v-on:click="language='en'"><a href="#">Английский</a></li>
                                    </ul>
                                    <div class="panel panel-default panel-shadow" style="border-radius: 0px 5px 5px 5px;">
                                        <div class="panel-body">
                                            <div class="panel-group" id="accordion">
                                                @foreach(['ru', 'kz', 'en'] as $lang)
                                                    <div v-show="language == '{{$lang}}'" style="display:none;">
                                                        <document-list :documents="disciplineDocuments.{{$lang}}" :remove="deleteDocument" lang="{{$lang}}"/>
                                                    </div>
                                                    <div v-show="language == '{{$lang}}'" style="display:none;">
                                                         <description-form lang="{{$lang}}"/>
                                                    </div>     
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="addDocument"> 
                                    <div class="margin-15">
                                        <label>Документ</label>
                                        <input type="file" name="document" id="document_input" class="form-control">
                                    </div>
                                    <div class="margin-15">
                                        <label>Описание</label>
                                        <textarea name="description" cols="30" rows="5" class="form-control" v-model="form_description"></textarea>
                                    </div>

                                    <input type="hidden" name="language" v-bind:value="language">

                                    <div class="modal-footer">
                                        <button class="btn btn-primary" v-on:click="addNewDocument">Загрузить</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <a v-bind:href="'{{ route('admin.syllabus.export.doc', ['disciplineId' => $disciplineId]) }}?lang=' + language" class="btn btn-primary">Выгрузить силлабус</a>
                    <a v-bind:href="'{{ route('adminSyllabusExportPdf', ['disciplineId' => $disciplineId]) }}?lang=' + language" class="btn btn-primary">Выгрузить в PDF</a>
                    <a v-bind:href="'{{ route('adminSyllabusExportPdf', ['disciplineId' => $disciplineId]) }}?mode=question_only&lang=' + language" class="btn btn-primary">Выгрузить
                        в PDF без ответов</a>
                    <button class="btn btn-primary" @click="showSyllabusModuleList">
                        Список модулей
                    </button>
                    <a v-bind:href="'{{ route('admin.syllabus.module.create', ['disciplineId' => $disciplineId]) }}?language=' + language" class="btn btn-primary">Добавить модуль<i class="fa fa-plus"></i></a>
                </div>

            <h2>Темы</h2>
        </div>

        @if(Session::has('flash_message'))
            <div class="alert alert-warning">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <ul class="nav nav-tabs">
            <li role="presentation" class="tab-ru" v-bind:class="{'active': language=='ru'}" v-on:click="language='ru'"><a href="#">Русский</a></li>
            <li role="presentation" class="tab-kz" v-bind:class="{'active': language=='kz'}" v-on:click="language='kz'"><a href="#">Казахский</a></li>
            <li role="presentation" class="tab-en" v-bind:class="{'active': language=='en'}" v-on:click="language='en'"><a href="#">Английский</a></li>
        </ul>
        <div class="panel panel-default panel-shadow" style="border-radius: 0px 5px 5px 5px;">
            <div class="panel-body">
                @foreach(['ru', 'kz', 'en'] as $lang)
                    <div v-show="language == '{{$lang}}'" style="display:none;">
                        <table id="data-table-{{$lang}}" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td>ID Модуля</td>
                                    <td>Имя Модуля</td>
                                    <td></td>
                                    <th>Id</th>
                                    <th>Номер</th>
                                    <th>Наименование</th>
                                    <th>Лекц.</th>
                                    <th>Пр/сем</th>
                                    <th>Лаб.</th>
                                    <th>СРОП</th>
                                    <th>СРО</th>
                                    <th>Для Тестирования</th>
                                    <th>Кол-во вопросов</th>
                                    <th class="text-center width-100">Действие</th>
                                </tr>
                            </thead>

                            <tbody>

                            @php
                                $hoursThemeCount = [
                                    'contact'           => 0,
                                    'self'              => 0,
                                    'self_with_teacher' => 0,
                                    'srop'              => 0,
                                    'sro'               => 0,
                                    'questions'         => 0,
                                ];
                            @endphp

                            @foreach($syllabusList as $syllabus)
                                @if($syllabus->language == $lang)
                                    @php
                                        $hoursThemeCount['contact'] += $syllabus->contact_hours;
                                        $hoursThemeCount['self'] += $syllabus->self_hours;
                                        $hoursThemeCount['self_with_teacher'] += $syllabus->self_with_teacher_hours;
                                        $hoursThemeCount['srop'] += $syllabus->srop_hours;
                                        $hoursThemeCount['sro'] += $syllabus->sro_hours;
                                        $hoursThemeCount['questions'] += $syllabus->quizeQuestions->count();
                                    @endphp

                                    <tr>
                                        <td>{{ $syllabus->module_id }}</td>
                                        <td>{{ $syllabus->module->name ?? '' }}</td>
                                        <td>
                                            <input class="syllabus-{{ $lang }}-id" type="checkbox" value="{{ $syllabus->id }}" v-model="selectList"/>
                                        </td>
                                        <td>{{ $syllabus->id }}</td>
                                        <td>{{ $syllabus->theme_number }}</td>
                                        <td>{{ $syllabus->theme_name }}</td>

                                        <td @dblclick="editHours('contact_hours',{{ $syllabus->id }})" class="text-center edit-hours">
                                            {{ $syllabus->contact_hours }}
                                        </td>

                                        <td @dblclick="editHours('self_hours',{{ $syllabus->id }})" class="text-center edit-hours" data-key="self_hours" data-id="{{ $syllabus->id }}">
                                            {{ $syllabus->self_hours }}
                                        </td>

                                        <td @dblclick="editHours('self_with_teacher_hours',{{ $syllabus->id }})" class="text-center edit-hours" data-key="self_with_teacher_hours" data-id="{{ $syllabus->id }}">
                                            {{ $syllabus->self_with_teacher_hours }}
                                        </td>

                                        <td @dblclick="editHours('srop_hours',{{ $syllabus->id }})" class="text-center edit-hours" data-key="srop_hours" data-id="{{ $syllabus->id }}">
                                            {{ $syllabus->srop_hours }}
                                        </td>

                                        <td @dblclick="editHours('sro_hours',{{ $syllabus->id }})" class="text-center edit-hours" data-key="sro_hours" data-id="{{ $syllabus->id }}">
                                            {{ $syllabus->sro_hours }}
                                        </td>

                                        <td><input type="checkbox" class="for_test1" id="test1_{{$syllabus->id}}" onchange="test1({{$syllabus->id}}, this)" @if($syllabus->for_test1) checked @endif></td>

                                        <td class="text-center">
                                            {{ $syllabus->quizeQuestions->count() }}
                                        </td>

                                        <td class="text-center" style="width: 200px">

                                            <div class="btn-group">
                                                @if(\App\Services\Auth::user()->hasRight('themes','edit'))
                                                    <a class="btn btn-default"
                                                       href="{{ route('adminSyllabusEdit', ['disciplineId' => $syllabus->discipline_id, 'themeId' => $syllabus->id]) }}"><i
                                                                class="md md-edit"></i></a>
                                                    <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i
                                                                class="md md-more"></i><span class="caret"></span></button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        @if($lang == 'ru')
                                                            <li><a v-on:click="themeCopy({{ $syllabus->id }}, 'kz')"><i class="md md-content-copy"></i>Копия в казахский</a>
                                                            </li>
                                                            <li><a v-on:click="themeCopy({{ $syllabus->id }}, 'en')"><i class="md md-content-copy"></i>Копия в английский</a>
                                                            </li>
                                                        @endif
                                                        @if($lang == 'kz')
                                                            <li><a v-on:click="themeCopy({{ $syllabus->id }}, 'ru')"><i class="md md-content-copy"></i>Копия в русский</a></li>
                                                            <li><a v-on:click="themeCopy({{ $syllabus->id }}, 'en')"><i class="md md-content-copy"></i>Копия в английский</a>
                                                            </li>
                                                        @endif
                                                        @if($lang == 'en')
                                                            <li><a v-on:click="themeCopy({{ $syllabus->id }}, 'ru')"><i class="md md-content-copy"></i>Копия в русский</a></li>
                                                            <li><a v-on:click="themeCopy({{ $syllabus->id }}, 'kz')"><i class="md md-content-copy"></i>Копия в казахский</a>
                                                            </li>
                                                        @endif
                                                        @if(\App\Services\Auth::user()->hasRight('themes','delete'))
                                                            <li>
                                                                <a href="{{ route('adminSyllabusDelete', ['disciplineId' => $syllabus->discipline_id, 'themeId' => $syllabus->id]) }}"><i
                                                                            class="md md-delete"></i> Удалить</a></li>
                                                        @endif
                                                    </ul>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                @endif

                            @endforeach

                            </tbody>

                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">Итого:</td>

                                    <td class="text-center">
                                        {{ $hoursThemeCount['contact'] }}
                                    </td>

                                    <td class="text-center">
                                        {{ $hoursThemeCount['self'] }}
                                    </td>

                                    <td class="text-center">
                                        {{ $hoursThemeCount['self_with_teacher'] }}
                                    </td>

                                    <td class="text-center">
                                        {{ $hoursThemeCount['srop'] }}
                                    </td>

                                    <td class="text-center">
                                        {{ $hoursThemeCount['sro'] }}
                                    </td>
                                    <td></td>

                                    <td class="text-center">
                                        {{ $hoursThemeCount['questions'] }}
                                    </td>

                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="col-sm-12" v-show="selectList.length > 0" style="margin-bottom: 20px">
                            <div class="col-sm-3 no-padding">
                                <select class="form-control" v-model="multiCopyTo" v-if="!copyProcess">
                                    <option v-bind:value="null" disabled selected>Копировать выбранное в...</option>
                                    @if($lang == 'ru')
                                        <option v-bind:value="'kz'">Казахский</option>
                                        <option v-bind:value="'en'">Английский</option>
                                    @endif
                                    @if($lang == 'kz')
                                        <option v-bind:value="'ru'">Русский</option>
                                        <option v-bind:value="'en'">Английский</option>
                                    @endif
                                    @if($lang == 'en')
                                        <option v-bind:value="'kz'">Казахский</option>
                                        <option v-bind:value="'ru'">Русский</option>
                                    @endif
                                </select>
                                <span v-if="copyProcess">Пожалуйста, подождите... Идет копирование тем.</span>
                            </div>

                            <div class="col-sm-3">
                                <select class="form-control" v-model="moveToModule" v-if="!copyProcess">
                                    <option v-bind:value="null" disabled selected>Переместить выбранное в...</option>
                                    <option value="">Без модуля</option>
                                    @foreach($syllabusModules as $syllabusModule)
                                        @if($syllabusModule->language == $lang)
                                            <option :value="{{ $syllabusModule->id }}">{{ $syllabusModule->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- SyllabusModuleList Modal -->
        <div class="modal fade" id="syllabusModuleList" tabindex="-1" role="dialog" aria-labelledby="syllabusModuleListLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="syllabusModuleListLabel">Список модулей</h4>
                    </div>

                    <div class="modal-body">
                        <ul class="nav nav-tabs">
                            <li role="presentation" v-bind:class="{'active': language=='ru'}" v-on:click="language='ru'">
                                <a href="#">Русский</a>
                            </li>

                            <li role="presentation" v-bind:class="{'active': language=='kz'}" v-on:click="language='kz'">
                                <a href="#">Казахский</a>
                            </li>

                            <li role="presentation" v-bind:class="{'active': language=='en'}" v-on:click="language='en'">
                                <a href="#">Английский</a>
                            </li>
                        </ul>

                        <div class="panel panel-default panel-shadow" style="border-radius: 0px 5px 5px 5px;">
                            <div class="panel-body">
                                @foreach(['ru', 'kz', 'en'] as $lang)
                                    <ul v-show="language == '{{$lang}}'" style="display:none;">
                                        @foreach($syllabusModules as $module)
                                            @if($module->language == $lang)
                                                <li class="list-group-item">
                                                    {{ $module->name }}

                                                    <div class="pull-right">
                                                        <div class="btn-group btn-group-xs" role="group">
                                                            <a class="btn btn-default" href="{{ route('admin.syllabus.module.edit', [
                                                                                            'disciplineId' => $disciplineId,
                                                                                            'module_id' => $module['id'],
                                                                                        ]) }}?language={{ $lang }}">
                                                                <i class="md md-edit"></i>
                                                            </a>
                                                            <a class="btn btn-default" href="{{ route('admin.syllabus.module.delete', [
                                                                                            'disciplineId' => $disciplineId,
                                                                                            'module_id' => $module['id'],
                                                                                        ]) }}">
                                                                <i class="md md-delete"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
 </div>
    
    
<!-- вопросы -->     
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="main-quiz-list">
                    
                    <ul class="nav nav-tabs">
                    <li role="presentation" class="tab-ru" v-bind:class="{'active': language=='ru'}" v-on:click="language='ru'"><a href="#">Русский</a></li>
                    <li role="presentation" class="tab-kz" v-bind:class="{'active': language=='kz'}" v-on:click="language='kz'"><a href="#">Казахский</a></li>
                    <li role="presentation" class="tab-en" v-bind:class="{'active': language=='en'}" v-on:click="language='en'"><a href="#">Английский</a></li>
                </ul> 
                    
                    
                    <div class="form-group">
                        <label for="self_with_teacher_hours" class="col-md-3 control-label">Вопросы</label>
                        <div class="col-md-9">

                            <table id="question-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <tbody> 
                                    
                                    @foreach(['ru', 'kz', 'en'] as $lang)
                                   
                                    <tr v-for="quiz in quizList" v-if="quiz.lang === '{{$lang}}'" v-show="language == '{{$lang}}'" style="display:none;">

                                        <td v-html="quiz.question"></td>
                                         <td v-html="quiz.lang"></td>
                                         

                                        <td class="text-right">

                                            <div class="btn-group">
                                                <a class="btn btn-default" v-on:click="showEditQuize(quiz.id)"><i class="md md-edit"></i></a>
                                                <a class="btn btn-default" v-on:click="deleteQuize(quiz.id)"><i class="fa fa-remove"></i></a>
                                            </div>
                                        </td>
                                    </tr>  
                                   
                                 @endforeach
                                    
                                </tbody>
                            </table>
                                
                            <a id="addQuizBtn" v-on:click="createQuestion()" class="hide btn btn-default">Добавить вопрос <i class="fa fa-plus"></i></a>
                            <span id="addQuizNeedThemeMsg" >Требуется созданная тема.</span>
                        
                        </div>
                    </div>

                    <div class="modal modal-question" v-bind:class="{'show': quizEdit}" tabindex="-1" role="dialog" aria-labelledby="" v-if="quizEdit">
                        <div class="modal-dialog modal-lg" style="min-width:950px;" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" v-on:click="quizEdit = null"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Редактирование вопроса</h4>
                                </div>
                                <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;">
                                    @include('admin.pages.syllabus.quiz.form')
                                </div>
                                <div class="modal-footer">
                                    <button v-bind:disabled="processed" class="btn btn-primary" v-on:click="saveQuestion()">Сохранить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
 </div>
<!-- вопросы -->    

   
@endsection

@section('scripts')
    <style>
        .dataTables_empty {
            text-align: center;
        }
    </style>

    <script type="text/javascript">
        var documentsList = Vue.component('document-list', {
            props:[
                'documents',
                'remove',
                'lang'
            ],
            template: `
                <div>
                   <div class="panel panel-default" v-for="doc in documents" :key="doc.id">
                        <div class="padding-15">
                            <div style="display: flex; justify-content: space-between">
                                <a v-bind:href="doc.url" target="_blank" v-html="doc.original_name"></a>
                                <div>
                                    <button v-on:click="remove(doc.id, lang)"
                                        class="btn btn-default-dark">
                                        <i class="md md-delete"></i>
                                    </button>
                                    <a data-toggle="collapse" data-parent="#accordion" class="btn btn-default-dark" v-bind:href="'#'+doc.id">Описание</a>
                                </div>
                            </div>
                        </div>
                        <div v-bind:id="doc.id" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p v-html="doc.description"></p>
                            </div>
                        </div>
                    </div>
                </div>`
        })

      var descriptionForm = Vue.component('description-form',{
            props:[
              'lang',
              'edit'
            ],
            data: function(){
                return {
                    description: {
                        en: "{{str_replace("\n", ' ', $oDiscipline->files_description_en)}}",
                        ru: "{{str_replace("\n", ' ', $oDiscipline->files_description_ru)}}",
                        kz: "{{str_replace("\n", ' ', $oDiscipline->files_description_kz)}}"
                    }
                }
            },
            methods: {
                 editDescription: function(lang){
                    axios.post('{{route('adminSyllabusEditDescription', ['disciplineId' => $disciplineId,'lang' => ''])}}/' + lang, {
                        'description': this.description[lang]
                    }).then(res => {
                            app.checkMessages(res.data)
                        })
                        .catch(err => {
                            app.checkMessages(err.response.data)
                        })
                },
            },
            template: ` <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" v-bind:href="'#'+lang">Описание</a>
                                </h4>
                            </div>
                            <div v-bind:id="lang" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div>
                                        <textarea name="description" class="form-control border-0" rows="10" v-model="description[lang]"></textarea>
                                    </div>
                                    <button class="btn btn-primary margin-t5" v-on:click="editDescription(lang)">Изменить</button> 
                                </div>
                            </div>
                        </div>`
        })

        var app = new Vue({
            el: '#main-app',
            data: {
                language: '{{ $defaultLang }}',
                selectList: [],
                multiCopyTo: null,
                copyProcess: false,
                dataCellLast: '',
                moveToModule: null,
                moveModuleProcess: false,
                showModal: false,
                disciplineDocuments: {
                    en: [],
                    ru: [],
                    kz: []
                },
                success_message: null,
                error_message: null,
                form_description: '',
                ratingLimits: [],
                lastRatingDayEdit: 0,
                currentStudyGroup: 0
            },
            components: {
                'document-list': documentsList,
                'description-form': descriptionForm
            },
            methods: {
                getDisciplineDocuments: function(){
                    axios.get('{{route('adminGetDocumentsList', ['disciplineId' => $disciplineId])}}')
                        .then(res => {
                            this.disciplineDocuments = res.data
                            this.checkMessages(res.data)
                        })
                },
                deleteDocument: function(id, lang){
                    axios.post('{{route('adminSyllabusDeleteDocument', ['disciplineId' => $disciplineId, 'document_id' => ''])}}/' + id)
                        .then(res => {
                            this.disciplineDocuments[lang] = this.disciplineDocuments[lang].filter(e => e.id != id)
                            this.checkMessages(res.data)
                        })
                },
                addNewDocument: function(){
                    let file = document.getElementById('document_input')
        
                    const form = new FormData()
                    const lang = this.language
                    form.append('document', file.files[0]);
                    form.append('description', this.form_description);
                    form.append('language', this.language);

                    this.form_description = ''
                    file.value = ''

                    axios.post('{{route('adminSyllabusAddDocument', ['disciplineId' => $disciplineId])}}', form)
                        .then(res => {
                            this.disciplineDocuments[lang].push(res.data)
                            this.checkMessages(res.data)
                        }).catch(err => {
                            this.checkMessages(err.response.data)
                        })
                },
                checkMessages: function(data){
                    this.error_message = null
                    this.error_success = null

                    if(typeof data.error !== "undefined"){
                        this.error_message =  data.error
                    }
                    if(typeof data.success !== "undefined"){
                        this.success_message =  data.success
                    }
                },
                themeCopy: function (id, lang) {
                    var idList = (id != 'all') ? [id] : this.selectList;
                    this.copyProcess = true;
                    axios.post('{{route('adminSyllabusCopyTheme', ['disciplineId' => $disciplineId])}}', {
                        idList: idList,
                        lang: lang
                    })
                    .then(function (response) {
                        location.href = '{{route('adminSyllabusList', ['disciplineId' => $disciplineId])}}?defaultLang=' + lang;
                    });
                },
                createRequirements: function(event){
                    $('#candidateRequirementsModal').modal('show');
                },
                editHours: function (field_key, item_id) {
                    if ($('div.edit-hours').parent('td').length > 0) {
                        $('div.edit-hours').parent('td').get(0).innerHTML = this.dataCellLast;
                        $('div.edit-hours').remove();
                    }

                    this.dataCellLast = event.target.innerHTML;

                    event.target.innerHTML =
                        '<div class="input-group input-group-sm edit-hours w-100">' +
                        '<input type="text" class="form-control new-hours" value="' + this.dataCellLast.trim() + '">' +
                        '<span class="input-group-addon update-hours" data-key="' + field_key + '" data-id="' + item_id + '">' +
                        '<i class="md md-save"></i>' +
                        '</span>' +
                        '</div>';
                },
                createRequirements: function(event){
                    $('#candidateRequirementsModal').modal('show');
                },
                themeMoveToModule: function (module_id) {
                    this.copyProcess = true;

                    axios.post('{{route('admin.syllabus.theme.move.module', ['disciplineId' => $disciplineId])}}', {
                        theme_list: this.selectList,
                        module_id: module_id
                    })
                    .then(function () {
                        location.href = '{{route('adminSyllabusList', ['disciplineId' => $disciplineId])}}?defaultLang=' + app.language;
                    });
                },
                showSyllabusModuleList: function () {
                    $('#syllabusModuleList').modal('show');
                },
                ratingDayChange: function(dayIndex) {
                    this.lastRatingDayEdit = dayIndex;

                    if(this.ratingSum > 20) {
                        this.ratingLimits[dayIndex] = '0';
                        app.$forceUpdate();
                        $('#day-rating-' + dayIndex).val('0');
                    }
                },
                saveRatingLimit: function() {
                    axios.post('{{route('adminSyllabusRatingLimitUpdate', ['disciplineId' => $disciplineId])}}', {
                        rating_list: this.ratingLimits,
                        group_id: this.currentStudyGroup
                    })
                        .then(function (response) {
                            alert('Изменения сохранены');
                        });
                }
            },
            computed: {
                ratingSum: function(){

                    var result = 0;

                    for (var i = 1; i <= 75; i++)
                    {
                        if(this.ratingLimits[i] != undefined && this.ratingLimits[i] != NaN)
                            result += parseInt(this.ratingLimits[i]);
                    }

                    return result;
                }
            },
            mounted: function(){
                this.getDisciplineDocuments()
            },
            watch: {
                ratingLimits: function () {
                    console.log(this.ratingSum);
                },
                language: function () {
                    this.selectList = [];
                    this.multiCopyTo = null;
                },
                multiCopyTo: function () {
                    if (this.multiCopyTo && this.selectList.length > 0) {
                        if (!confirm('Копировать выбранные темы?')) {
                            this.multiCopyTo = null;
                            return;
                        }

                        this.themeCopy('all', this.multiCopyTo);
                    }
                },
                moveToModule: function () {
                    if (this.moveToModule != null && this.selectList.length > 0) {
                        if (!confirm('Переместить выбранные темы?')) {
                            this.moveToModule = null;
                            return;
                        }

                        this.themeMoveToModule(this.moveToModule);
                    }
                },
                currentStudyGroup: function() {

                    var self = this;

                    axios.post('{{route('adminSyllabusLoadRatingLimitsByGroup', ['disciplineId' => $disciplineId])}}', {
                        group_id: this.currentStudyGroup
                    })
                        .then(function (response) {
                            self.ratingLimits = response.data;
                        });
                }
            },
            created: function(){
                @foreach($oDiscipline->studentDisciplineDayLimits as $dayLimit)
                    this.ratingLimits[{{ $dayLimit->day_num }}] = {{ $dayLimit->rating_limit }};
                @endforeach
            }
        });

        @foreach(['ru', 'kz', 'en'] as $lang)
            $('#data-table-{{ $lang }}').dataTable({
                order: [[ 0, "asc" ]],
                dom: "<'row'<'col-sm-4'l><'col-sm-4 head-buttons-{{ $lang }}'><'col-sm-4'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                language: {
                    emptyTable:   '<div class="btn-group">' +
                                        '<a class="btn btn-default" href="{{ route('adminSyllabusEdit', [
                                                                    'disciplineId' => $disciplineId,
                                                                    'themeId' => 'add'
                                                                ]) }}/{{ $lang }}?module_id=">' +
                                            'Добавить тему' +
                                        '</a>' +
                                    '</div>'
                },
                rowGroup: {
                    dataSrc: function(row) {
                        return row.module_name == '' ? 'Без модуля' : row.module_name + ',' + row.module_id;
                    },
                    startRender: function ( rows, module ) {
                        var module = module.split(',');
                        var editButton = '';
                        var deleteButton = '';

                        if (module[1] != undefined) {
                            editButton +=   '<a class="btn btn-default" href="{{ route('admin.syllabus.module.edit', [
                                                                            'disciplineId' => $disciplineId,
                                                                            'module_id' => '',
                                                                        ]) }}/' + module[1] + '?language={{ $lang }}">' +
                                                '<i class="md md-edit"></i>' +
                                            '</a>';

                            deleteButton =  '<a class="btn btn-default" href="{{ route('admin.syllabus.module.delete', [
                                                                            'disciplineId' => $disciplineId,
                                                                            'module_id' => '',
                                                                        ]) }}/' + module[1] + '">' +
                                                '<i class="md md-remove"></i>' +
                                            '</a>';
                        } else {
                            module[1] = '';
                        }

                        var buttons =  '<div class="btn-group">' +
                                            '<a class="btn btn-default" href="{{ route('adminSyllabusEdit', [
                                                'disciplineId' => $disciplineId,
                                                'themeId' => 'add'
                                            ]) }}/{{ $lang }}?module_id=' + module[1] + '">' +
                                                '<i class="md md-add"></i>' +
                                            '</a>' +
                                            editButton +
                                            deleteButton +
                                        '</div>';

                        return $('<tr/>')
                            .append('<td class="text-center" colspan="11">'+ module[0] +'</td>')
                            .append(
                                '<td class="text-center">' +
                                    buttons +
                                '</td>'
                            );
                    },
                },
                columns: [
                    {
                        data: 'module_id',
                        orderable: false,
                        className: 'hide',
                    },
                    {
                        data: 'module_name',
                        orderable: false,
                        className: 'hide',
                    },
                    {
                        orderable: false,
                    },
                    {
                        orderable: true,
                    },
                    {
                        orderable: true,
                    },
                    {
                        orderable: true,
                    },
                    {
                        orderable: false,
                    },
                    {
                        orderable: false,
                    },
                    {
                        orderable: false,
                    },
                    {
                        orderable: false,
                    },
                    {
                        orderable: false,
                    },
                    {
                        orderable: false,
                    },
                    {
                        orderable: false,
                    },
                    {
                        orderable: false,
                    },
                ]
            });

            $('.head-buttons-{{ $lang }}').html(
                '<button type="button" class="btn btn-default select-all" data-lang="{{ $lang }}">Выделить все</button>'
            );
        @endforeach

        $('body').on('click', '.select-all', function () {
            var lang = $(this).data('lang');

            $('.syllabus-' + lang + '-id').each(function (i, element) {
                app.selectList.push(element.value);
            });
        });

        $('table[id^=data-table]').on('click', 'span.update-hours', function () {
            let key = $(this).attr('data-key');
            let value = $('.new-hours').val();
            let theme_id = $(this).attr('data-id');

            axios.post('{{ route('disciplines.themes.hours.edit', ['disciplineId' => $disciplineId]) }}', {
                key: key,
                value: value,
                theme_id: theme_id
            })
            .then(function () {
                $('div.edit-hours').parent('td').get(0).innerHTML = value;
            });
        });

        function test1 (id, checkbox) {
            var checked = $(checkbox).prop('checked');
            $('.for_test1').prop('disabled', true);

            axios.post('{{route('adminSyllabusTest1Set', ['disciplineId' => $disciplineId])}}', {
                syllabusId: id,
                on: checked
            })
            .then(function (response) {
                $('.for_test1').prop('disabled', false);
            });
        }

// -------------------------------------- вопросы -------------------------------------------

    Vue.component('summernote', {
            template: `<textarea  v-on:change="$emit('input', $event.target.value)" v-bind:value="value">@{{value}}</textarea>`,
            props: {
                value: {
                    required: true,
                },

                height: {
                    type: String,
                    default: '150'
                }
            },
            methods: {
                onChange: function () {
                    console.log('ok');
                }
            },
            mounted() {
                let config = {
                    height: this.height
                };

                let vm = this;

                $(this.$el).summernote(config);
                $(this.$el).on('summernote.change', function (e) {
                    vm.$emit('input', $(vm.$el).summernote().code());
                });
                $(this.$el).on('summernote.blur', function (e) {
                    vm.$emit('input', $(vm.$el).summernote().code());
                });
            }
        });

        var app = new Vue({
            el: '#main-quiz-list',
            data: {
                quizList: [],
                quizEdit: null,
                processed: false,
                language: '{{ $defaultLang }}'                
            },
            methods: {
                loadQuestion: function (questionId) {
                    var self = this;

                    axios.post('{{route('adminSyllabusQuestionInfo', ['disciplineId' => $disciplineId])}}', {
                        id: questionId
                    })
                        .then(function (response) {
                            self.quizEdit = response.data;

                            self.quizEdit.validateCheckBox = function () {
                                for (var i = 0; i < this.answers.length; i++) {
                                    if (this.answers[i].correct) {
                                        return true;
                                    }
                                }

                                return false;
                            };

                            self.quizEdit.validateCorrectPoints = function () {
                                for (var i = 0; i < this.answers.length; i++) {
                                    if (this.answers[i].correct && (this.answers[i].points < 1 || this.answers[i].points > 5)) {
                                        return false;
                                    }
                                }

                                return true;
                            };

                            self.quizEdit.validateEmptyText = function () {
                                var pattern = /\s/gi;

                                if (this.question.replace(pattern, '').length === 0) {
                                    return false;
                                }

                                for (var i = 0; i < this.answers.length; i++) {
                                    if (this.answers[i].answer.replace(pattern, '').length === 0)
                                        return false;
                                }

                                return true;
                            };

                            self.processed = false;
                        });
                },
                showEditQuize: function (questionId) {
                    this.loadQuestion(questionId);
                },
                deleteQuize: function (questionId) {
                    if (!confirm('Удалить вопрос?')) {
                        return;
                    }
                    var self = this;
                    axios.post('{{route('adminSyllabusDeleteQuize', ['disciplineId' => $disciplineId])}}',
                        {id: questionId}
                    )
                        .then(function (response) {
                            self.quizEdit = null;
                            self.loadQuizList();
                        });
                },
                createQuestion: function () {
                    this.quizEdit = {
                        id: 0,
                        question: '',
                        answers: [],
                        audiofiles: [],
                        validateCheckBox: function () {
                            for (var i = 0; i < this.answers.length; i++) {
                                if (this.answers[i].correct)  {
                                    return true;
                                }
                            }

                            return false;
                        },
                        validateCorrectPoints: function () {
                            for (var i = 0; i < this.answers.length; i++) {
                                if (this.answers[i].correct && (this.answers[i].points < 1 || this.answers[i].points > 5)) {
                                    return false;
                                }
                            }

                            return true;
                        },
                        validateEmptyText: function () {
                            var pattern = /\s/gi;

                            if (this.question.replace(pattern, '').length === 0) {
                                return false;
                            }

                            for (var i = 0; i < this.answers.length; i++) {
                                if (this.answers[i].answer.replace(pattern, '').length === 0)
                                    return false;
                            }

                            return true;
                        }
                    };
                    this.processed = false;
                },
                createAnswer: function () {
                    this.quizEdit.answers.push({
                        id: 0,
                        answer: '',
                        points: 0,
                        correct: false
                    });
                },
                processFile: function (event) {
                    var file = event.target.files[0];
                    if (file) {
                        var self = this;
                        self.quizEdit.uploadAudio = {
                            filename: file.name,
                            source: ''
                        };

                        var reader = new FileReader();
                        reader.readAsBinaryString(file);
                        reader.onload = function (evt) {
                            self.quizEdit.uploadAudio.source = btoa(evt.target.result);
                        }
                    }
                },
                saveQuestion: function () {
                    if (!this.quizEdit.validateEmptyText()) {
                        alert('Необходимо заполнить все ответы и вопрос.');
                        return;
                    }

                    if (!this.quizEdit.validateCheckBox()) {
                        alert('Необходимо задать хотябы один верный ответ');
                        return;
                    }

                    if (!this.quizEdit.validateCorrectPoints()) {
                        alert('Для всех верных ответов необходимо задать балл от 1 до 5');
                        return;
                    }

                    var self = this;
                    this.processed = true;
                    axios.post('{{route('adminSyllabusSaveQuizQuestion', ['disciplineId' => $disciplineId, 'themeId' => $themeIdQuizVoid])}}',
                        this.quizEdit
                    )
                        .then(function (response) {
                            self.quizEdit = null;
                            self.loadQuizList();
                            self.processed = false;
                        });
                },
                loadQuizList: function () {
                    var self = this;
                    axios.post('{{route('adminSyllabusQuizList', ['disciplineId' => $disciplineId, 'themeId' => $themeIdQuizVoid])}}', {})
                        .then(function (response) {
                            self.quizList = response.data;
                        });
                }
            },
            created: function () {
                this.loadQuizList();
            }
        });
        
        
        if ({{$themeIdQuizVoid}} > 0) {
            $("#addQuizBtn").removeClass("hide");
            $("#addQuizNeedThemeMsg").addClass("hide");   
        } else { 
            $("#addQuizBtn").addClass("hide");
            $("#addQuizNeedThemeMsg").removeClass("hide");   
        }             
        
        
// ------------------------------------------------ конец вопросы ----------------------------------
        
 </script>
@endsection
