@extends("admin.admin_app")

@section('title', 'Учебный план')

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Учебный план</h2>

            <a href="{{URL::route('adminStudyPlanList')}}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div><strong>ID студента:</strong> {{$user->id}}</div>
                <div><strong>Студент:</strong> <a href="{{route('adminStudentEdit', ['id' => $user->id])}}">{{$user->studentProfile->fio}}</a> ({{$user->study_year}} курс)</div>
                <div><strong>Специальность:</strong> <a href="{{route('specialityEdit', ['id' => $user->studentProfile->speciality->id])}}">{{$user->studentProfile->speciality->name}} ({{$user->studentProfile->speciality->year}})</a></div>
                <div><strong>Базовое образование:</strong> @lang($user->base_education)</div>
                <div><strong>Форма обучения:</strong> @lang($user->studentProfile->education_study_form)</div>

                <div class="margin-t20 margin-b20">
                    <table class="table table-bordered" style="width:300px;">
                        <thead>Кредитность</thead>
                        <tr>
                            <th>Семестр</th>
                            <th>Кредитов максимум</th>
                            <th>Кредитов выбрано</th>
                            <th>Кредитов свободно</th>
                        </tr>

                        <tr v-for="semester in semesters">
                            <td v-text="semester.semester"></td>
                            <td v-text="semester.max"></td>
                            <td v-text="semester.credits"></td>
                            <td v-text="semester.max - semester.credits"></td>
                        </tr>
                    </table>
                </div>

                <table class="table table-striped table-hover table-bordered">
                    <tr>
                        <th>Дисциплина</th>
                        <th>Зависимость</th>
                        <th>Результат</th>
                        <th>Кредитность</th>
                        <th>Рекомендуемый. сем.</th>
                        <th>Купленна в семестре</th>
                        <th>План</th>
                        <th>Утверждена преп.</th>
                        <th>Утверждена студ.</th>
                        <th></th>
                    </tr>

                    @foreach($SDs as $SD)
                        <tr>
                            <td>{{$SD->discipline->name}} (<a href="{{route('disciplineEdit', ['id' => $SD->discipline_id])}}">id{{$SD->discipline_id}}</a>)</td>
                            <td class="padding-0">
                                @if(count($SD->dependencies))
                                    <table class="table table-striped table-hover table-bordered margin-0">
                                        @foreach($SD->dependencies as $year =>  $depList)
                                            <tr>
                                                <td>{{$year}}</td>
                                                <td class="padding-0">
                                                    @foreach($depList as $dependencies)
                                                        <div class="list-group-item">
                                                            @foreach($dependencies as $i => $dependency)
                                                                {{$dependency['name']}} (<a href="{{route('disciplineEdit', ['id' => $dependency['id']])}}">id{{$dependency['id']}}</a>) @if($i < count($dependencies)-1) {{ __('or') }} @endif
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
<!--                            --><?php //print_r($SD->dependencies) ?>
                            <td>
                                @if($SD->final_result !== null)
                                    {{$SD->final_result}}, {{$SD->final_result_letter}}
                                @endif
                            </td>
                            <td>{{$SD->discipline->ects}}</td>
                            <td><span class="badge" @if($SD->recommended_semester % 2)style="background-color: #0e6dcd;"@else style="background-color: orange;" @endif>{{$SD->recommended_semester}}</span></td>
                            <td>{{$SD->at_semester}}</td>
                            <td v-text="disciplines[{{$SD->discipline_id}}].plan_semester"></td>
                            <td v-text="disciplines[{{$SD->discipline_id}}].plan_admin_confirm"></td>
                            <td v-text="disciplines[{{$SD->discipline_id}}].plan_student_confirm"></td>
                            <td>
                                <div style="min-height: 37px;">
                                    <button
                                            class="btn btn-success"
                                            v-if="disciplines[{{$SD->discipline_id}}].addButtonVisible"
                                            v-on:click="addToPlan({{$SD->discipline_id}})"
                                            :disabled="requestSent"
                                    >Добавить</button>

                                    <button
                                            class="btn btn-danger"
                                            v-if="disciplines[{{$SD->discipline_id}}].deleteButtonVisible"
                                            v-on:click="deleteFromPlan({{$SD->discipline_id}})"
                                            :disabled="requestSent"
                                    >Удалить</button>

                                    <button
                                            class="btn btn-warning"
                                            v-if="disciplines[{{$SD->discipline_id}}].changeButtonVisible"
                                            v-on:click="changeSemester({{$SD->discipline_id}})"
                                            :disabled="requestSent"
                                    >Сменить</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>

                <hr>

                <button
                        class="btn btn-lg btn-success"
                        v-on:click="confirmPlan()"
                        :disabled="requestSent"
                >Утвердить план на 2019-20.2</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.min.js"></script>
    <script type="text/javascript">
        const SEMESTER = '2019-20.2';

        var vueApp = new Vue({
            el: '#main',
            data: {
                requestSent: false,
                semesters: {
                    @foreach($semesterCredits as $planSemester => $credits)
                        '{{$planSemester}}' : {
                            semester: '{{$planSemester}}',
                            max: @if($credits['semester'] == 3) {{\App\StudentDiscipline::MAX_CREDITS_AT_SEMESTER3}} @else {{$user->semester_credits_limit}} @endif,
                            credits : '{{$credits['credits']}}'
                        },
                    @endforeach
                },
                disciplines : {
                    @foreach($SDs as $SD)
                        '{{$SD->discipline_id}}' : {
                            id : '{{$SD->discipline_id}}',
                            hasDependencies : @if (!empty($SD->dependencies)) true @else false @endif,
                            credits : '{{$SD->discipline->ects}}',
                            final_result : '{{$SD->final_result}}',
                            at_semester: '{{$SD->at_semester}}',
                            plan_semester: '{{$SD->plan_semester}}',
                            plan_admin_confirm: '@if (!empty($SD->plan_admin_confirm)) да @else  @endif',
                            plan_student_confirm: '@if (!empty($SD->plan_student_confirm)) да @else  @endif',
                            addButtonVisible: false,
                            deleteButtonVisible : false,
                            changeButtonVisible : false
                        },
                    @endforeach
                }
            },
            methods: {
                setAddButtonVisibility : function(disciplineId) {
                    var discipline = this.disciplines[disciplineId];

                    this.disciplines[disciplineId].addButtonVisible =
                        // !discipline.hasDependencies &&
                        !discipline.final_result &&
                        discipline.at_semester === '' &&
                        discipline.plan_semester === '' &&
                        this.semesters[SEMESTER].credits*1 + discipline.credits*1 <= this.semesters[SEMESTER].max*1;
                },
                setDeleteButtonVisibility : function(disciplineId) {
                    this.disciplines[disciplineId].deleteButtonVisible =
                        this.disciplines[disciplineId].plan_semester !== '' &&
                        this.disciplines[disciplineId].at_semester === '';
                },
                setChangeButtonVisible : function(disciplineId) {
                    this.disciplines[disciplineId].changeButtonVisible =
                        (
                            this.disciplines[disciplineId].final_result !== '' ||
                            this.disciplines[disciplineId].at_semester !== ''
                        )
                        &&
                        this.disciplines[disciplineId].plan_semester !== SEMESTER &&
                        this.semesters[SEMESTER].credits*1 + this.disciplines[disciplineId].credits*1 <= this.semesters[SEMESTER].max*1;
                },
                deleteFromPlan : function(disciplineId) {
                    var self = this;

                    this.requestSent = true;

                    axios.post('{{route('adminStudyPlanDelete')}}', {
                        'disciplineId': disciplineId,
                        'userId' : '{{$user->id}}',
                        'semester' : SEMESTER
                    })
                        .then(function (response) {
                            if (response.data.success) {
                                self.semesters[SEMESTER].credits = self.semesters[SEMESTER].credits*1 - self.disciplines[disciplineId].credits*1;
                                self.disciplines[disciplineId].plan_semester = '';
                                self.disciplines[disciplineId].plan_admin_confirm = '';
                                self.disciplines[disciplineId].plan_student_confirm = '';

                                self.updateButtonsVisibility();
                            } else {
                                alert(response.data.error);
                            }

                            self.requestSent = false;
                        });
                },
                addToPlan : function(disciplineId) {
                    var self = this;

                    this.requestSent = true;

                    axios.post('{{route('adminStudyPlanAdd')}}', {
                        'disciplineId': disciplineId,
                        'userId' : '{{$user->id}}',
                        'semester' : SEMESTER
                    })
                        .then(function (response) {
                            if (response.data.success) {
                                self.semesters[SEMESTER].credits = self.semesters[SEMESTER].credits * 1 + self.disciplines[disciplineId].credits * 1;
                                self.disciplines[disciplineId].plan_semester = SEMESTER;

                                self.updateButtonsVisibility();
                            } else {
                                alert(response.data.error);
                            }

                            self.requestSent = false;
                        });
                },
                changeSemester : function(disciplineId) {
                    var self = this;

                    this.requestSent = true;

                    axios.post('{{route('adminStudyPlanChange')}}', {
                        'disciplineId': disciplineId,
                        'userId' : '{{$user->id}}',
                        'semester' : SEMESTER
                    })
                        .then(function (response) {
                            if (response.data.success) {
                                self.semesters[SEMESTER].credits = self.semesters[SEMESTER].credits*1 + self.disciplines[disciplineId].credits*1;
                                self.disciplines[disciplineId].plan_semester = SEMESTER;
                                self.disciplines[disciplineId].plan_admin_confirm = 'да';
                                self.disciplines[disciplineId].plan_student_confirm = 'да';

                                self.updateButtonsVisibility();
                            } else {
                                alert(response.data.error);
                            }

                            self.requestSent = false;
                        });
                },
                updateButtonsVisibility: function() {
                    var self = this;

                    $.each(this.disciplines, function(index, discipline) {
                        self.setAddButtonVisibility(discipline.id);
                        self.setDeleteButtonVisibility(discipline.id);
                        self.setChangeButtonVisible(discipline.id);
                    });
                },
                confirmPlan: function() {
                    var self = this;

                    var confirmText = "После подтверждения дисциплины будут открыты студенту для покупки.";

                    if (this.semesters[SEMESTER].credits != this.semesters[SEMESTER].max) {
                        confirmText += " <strong>Внимание! Не использованы " + (this.semesters[SEMESTER].max*1 - this.semesters[SEMESTER].credits*1) + " кредитов.</strong>"
                    }

                    bootbox.confirm(confirmText, function(result) {
                        if (result === false) {
                            return;
                        }

                        self.requestSent = true;

                        axios.post('{{route('adminStudyPlanConfirm')}}', {
                            'userId' : '{{$user->id}}',
                            'semester' : SEMESTER
                        })
                            .then(function (response) {
                                if (response.data.success) {
                                    $.each(self.disciplines, function(index, discipline) {
                                        if (discipline.plan_semester == SEMESTER) {
                                            self.disciplines[index].plan_admin_confirm = 'да';
                                        }
                                    });

                                    bootbox.alert({
                                        message: "План на " + SEMESTER + " утверждён",
                                        centerVertical: true
                                    });
                                } else {
                                    alert(response.data.error);
                                }

                                self.requestSent = false;
                            });
                    });
                }
            },
            created: function () {
                this.updateButtonsVisibility();
            }
        });
    </script>
@endsection