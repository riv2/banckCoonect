@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <a href="{{ route('admin.quiz.show') }}" class="btn btn-default-light btn-xs">
                <i class="md md-backspace"></i> Назад
            </a>
        </div>

        <div class="panel panel-default">
            {!! Form::open(array(
                            'url' => array( route('admin.quiz.edit', ['quiz_id' => $poll->id ?? '']) ),
                            'class'=>'form-horizontal padding-15',
                            'name'=>'quiz_form',
                            'id'=>'quiz_form',
                            'role'=>'form',
                            'enctype' => 'multipart/form-data')
            ) !!}
                <div class="panel-body">
                    <div class="form-group
                        @if(!empty($errors->first('title_ru')))
                            has-error
                        @endif
                    ">
                        <label for="title_ru" class="col-sm-3 control-label">Название (ru)</label>

                        <div class="col-sm-9">
                            <input type="text" required name="title_ru" id="title_ru" value="{{ old('title_ru') ?? $poll->title_ru ?? '' }}" class="form-control">

                            @if(!empty($errors->first('title_ru')))
                                <span class="help-block">
                                    {{ $errors->first('title_ru') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group
                        @if(!empty($errors->first('title_kz')))
                            has-error
                        @endif
                    ">
                        <label for="title_kz" class="col-sm-3 control-label">Название (kz)</label>

                        <div class="col-sm-9">
                            <input type="text" required name="title_kz" id="title_kz" value="{{ old('title_kz') ?? $poll->title_kz ?? '' }}" class="form-control">

                            @if(!empty($errors->first('title_kz')))
                                <span class="help-block">
                                    {{ $errors->first('title_kz') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="end_date" class="col-sm-3 control-label">Дата окончания</label>

                        <div class="col-sm-9">
                            <input
                                    name="end_date"
                                    type="date"
                                    class="form-control"
                                    value="@if(!empty(old('end_date'))){{old('end_date')}}@elseif(!empty($poll)){{$poll->end_date->format('Y-m-d')}}@endif"
                                    required
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="is_required" class="col-sm-3 control-label">Обязательная</label>

                        <div class="col-sm-1">
                            <input id="is_required" name="is_required" type="checkbox" {{ (old('is_required') ?? $poll->is_required ?? false )? 'checked' : '' }} />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="is_active" class="col-sm-3 control-label">Активная</label>

                        <div class="col-sm-1">
                            <input id="is_active" name="is_active" type="checkbox" {{ (old('is_active') ?? ($poll->is_active ?? false) )? 'checked' : '' }} />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-2">
                            <button class="btn btn-primary">Сохранить</button>
                        </div>

                        @if(!empty($poll->id))
                            <div class="col-sm-2">
                                <button class="btn btn-primary" type="button" @click="editQuizUsersTable">Пользователи</button>
                            </div>
                        @endif
                    </div>
                </div>

                @if(!empty($poll->id))
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Пользователи

                                <span>
                                    <button id="active-users-poll-clear" class="btn btn-primary" type="button">Очистить список</button>
                                </span>
                            </h3>
                        </div>

                        <div class="panel-body">
                            <table id="data-table-users-active" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ФИО</th>
                                        <th>Действие</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {{-- Insert by DataTable --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Вопросы

                            <button class="btn btn-primary" @click="addQuestion" type="button">
                                <i class="fa fa-plus"></i>
                            </button>
                        </h3>
                    </div>

                    <div class="panel-body" id="questions">
                        <question-block
                            v-for="(question, index) in questions"
                            :text_ru="question.text_ru"
                            :text_kz="question.text_kz"
                            :is_multiple="question.is_multiple"
                            :is_custom_answer="question.is_custom_answer"
                            :answers="question.answers"
                            :question_number="index"
                            :is_new="question.is_new"
                            :is_active="{{ (!empty($poll->is_active) && $poll->is_active == true) ? 'true' : 'false' }}"
                            v-on:add-question-value-ru="addQuestionValueRu"
                            v-on:add-question-value-kz="addQuestionValueKz"
                            v-on:add-question-value-multiple="addQuestionValueMultiple"
                            v-on:add-question-value-custom-answer="addQuestionValueCustomAnswer"
                            v-on:add-answer="addAnswer"
                        >
                        </question-block>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>

    @if(!empty($poll->id))
        {{-- add/edit users --}}
        <div id="editQuizUsersTable" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <h5 class="modal-title"> Пользователи </h5>
                </div>

                <div class="modal-body">
                    <table id="data-table-poll-users" class="table table-striped table-hover dt-responsive text-center" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    ФИО
                                    <div>
                                        <input type="text" class="form-control filter-input" id="filter_name">
                                    </div>
                                </th>
                                <th class="text-center">
                                    Роль
                                    <div>
                                        <select data-width="auto" title="" class="form-control filter-select" id="filter_role">
                                            <option value=""></option>
                                            @foreach($roles as $code => $role)
                                                <option value="{{ $code }}">{{ $role }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </th>
                                <th class="text-center">
                                    Категория
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <select data-width="auto" id="filter_categories" class="filter-select selectpicker" multiple>
                                                @foreach($categories as $code => $category)
                                                    <option value="{{ $code }}">{{ $category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </th>
                                <th class="text-center">
                                    Курс
                                    <div>
                                        <select data-width="50px" title="" id="filter_course" class="form-control filter-select" multiple>
                                            <option value=""></option>
                                            @foreach($courses as $key => $course)
                                                <option value="{{ $key }}">{{ $course }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </th>
                                <th class="text-center">
                                    Форма обучения
                                    <div>
                                        <select id="filter_study_from" class="form-control filter-select" title="" data-width="auto" multiple>
                                            <option value=""></option>
                                            @foreach($studyForms as $key => $studyForm)
                                                <option value="{{ $key }}">{{ $studyForm }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </th>
                                <th class="text-center">
                                    Специальность
                                    <div>
                                        <select id="filter_group" class="form-control filter-select" data-live-search="true" title="" data-width="auto" multiple>
                                            <option value=""></option>
                                            @foreach($studentGroups as $group)
                                                <option value="{{ $group->team }}">{{ $group->team }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </th>
                                <th class="text-center">
                                    <input type="checkbox" class="quiz-users">
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                        {{-- Insert by DataTable ajax --}}
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer" style="margin-top: 35px;">
                    <button type="button" class="btn btn-primary poll-users-all-save" data-poll-id="{{ $poll->id }}">Добавить всех</button>
                    <button type="button" class="btn btn-primary poll-users-save" data-poll-id="{{ $poll->id }}">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')

    <script type="text/javascript">
        var users = {};

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.filter-select').selectpicker();

        var dataUsersTable = $('#data-table-poll-users').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            searching: false,
            ajax: {
                url :"{{ route('admin.quiz.users.show.table') }}",
                type: "post",
                data: function (d) {
                    d.users = users;
                    d.name = $('#filter_name').val(),
                    d.role = $('#filter_role').val(),
                    d.categories = $('#filter_categories').val(),
                    d.course = $('#filter_course').val(),
                    d.study_form = $('#filter_study_from').val(),
                    d.group = $('#filter_group').val()
                }
            },
            columns: [
                {
                    data: 'name',
                    className: 'text-center',
                },
                {
                    data: 'role',
                    className: 'text-center',
                },
                {
                    data: 'category',
                    className: 'text-center',
                },
                {
                    data: 'course',
                    className: 'text-center',
                },
                {
                    data: 'study_form',
                    className: 'text-center',
                },
                {
                    data: 'group',
                    className: 'text-center',
                },
                {
                    data: 'is_checked',
                    className: 'text-center',
                },
            ]
        });

        Vue.component('question-block', {
            props: [
                'text_ru',
                'text_kz',
                'is_multiple',
                'is_custom_answer',
                'question_number',
                'answers',
                'is_new',
                'is_active',
            ],
            template: `
                        <div class="panel panel-default question">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="text_ru" class="col-sm-3 control-label">Вопрос (ru)</label>
                                    <div class="col-sm-8">
                                        <input
                                            v-if="is_new === true || is_active === false"
                                            id="text_ru"
                                            class="form-control"
                                            type="text"
                                            @input="$emit('add-question-value-ru', question_number, $event.target.value)"
                                            :name="'questions['+question_number+'][text_ru]'"
                                            :value="text_ru"
                                            required
                                        >

                                        <input
                                            v-else
                                            id="text_ru"
                                            class="form-control"
                                            type="text"
                                            :value="text_ru"
                                            disabled
                                        >
                                    </div>

                                    <div class="col-sm-1" v-if="is_new === true || is_active === false">
                                        <button class="btn btn-danger remove-question" type="button">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="text_kz" class="col-sm-3 control-label">Вопрос (kz)</label>
                                    <div class="col-sm-8">
                                        <input
                                            v-if="is_new === true || is_active === false"
                                            id="text_kz"
                                            class="form-control"
                                            type="text"
                                            @input="$emit('add-question-value-kz', question_number, $event.target.value)"
                                            :name="'questions['+question_number+'][text_kz]'"
                                            :value="text_kz"
                                            required
                                        >

                                        <input
                                            v-else
                                            id="text_kz"
                                            class="form-control"
                                            type="text"
                                            :value="text_kz"
                                            disabled
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="is_multiple" class="col-sm-3 control-label">Мультиответ</label>

                                    <div class="col-sm-1">
                                        <input
                                            v-if="is_new === true || is_active === false"
                                            type="checkbox"
                                            id="is_multiple"
                                            @input="$emit('add-question-value-multiple', question_number, $event.target.value)"
                                            :checked="is_multiple"
                                            :name="'questions['+question_number+'][is_multiple]'"
                                        >

                                        <input
                                            v-else
                                            type="checkbox"
                                            id="is_multiple"
                                            :checked="is_multiple"
                                            disabled
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="is_custom_answer" class="col-sm-3 control-label">Свой ответ</label>

                                    <div class="col-sm-1">
                                        <input
                                            v-if="is_new === true || is_active === false"
                                            @input="$emit('add-question-value-custom-answer', question_number, $event.target.value)"
                                            :checked="is_custom_answer"
                                            :name="'questions['+question_number+'][is_custom_answer]'"
                                            id="is_custom_answer"
                                            type="checkbox"
                                        >

                                        <input
                                            v-else
                                            :checked="is_custom_answer"
                                            id="is_custom_answer"
                                            type="checkbox"
                                            disabled
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-2">
                                        <button v-if="is_new === true || is_active === false" class="btn btn-primary" type="button" @click="$emit('add-answer', question_number)">Добавить ответ</button>
                                    </div>
                                </div>

                                <answer-block
                                    v-for="(answer, index) in answers"
                                    :text_ru="answer.text_ru"
                                    :text_kz="answer.text_kz"
                                    :answer_number="index"
                                    :question_number="question_number"
                                    :is_new="is_new"
                                    :is_active="is_active"
                                >
                                </answer-block>
                            </div>
                        </div>
            `
        });

        Vue.component('answer-block', {
            props: [
                'question_number',
                'answer_number',
                'text_ru',
                'text_kz',
                'is_new',
                'is_active',
            ],
            template: `
                        <div class="panel panel-default answer">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="text_ru" class="col-sm-3 control-label">Ответ (ru)</label>
                                    <div class="col-sm-8">
                                        <input
                                            v-if="is_new === true || is_active === false"
                                            type="text"
                                            required
                                            :value="text_ru"
                                            :name="'questions['+question_number+'][answers]['+answer_number+'][text_ru]'"
                                            class="form-control"
                                        >

                                        <input
                                            v-else
                                            type="text"
                                            disabled
                                            :value="text_ru"
                                            class="form-control"
                                        >
                                    </div>

                                    <div class="col-sm-1">
                                        <button v-if="is_new === true || is_active === false" class="btn btn-danger remove-answer" type="button">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="text_kz" class="col-sm-3 control-label">Ответ (kz)</label>
                                    <div class="col-sm-8">
                                        <input
                                            v-if="is_new === true || is_active === false"
                                            type="text"
                                            required
                                            :value="text_kz"
                                            :name="'questions['+question_number+'][answers]['+answer_number+'][text_kz]'"
                                            class="form-control"
                                        >

                                        <input
                                            v-else
                                            type="text"
                                            disabled
                                            :value="text_kz"
                                            class="form-control"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
            `
        });

        var main = new Vue({
            el: '#main',
            data: {
                editQuizUsersModal: false,
                editQuizUsersProcess: false,
                questions: {!! json_encode(old('questions') ?? $poll->questions ?? []) !!}
            },
            methods: {
                addQuestion: function () {
                    this.i++;
                    this.questions.push({
                        is_new: true,
                        answers: []
                    });
                },
                addAnswer: function (question_id) {
                    var empty_question = true;

                    if (this.questions[question_id] != undefined) {
                        if (this.questions[question_id].answers == undefined) {
                            this.questions[question_id]['answers'] = [];
                        }

                        this.questions[question_id].answers.push({});
                        empty_question = false;
                    }

                    if (empty_question) {
                        this.questions.push({
                            count: 1
                        });
                    }
                },
                editQuizUsersTable: function () {
                    $('#editQuizUsersTable').modal('toggle');
                },
                addQuestionValueRu: function (question_id, value) {
                    this.questions[question_id].text_ru = value;
                },
                addQuestionValueKz: function (question_id, value) {
                    this.questions[question_id].text_kz = value;
                },
                addQuestionValueMultiple: function (question_id, value) {
                    this.questions[question_id].is_multiple = value;
                },
                addQuestionValueCustomAnswer: function (question_id, value) {
                    this.questions[question_id].is_custom_answer = value;
                }
            },
            mounted: function () {
                this.$nextTick(function () {
                    this.dataUsersActiveTable = $('#data-table-users-active').DataTable({
                        processing: true,
                        serverSide: true,
                        ordering: false,
                        searching: false,
                        ajax: {
                            url : "{{ route('admin.quiz.users.active.show.table', ['poll_id' => $poll->id ?? '']) }}",
                            type: "post",
                        },
                        columns: [
                            {
                                data: 'name',
                                className: 'text-center',
                            },
                            {
                                data: 'action',
                                className: 'text-center',
                            },
                        ]
                    });
                });
            }
        });

        $('.panel-default').on('click', '.remove-question', function () {
            $(this).closest('.question').remove();
        });

        $('.panel-default').on('click', '.remove-answer', function () {
            $(this).closest('.answer').remove();
        });

        $('.filter-select').change(function () {
            dataUsersTable.draw();
        });

        $('.filter-input').change(function () {
            dataUsersTable.draw();
        });

        $('#editQuizUsersTable').on('change', '.quiz-user', function () {
            if ($(this).prop('checked')) {
                users[$(this).val()] = true;
            } else {
                $('.quiz-users').prop('checked', false);
                delete users[$(this).val()];
            }
        });

        $('.quiz-users').change(function () {
            if ($(this).prop('checked')) {
                $('.quiz-user').each(function () {
                    users[$(this).val()] = true;
                });
            } else {
                $('.quiz-user').each(function () {
                    delete users[$(this).val()];
                });
            }

            $('.quiz-user').prop('checked', $(this).prop('checked'));
        });

        $('.poll-users-save').click(function () {
            $.ajax({
                url: '{{ route('admin.quiz.edit.users', ['poll_id' => $poll->id ?? '']) }}',
                data: {
                    users: users
                },
                type: 'POST',
                success: function(){
                    users = {};
                    main.dataUsersActiveTable.draw();
                    $('#editQuizUsersTable').modal('hide');
                }
            });
        });

        $('#data-table-users-active').on('click', '.remove-active-user', function () {
            var user_id = $(this).attr('data-user-id');

            $.ajax({
                url: '{{ route('admin.quiz.user.active.remove', ['poll_id' => $poll->id ?? '']) }}',
                data: {
                    user_id: user_id
                },
                type: 'POST',
                success: function(){
                    main.dataUsersActiveTable.draw();
                }
            });
        });

        $('#active-users-poll-clear').click(function () {
            $.ajax({
                url: '{{ route('admin.quiz.user.active.clear', ['poll_id' => $poll->id ?? '']) }}',
                type: 'POST',
                success: function(){
                    main.dataUsersActiveTable.draw();
                }
            });
        });

        $('.poll-users-all-save').click(function () {
            $.ajax({
                url: '{{ route('admin.quiz.users.all.insert', ['poll_id' => $poll->id ?? '']) }}',
                data: {
                    name: $('#filter_name').val(),
                    role: $('#filter_role').val(),
                    categories: $('#filter_categories').val(),
                    course: $('#filter_course').val(),
                    study_form: $('#filter_study_from').val(),
                    group: $('#filter_group').val()
                },
                type: 'POST',
                success: function(result){
                    users = {};
                    main.dataUsersActiveTable.draw();
                    $('#editQuizUsersTable').modal('hide');
                }
            });
        });
    </script>
@endsection
