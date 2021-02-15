<div id="main-quiz-list">
    <div class="form-group">
        <label for="self_with_teacher_hours" class="col-md-3 control-label">Вопросы</label>
        <div class="col-md-9">

            <table id="question-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                <tbody>
                <tr v-for="quiz in quizList">
                    <td v-html="quiz.question"></td>

                    <td class="text-right">

                        <div class="btn-group">
                            <a class="btn btn-default" v-on:click="showEditQuize(quiz.id)"><i class="md md-edit"></i></a>
                            <a class="btn btn-default" v-on:click="deleteQuize(quiz.id)"><i class="fa fa-remove"></i></a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <a v-on:click="createQuestion()" class="btn btn-default">Добавить вопрос <i class="fa fa-plus"></i></a>
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
@section('scripts')
    <script type="text/javascript">
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
                processed: false
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
                    axios.post('{{route('adminSyllabusSaveQuizQuestion', ['disciplineId' => $disciplineId, 'themeId' => $syllabus->id])}}',
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
                    axios.post('{{route('adminSyllabusQuizList', ['disciplineId' => $disciplineId, 'themeId' => $syllabus->id])}}', {})
                        .then(function (response) {
                            self.quizList = response.data;
                        });
                }
            },
            created: function () {
                this.loadQuizList();
            }
        });
    </script>
@endsection