@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/journal-table.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/bootstrap-datepicker.min.css') }}">
@endsection

<div id="main_block" class="margin-b70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('Journal')</div>

                    <div class="panel-body margin-bottom">
                        <form class="form-horizontal" id="form_journal">
                            <div class="col-md-4" id="discipline">
                                <select name="discipline" v-model="disciplineId">
                                    <option disabled>Дисциплина не выбрана</option>
                                    @foreach($disciplines as $discipline)
                                        <option value="{{ $discipline->id }}">
                                            {{ $discipline->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4" id="studyGroup">
                                <select name="studyGroup" class="selectpicker" v-if="studyGroups" v-model="studyGroup">
                                    <option disabled>Группа не выбрана</option>
                                    <option v-for="(name, id) in studyGroups" :value="id">@{{ name }}</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="semester">
                                <select name="semester" class="selectpicker" v-if="semesters" v-model="semester">
                                    <option disabled>Семестр не выбран</option>
                                    <option v-for="(semester, index) in semesters" :value="semester">@{{ semester.semester }}</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="alert alert-danger" v-for="(error, index) in errors" role="alert">
                    @{{ error }}
                    <i class="pull-right fa fa-remove" v-on:click="errors.splice(index, 1)"></i>
                </div>
            </div>
        </div>
    </div>
    <div v-if="Object.keys(students).length && studyGroup" class="table-wrapper">
        <table class="table table-hover dt-responsive table-bordered table-sticky" cellspacing="0">
            <thead>
            <tr id="date">
                <th class="text-center full-header-fix">
                    Дата
                </th>
                <th v-for="(schedule, index) in schedules" class="text-center full-header-fix">
                    <div class="text-center" style="width: inherit">
                        <input type="text" name="date" :value="schedule.date" required="required" class="form-control"
                               @input="addToSchedule(schedule, $event)">
                    </div>
                </th>
                <th class="text-center full-header-fix" rowspan="3" @click="addSchedule($event)">
                    <i class="fa fa-plus"></i> Добавить столбец
                </th>
                <th class="text-center full-header-fix" rowspan="3">
                    Итоговая оценка
                </th>
            </tr>
            <tr id="lesson-type">
                <th class="text-center full-header-fix">
                    Вид занятия
                </th>
                <th v-for="(schedule, index) in schedules" class="text-center full-header-fix">
                    <div class="text-center" style="width: inherit">
                        <input type="text" name="lesson_type" :value="schedule.lesson_type" required="required" class="form-control"
                               @keyup.enter="addToSchedule(schedule, $event)" v-if="schedule.date">
                    </div>
                </th>
            </tr>
            <tr id="topic">
                <th class="text-center full-header-fix">
                    Тема
                </th>
                <th v-for="(schedule, index) in schedules" class="text-center full-header-fix">
                    <div class="text-center" style="width: inherit">
                        <input type="text" name="topic" :value="schedule.topic" required="required" class="form-control"
                               @keyup.enter="addToSchedule(schedule, $event)" v-if="schedule.date">
                    </div>
                </th>
            </tr>
            </thead>
        </table>
        <table class="table table-hover table-bordered table-body">
            <tbody id="tbody-journal">
            <tr v-for="student in students" v-bind:key="student.user_id">
                <td><b>@{{ student.fio }}</b></td>
                <td v-for="(lesson, index) in lessons" v-bind:key="index" v-on:click.self="setSelectedCell(index, student, lesson)">
                    <div v-on:click.self="setSelectedCell(index, student, lesson)"
                         v-if="!(selectedCell.index === index && selectedCell.student_id === student.user_id)"
                         style="width: inherit;" class="text-center">
                        @{{ getRating(lesson, student) }}
                    </div>

                    <input v-else-if="selectedCell.index === index && selectedCell.student_id === student.user_id"
                           type="text" class="form-control" pattern="[0-9]{0,3}"
                           :value="getRating(lesson, student)"
                           v-on:keyup.enter="setRating(lesson, student, $event)">
                </td>
                <td class="full-header-fix"></td>
                <td v-on:click.self="setSelectedCell('summary', student)">
                    <div v-on:click.self="setSelectedCell('summary', student)"
                         v-if="!(selectedCell.index === 'summary' && selectedCell.student_id === student.user_id)"
                         style="width: inherit;" class="text-center">
                        @{{ student.students_disciplines[0].final_result_points }}
                    </div>

                    <input v-else-if="selectedCell.index === 'summary' && selectedCell.student_id === student.user_id"
                           type="text" class="form-control" pattern="[0-9]{0,3}"
                           :value="student.students_disciplines[0].final_result_points"
                           v-on:keyup.enter="setFinalResult(student, $event)">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
</div>

@section('scripts')
    <script src="{{ URL::asset('assets/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
        let app = new Vue({
            el: '#main_block',
            data: {
                teacherId: {{ $teacherId }},
                studyGroups: null,
                studyGroup: null,
                discipline: {},
                disciplineId: null,
                semesters: null,
                semester: null,
                students: {},
                studentsIds: [],
                lessons: [],
                schedules: [],
                weekDays: [
                    'Понедельник',
                    'Вторник',
                    'Среда',
                    'Четверг',
                    'Пятница',
                    'Субота',
                    'Воскресенье',
                ],
                selectedCell: {},
                errors: [],
                canEditJournal: true,
            },
            methods: {
                loadGroups: function() {
                    this.studyGroup = null;
                    if (!this.disciplineId) {
                        return false;
                    }
                    const self = this;
                    axios.post('{{ route('getStudyGroups' . (isset($fromAdmin) ? 'Admin' : '')) }}',{
                        discipline: self.disciplineId,
                        teacher_id: self.teacherId
                    })
                        .then(function(response){
                            self.studyGroups = response.data.studyGroups;
                        }).then(function () {
                        $('select[name=studyGroup]').selectpicker('refresh');
                    });
                },
                loadSemesters: function() {
                    this.semester = null;
                    if (!this.disciplineId || !this.studyGroup) {
                        return false;
                    }
                    const self = this;
                    axios.post('{{ route('getSemesters' . (isset($fromAdmin) ? 'Admin' : '')) }}',{
                        disciplineId: self.disciplineId,
                        studyGroup: self.studyGroup,
                        teacher_id: self.teacherId
                    })
                    .then(function(response){
                        self.semesters = response.data.semesters;
                    }).then(function () {
                        $('select[name=semester]').selectpicker('refresh');
                    });
                },
                loadJournal: function() {
                    this.lessons = {};
                    if(!this.studyGroup || !this.disciplineId || !this.semester){
                        return false;
                    }
                    const self = this;
                    axios.post('{{ route('getTeacherJournal' . (isset($fromAdmin) ? 'Admin' : '')) }}',{
                        studyGroup: self.studyGroup,
                        discipline: self.disciplineId,
                        semester: self.semester.semester,
                        teacher_id: self.teacherId,
                        fromAdmin: {{ isset($fromAdmin) ?: 0 }}
                    })
                        .then(function(response) {
                            console.log(response.data);

                            if (response.data.status === false) {
                                throw response.data.message ?? 'Ошибка сервера';
                            }
                            self.lessons = response.data.lessons;
                            self.students = response.data.students;
                            self.studentsIds = response.data.studentsIds;
                            self.schedules = response.data.schedules;
                        })
                        .catch(function (message) {
                            self.errors.push(message);
                        });
                },
                getStudentLesson: function (lesson, student_id) {
                    if (!lesson.length) {
                        return  null;
                    }
                    const studentLesson = lesson.filter(function (students_lesson) {
                        return students_lesson.user_id === student_id;
                    });

                    if (!studentLesson.length) {
                        return null;
                    } else {
                        return studentLesson[0];
                    }
                },
                getRating: function (lesson, student) {
                    const studentLesson = this.getStudentLesson(lesson, student.user_id);
                    if (studentLesson) {
                        return studentLesson.rating;
                    }
                },
                setRating: function (lesson, student, event) {
                    const self = this;
                    let rating = event.target.value;
                    const oldStudentLesson = self.getStudentLesson(lesson, student.user_id);

                    axios.post('{{ route('setRating' . (isset($fromAdmin) ? 'Admin' : '')) }}',{
                        lesson_id: oldStudentLesson.id,
                        rating: rating,
                        fromAdmin: {{ isset($fromAdmin) ?: 0 }}
                    }).then(function (response) {
                        if (response.data.status === false) {
                            self.errors = [response.data.message];
                            self.selectedCell = {};
                            return false;
                        }

                        oldStudentLesson.rating = response.data.lesson.rating;

                        self.selectedCell = {};
                    });
                },
                setFinalResult: function (student, event) {
                    const self = this;
                    let rating = event.target.value;
                    const sd = student.students_disciplines[0];
                    axios.post('{{ route('setFinalResult' . (isset($fromAdmin) ? 'Admin' : '')) }}',{
                        sd_id: sd.id,
                        rating: rating,
                        fromAdmin: {{ isset($fromAdmin) ?: 0 }}
                    }).then(function (response) {
                        if (response.data.status === false) {
                            self.errors = [response.data.message];
                            self.selectedCell = {};
                            return false;
                        }

                        sd.final_result_points = response.data.studentDiscipline.final_result_points;

                        self.selectedCell = {};
                    });
                },
                setSelectedCell: function (index, student, lesson) {
                    const student_id = student.user_id;
                    let errors = [];

                    if (!this.canEditJournal) {
                        errors.push('Время выставления оценок не наступило.');
                    }

                    if (errors.length > 0 || (this.selectedCell.index === index && this.selectedCell.student_id === student_id)) {
                        this.errors = errors;
                        this.selectedCell = {};
                    } else {
                        this.errors = [];
                        this.selectedCell = {
                            index: index,
                            student_id: student_id,
                            // lesson_id: this.getStudentLesson(lesson, student_id).id
                        };
                    }
                },
                setDiscipline: function () {
                    const disciplines = Object.values(@json($disciplines));
                    this.discipline = disciplines.filter(discipline => {
                        return discipline.id == this.disciplineId;
                    })[0];
                },
                addSchedule: function (event) {
                    this.schedules.push({
                        'date': '',
                        'lesson_type': '',
                        'topic': ''
                    });

                    this.lessons.push({
                        lesson: [],
                    });
                },
                addToSchedule: function (schedule, event) {
                    schedule[event.target.name] = event.target.value;
                    const self = this;
                    axios.post('{{ route('addToSchedule' . (isset($fromAdmin) ? 'Admin' : '')) }}',{
                        disciplineId: self.disciplineId,
                        studyGroup: self.studyGroup,
                        teacher_id: self.teacherId,
                        semester: self.semester.semester,
                        studentsIds: self.studentsIds,
                        schedule: schedule,
                    })
                    .then(function(response){
                        console.log(response.data);
                        schedule = response.data.schedule;
                    })
                }
            },
            watch: {
                disciplineId: function(newVal) {
                    this.errors = [];
                    this.setDiscipline();
                    this.loadGroups();
                    this.loadSemesters();
                    this.loadJournal();
                },
                studyGroup: function(newVal) {
                    this.errors = [];
                    $('select[name=discipline]').selectpicker('refresh');
                    this.loadSemesters();
                    this.loadJournal();
                },
                semester: function () {
                    this.errors = [];
                    this.loadJournal();
                },
                schedules: {
                    deep: true,
                    handler() {
                        setTimeout(function () {
                            $('input[name=date]').datepicker({
                                format: "dd.mm.yyyy",
                                weekStart: 1,
                                autoclose: true,
                                todayHighlight: true
                            }).on('changeDate', function (event) {
                                this.dispatchEvent(new Event('input', {'bubbles': true}));
                            });
                        }, 1)
                    }
                }
            },
            mounted: function () {
                $('select[name=discipline]').selectpicker();
                $('input[name=date]').datepicker({
                    format: "dd.mm.yyyy",
                    weekStart: 1,
                    autoclose: true,
                    todayHighlight: true
                }).on('changeDate', function (event) {
                    this.dispatchEvent(new Event('input', {'bubbles': true}));
                });
            },
        });
    </script>
@endsection