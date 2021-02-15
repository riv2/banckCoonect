@extends('admin.admin_app')

@section('style')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@endsection

@section('content')
    <div id="app">
        <div class="page-header">
            <h2>Журнал</h2>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table class="table table-striped table-hover dt-responsive table-bordered">
                    <thead>
                        <tr>
                            <th>Семестр</th>
                            <th>Дисциплины</th>
                            <th>Преподаватели</th>
                            <th>Группы</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <select class="form-control" v-model="selectedSemester" @change="getDisciplines">
                                @foreach($semesters as $semester)
                                    <option value="{{$semester}}">
                                        {{ $semester }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <vue-multiselect
                                    v-model="selectedDiscipline"
                                    :options="disciplines"
                                    :searchable="true"
                                    label="name"
                                    :close-on-select="true"
                                    :show-labels="false"
                                    @close="getTeachers"
                                    placeholder="Pick a value">
                            </vue-multiselect>
                        </td>
                        <td>
                           <vue-multiselect
                                   v-model="selectedTeacher"
                                   :options="teachers"
                                   :searchable="true"
                                   :close-on-select="true"
                                   :show-labels="false"
                                   label="name"
                                   @close="getTeacherGroups"
                                   placeholder="Pick a value">
                           </vue-multiselect>
                        </td>
                        <td>
                            <select class="form-control" v-model="selectedStudyGroup" @change="getJournal">
                                <option v-for="group in studyGroups" :value="group.id">
                                    @{{ group.name }}
                                </option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div>
                    <students-journal
                            ref="studentsJournal"
                            :semester="selectedSemester"
                            :discipline="selectedDiscipline"
                            :teacher="selectedTeacher"
                            :month="selectedMonth"
                            :group="selectedStudyGroup">
                    </students-journal>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script>
        Vue.component('vue-multiselect', window.VueMultiselect.default)

        const StudentsJournalColHead =  Vue.component('students-journal-col-head', {
            props: ['day', 'columnTypes', 'editable'],
            computed: {
                className(){
                    let className = '';
                    if (this.day.type !== 'default_rating' && this.day.type !== 'teacher_default_rating'){
                        className = 'info';
                    }
                    if (this.day.type === 'exam_rating' || this.day.type === 'score_rating' || this.day.type === 'course_work_rating'){
                        className = 'warning';
                    }
                    if (this.day.type === 'result_rating'){
                        className = 'success';
                    }
                    return className;
                }
            },
            template: `
                <th :style="day.type !== 'default_rating' ? {'width': '90px'} : {}" :class="className">
                    <div class="text-center" v-if="day.type === 'default_rating'">
                        @{{ day.day }}
                    </div>
                    <div class="text-center" v-if="day.type === 'teacher_default_rating'" style="width: 100px">
                        <div>Занятие</div>
                        <span> @{{ day.day }}</span>
                    </div>
                    <div class="text-center text-info" v-if="day.type === 'certification_rating'" style="width: 100px">
                        <div>Аттестация</div>
                        <span> @{{ day.day }}</span>
                    </div>
                    <div class="text-center text-info" v-if="day.type === 'pre_certification_rating'" style="width: 100px">
                        <div>Пре-аттестация</div>
                        <span> @{{ day.day }}</span>
                    </div>
                    <div class="text-center text-warning" v-if="day.type === 'exam_rating' || day.type === 'score_rating' || day.type === 'course_work_rating'">
                        @{{ columnTypes[day.type] }}
                    </div>
                    <div class="text-center text-success" v-if="day.type === 'result_rating'">
                        @{{ columnTypes[day.type] }}
                    </div>
                </th>
            `
        });

        const StudentsJournal = Vue.component('students-journal', {
            data () {
                return {
                    profiles: [],
                    days: [],
                    ratingDays: {},
                    columnTypes: {},
                    teacherTypes: {},
                    studentsDays: [],
                    isEditable: false,
                    month: null,
                    months: [],
                    load: false,
                }
            },
            props: ['semester', 'group', 'discipline', 'teacher'],
            computed: {
                selectedMonth(){
                    return this.months.find(m => m.num === this.month)
                },
                isAllFiltersFiled(){
                    return this.groupId !== null &&
                            this.discipline !== null &&
                            this.teacherId !== null &&
                            this.semester !== null;
                }
            },
            components: {
                'coll-head': StudentsJournalColHead,
            },
            methods: {
                getDisciplineGroupStudents(){
                    if (this.isAllFiltersFiled){
                        const data = {
                            groupId: this.group,
                            disciplineId: this.discipline.id,
                            teacherId: this.teacher.id,
                            semester: this.semester,
                            year: this.selectedMonth?.year ?? null,
                            month: this.month ?? null,
                        };
                        this.days = null;
                        this.load = true;

                        axios.post(`{{route('admin.teacher.journal.getDisciplineGroupStudents')}}`, data)
                            .then(({data}) => {
                                this.profiles = data.profiles;
                                this.days = data.days;
                                this.studentsDays = data.studentsDays;
                                this.months = data.months;
                                this.load = false;
                            })
                    }
                },
                getTeacherTypes(){
                    axios.post('{{route('admin.teacher.journal.getTypes')}}')
                        .then(({data}) =>{
                            this.columnTypes = data.allTypes;
                            this.teacherTypes = data.teacherTypes;
                        })
                },
            },
            watch: {
                month(){
                    this.getDisciplineGroupStudents();
                },
                semester(){
                    this.getDisciplineGroupStudents();
                },
                group(){
                    this.getDisciplineGroupStudents();
                }
            },
            created(){
                this.getTeacherTypes();
            },
            template: `
             <div>
                <p v-if="load" class="text-center">
                    <i class="fa fa-spin fa-spinner"></i>
                    Запрос обрабатывается...
                </p>
                 <div class="table-responsive padding-5" v-if="months.length > 0">
                    <div style="display: flex; justify-content:center;">
                        <div class="padding-10">
                            <select class="form-control" v-model="month">
                                <option v-for="month in months" :value="month.num">
                                    @{{ month.name }} (@{{ month.year }})
                                </option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <table class="table table-striped dt-responsive table-bordered" cellspacing="0" width="100%" v-if="days !== null">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ФИО</th>
                                    <coll-head :key="key" v-for='(day, key) in days' :day='day' :columnTypes="columnTypes" :editable="false"></coll-head>
                                </tr>
                            </thead>
                            <tbody v-if="profiles">
                                <tr v-for="profile in profiles">
                                    <td>
                                        @{{ profile.user_id }}
                                    </td>
                                    <td>
                                        @{{ profile.fio }}
                                    </td>
                                    <td v-for="day in days" class="text-center">
                                        @{{ day.students !== null ? day.students[profile.user_id] ?? null: null }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            `
        });

        const app = new Vue({
            el: '#app',
            data: {
                teachers: [],
                selectedTeacher: [],
                disciplines: [],
                selectedDiscipline: null,
                studyGroups: [],
                selectedStudyGroup: null,
                selectedSemester: null,
                months: [],
                selectedMonth: null,
            },
            components: {
                'students-journal': StudentsJournal
            },
            methods: {
                getDisciplines(){
                    axios.post('{{route('admin.assign.teachers.getDisciplinesBySemester')}}/' + this.selectedSemester)
                        .then(({data}) => this.disciplines = data)
                },
                getTeachers(){
                    if (this.selectedDiscipline !== null){
                        axios.post('{{route('admin.assign.teachers.getDisciplineTeachers')}}/' + this.selectedDiscipline.id)
                            .then(({data}) => this.teachers = data)
                    }
                },
                getTeacherGroups(){
                    const data = {
                        disciplineId: this.selectedDiscipline.id,
                        teacherId: this.selectedTeacher.id
                    };
                    if (data.disciplineId !== null && data.teacherId !== null){
                        axios.post('{{route('admin.teacher.journal.getTeacherGroups')}}', data)
                            .then(({data}) => this.studyGroups = data)
                    }
                },
                getJournal(){
                    this.$nextTick(function () {
                        this.$refs.studentsJournal.getDisciplineGroupStudents();
                    })
                },
                changeSemester(semester){
                    this.selectedSemester = semester;
                },
            },
        })
    </script>
@endsection