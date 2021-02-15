@extends("admin.admin_app")

@section('title', isset($discipline->name) ? $discipline->name : 'Добавить дисциплину')

@section('style')
    <link href="{{ URL::asset('admin_assets/css/selectize.default.css') }}" rel="stylesheet" type="text/css">
@endsection

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> {{ isset($discipline->name) ? 'Редактировать: '. $discipline->name : 'Добавить дисциплину' }}</h2>

            <a href="{{ URL::to('/disciplines') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

        </div>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-body">
                {!! Form::open(array('url' => array( route('POSTDisciplineAdd') ),'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data')) !!}
                <input type="hidden" name="id" value="{{ isset($discipline->id) ? $discipline->id : null }}">

                <div role="tabpanel">
                    <div class="tab-content tab-content-default">
                        <div role="tabpanel" class="tab-pane active" id="cz">
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Внешний ID</label>
                                <div class="col-sm-2">
                                    <input type="number" name="ex_id" value="{{ isset($discipline->ex_id) ? $discipline->ex_id : null }}" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Название дисциплины на русском</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" value="{{ isset($discipline->name) ? $discipline->name : null }}" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Название дисциплины на казахском</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name_kz" value="{{ isset($discipline->name_kz) ? $discipline->name_kz : null }}" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Название дисциплины на англ</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name_en" value="{{ isset($discipline->name_en) ? $discipline->name_en : null }}" class="form-control">
                                </div>
                            </div>
<!-- todo пока кредиты закомментированы.
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Кредиты KZT</label>
                                <div class="col-sm-9">
                                    <input type="number" name="credits" value="{{ isset($discipline->credits) ? $discipline->credits : null }}" class="form-control" required>
                                </div>
                            </div>
-->
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Кредиты ECTS</label>
                                <div class="col-sm-9">
                                    <input type="number" name="ects" value="{{ isset($discipline->ects) ? $discipline->ects : null }}" class="form-control" required>
                                </div>
                            </div>

<!-- todo пока кредиты закомментированы.
                            <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <label>
                                        <input type="checkbox" name="ru" {{ isset($discipline->ru) && $discipline->ru>0 ? 'checked' : null }}> ru
                                    </label><br/>

                                    <label>
                                        <input type="checkbox" name="kz" {{ isset($discipline->kz) && $discipline->kz>0 ? 'checked' : null }}> kz
                                    </label><br/>

                                    <label>
                                        <input type="checkbox" name="en" {{ isset($discipline->en) && $discipline->en>0 ? 'checked' : null }}> en
                                    </label>
                                </div>
                            </div>
-->
                            <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <div class="col-sm-6 padding-5" v-for="semester in semesters">
                                        <div class="card padding-0">
                                            <div class="card-header">
                                                <label class="col-md-6 control-label">Семестр @{{ semester.name }}</label>
                                                <button type="button" class="close">
                                                    <span @click="deleteSemester(semester.name)" aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-responsive">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>Очная</th>
                                                            <th>Заочная</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(name, type) in hoursTypes">
                                                            <td>@{{ name }}</td>
                                                            <td>
                                                                <input
                                                                    :name="`semesters[${semester.name}][fulltime][${type}]`"
                                                                    class="form-control"
                                                                    type="number"
                                                                    v-model="semester.hours.fulltime[type]">
                                                            </td>
                                                            <td>
                                                                <input
                                                                    :name="`semesters[${semester.name}][extramural][${type}]`"
                                                                    class="form-control"
                                                                    type="number"
                                                                    v-model="semester.hours.extramural[type]">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Итого часов</td>
                                                            <td>
                                                                <span disabled class="form-control ">
                                                                    @{{ getSumHoursForSemester(semester.name, 'fulltime') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span disabled class="form-control">
                                                                    @{{ getSumHoursForSemester(semester.name, 'extramural') }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Форма контроля</td>
                                                            <td>
                                                                <select class="form-control"
                                                                        :name="'semesters[' + semester.name + '][fulltime][control_form]'">

                                                                    <option value="">По умолчанию</option>
                                                                    <option v-for="(value, key) in controlForms"
                                                                            :selected="semester.controlForm.fulltime === key"
                                                                            :value="key">
                                                                        @{{ value }}
                                                                    </option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control"
                                                                        :name="'semesters[' + semester.name + '][extramural][control_form]'">

                                                                    <option value="">По умолчанию</option>
                                                                    <option v-for="(value, key) in controlForms"
                                                                            :selected="semester.controlForm.extramural === key"
                                                                            :value="key">
                                                                        @{{ value }}
                                                                    </option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="sector" class="col-sm-3 control-label">Факультет</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="sector_id" name="sector_id">
                                        @foreach($sectors as $sector)
                                            <option value="{{ $sector['id'] }}" {{ ($sector->id == $discipline->sector_id) ? 'selected' : '' }}>
                                                {{ $sector['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="is_practice" class="col-sm-3 control-label">{{ __('Is a practice') }}</label>
                                <div class="col-sm-9">
                                    <div>
                                        <input id="is_practice" type="checkbox" name="is_practice" {{ !empty($discipline->is_practice) ? 'checked' : null }} />
                                    </div>
                                    <div>
                                    @if(!empty($practice_files))
                                        @foreach($practice_files as $practice_file)
                                            <div class="form">
                                                <div class="col-sm-11">
                                                    <a href="{{ $practice_file->getPublicUrl() }}">
                                                        {{$practice_file->file_name_original}}
                                                    </a>
                                                </div>
                                                <a href="{{ route('disciplineFileDelete', ['id' => $practice_file->id]) }}" class="btn btn-default col-sm-1">
                                                    <i class="md md-delete"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="has_diplomawork" class="col-sm-3 control-label">{{ __('Diploma work') }}</label>
                                <div class="col-sm-9">
                                    <input id="has_diplomawork" type="checkbox" name="has_diplomawork" {{ !empty($discipline->has_diplomawork) ? 'checked' : null }} />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Начало контроля практики в 1 семестре</label>
                                <div class="col-sm-9">
                                    <input type="text" maxlength="5" class="form-control" style="width: 80px;" name="practise_1sem_control_start" value="{{$discipline->practise_1sem_control_start}}"/>
                                    <p class="help-block">Пример для 1 декабря - "01.12"</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Конец контроля практики в 1 семестре</label>
                                <div class="col-sm-9">
                                    <input type="text" maxlength="5" class="form-control" style="width: 80px;" name="practise_1sem_control_end" value="{{$discipline->practise_1sem_control_end}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Начало контроля практики во 2 семестре</label>
                                <div class="col-sm-9">
                                    <input type="text" maxlength="5" class="form-control" style="width: 80px;" name="practise_2sem_control_start" value="{{$discipline->practise_2sem_control_start}}"/>
                                    <p class="help-block">Пример для 1 мая - "01.05"</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Конец контроля практики во 2 семестре</label>
                                <div class="col-sm-9">
                                    <input type="text" maxlength="5" class="form-control" style="width: 80px;" name="practise_2sem_control_end" value="{{$discipline->practise_2sem_control_end}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="verbal_sro" class="col-sm-3 control-label">Устная СРО</label>
                                <div class="col-sm-9">
                                    {{Form::hidden('verbal_sro', 0)}}
                                    <input id="verbal_sro" type="checkbox" name="verbal_sro" value="1" {{ !empty($discipline->verbal_sro) ? 'checked' : null }} />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Описание (ru)</label>
                                <div class="col-sm-9">
                                    <textarea type="text" name="description" class="form-control"
                                              rows="4">{{ isset($discipline->description) ? $discipline->description : null }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Описание (kz)</label>
                                <div class="col-sm-9">
                                    <textarea type="text" name="description_kz" class="form-control"
                                              rows="4">{{ isset($discipline->description_kz) ? $discipline->description_kz : null }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Описание (en)</label>
                                <div class="col-sm-9">
                                    <textarea type="text" name="description_en" class="form-control"
                                              rows="4">{{ isset($discipline->description_en) ? $discipline->description_en : null }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tests_lang_invert" class="col-sm-3 control-label">Использовать второй язык для тестов</label>
                                <div class="col-sm-9">
                                    {{Form::hidden('tests_lang_invert', 0)}}
                                    <input type="checkbox" id="tests_lang_invert" name="tests_lang_invert" value="1" @if(!empty($discipline->tests_lang_invert)) checked @endif>
                                </div>
                            </div>

                        </div>

                        <hr>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-sm-9 ">
                                <button type="submit" class="btn btn-primary btn-lg">{{ isset($discipline->name) ? 'Сохранить' : 'Добавить' }}</button>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('admin_assets/js/selectize.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        const app = new Vue({
            el: '#main',
            data: {
                semester: null,
                semesters: [],
                hoursTypes: {
                    lecture: 'Лекционные',
                    practice: 'Практические',
                    lab: 'Лабораторные',
                    sro: 'СРО',
                    srop: 'СРОП',
                },
                controlForms: {
                    test: 'Тест',
                    traditional: 'Традиционный',
                    report: 'Отчет',
                    score: 'Диф. зачет',
                    protect: 'Защита',
                },
            },
            computed: {
                hoursSumFulltime(){
                    return this.getSumHoursAllSemestersForStudyForm('fulltime');
                },
                hoursSumExtramural(){
                    return this.getSumHoursAllSemestersForStudyForm('extramural');
                }
            },
            methods: {
                getSumHoursAllSemestersForStudyForm(studyForm){
                    let hoursSun = 0;
                    this.semesters.map(({hours}) => {
                        for(let hoursType in hours[studyForm]){
                            hoursSun += +hours[studyForm][hoursType];
                        }
                    });
                    return hoursSun;
                },
                getSumHoursForSemester(name, type){
                    let sum = 0;
                    const semester = this.semesters.find(semester => {
                        return semester.name === name;
                    });
                    for(let time in semester.hours[type]){
                        sum += +semester.hours[type][time];
                    }
                    return sum;
                },
                getSemesterId(){
                    if (this.semesters.length < 1 ){
                        return 1;
                    } else {
                        return this.semesters[this.semesters.length-1].id + 1;
                    }
                },
                addSemester(){
                    const findSemester = this.semesters.find(e => e.name === this.semester);

                    if(this.semester === null)
                        return Swal.fire('Выберете семестр!');

                    if(findSemester){
                        Swal.fire('Этот семестр уже добавлен!')

                    } else {
                        const semester = {
                            name: this.semester,
                            hours: {
                                fulltime: {
                                    lecture: 0,
                                    practice: 0,
                                    lab: 0,
                                    sro: 0,
                                    srop: 0,
                                    controlForm: null
                                },
                                extramural: {
                                    lecture: 0,
                                    practice: 0,
                                    lab: 0,
                                    sro: 0,
                                    srop: 0,
                                    controlForm: null
                                }
                            },
                            controlForm: {
                                fulltime: null,
                                extramural: null
                            }
                        }
                        this.semesters.push(semester)
                    }
                },
                deleteSemester(semester){
                    Swal.fire({
                        title: 'Удалить семестр?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Удалить'
                    }).then((result) => {
                        if (result.value) {
                            if ({{isset($discipline->id) ? 'true': 'false'}}){
                                axios.post('{{route('adminDeleteDisciplineSemester', ['disciplineId' => $discipline->id, 'semester' => ''])}}/' + semester)
                                    .then(() => {
                                        this.semesters = this.semesters.filter(e => e.name !== semester)
                                    })
                            } else {
                                this.semesters = this.semesters.filter(e => e.name !== semester)
                            }
                        }
                    })
                },
                getAllSemesters(){
                    axios.post('{{route('adminDisciplineSemesters', ['id' => $discipline->id])}}')
                        .then(({data}) => {
                            this.semesters = data;
                        })
                }
            },
            mounted(){
                if ({{isset($discipline->id) ? 'true': 'false'}}){
                    this.getAllSemesters();
                }
            }
        })
        $("#serviceType").change(function () {
            var placesList = $('#placesList');

            if ($(this).find('select').val() == 'Master') placesList.show(150);
            else placesList.hide(150);
        });
        $('#select-dependence').selectize({});
        $('#select-dependence2').selectize({});
        $('#select-dependence3').selectize({});
        $('#select-dependence4').selectize({});
        $('#select-dependence5').selectize({});
    </script>
@endsection
