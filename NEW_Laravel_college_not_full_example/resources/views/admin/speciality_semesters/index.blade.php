@extends("admin.admin_app")

@section("title", "Спец. семестры")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Спец. семестры</h2>
        </div>

        {{--  Tabs      --}}
        <ul class="nav nav-tabs nav-justified">
            <li>
                <a href="#specTab" data-toggle="tab" class="active show">Спец. семестры</a>
            </li>
            <li>
                <a href="#mainTab" data-toggle="tab">По умолчанию</a>
            </li>
        </ul>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="specTab">
                        <table id="main-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th class="text-center width-300">
                                    Специальность
                                    <select class="form-control" id="speciality_select">
                                        <option value=""></option>
                                        @foreach($specialities as $specialityId => $speciality)
                                            <option value="{{$specialityId}}">{{$speciality}}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center width-200">
                                    Базовое обр-е
                                    <select class="form-control" id="base_education_select">
                                        <option value=""></option>
                                        @foreach($baseEducations as $baseEducationId => $baseEducation)
                                            <option value="{{$baseEducationId}}">{{$baseEducation}}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center width-300">
                                    Форма обучения
                                    <select class="form-control" id="study_form_select">
                                        <option value=""></option>
                                        @foreach($studyForms as $studyFormId => $studyForm)
                                            <option value="{{$studyFormId}}">{{$studyForm}}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center width-200">
                                    Срок
                                    <select class="form-control" id="type_select">
                                        <option value=""></option>
                                        @foreach($types as $typeId => $type)
                                            <option value="{{$typeId}}">{{$type}}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center width-50">
                                    Семестр
                                    <select class="form-control" id="semester_select">
                                        <option value=""></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </th>
                                <th class="text-center width-150">От</th>
                                <th class="text-center width-150">До</th>
                                <th>Изм.</th>
                            </tr>
                            </thead>
                        </table>

                        <div class="col-md-12" id="buttons">
                            <hr>
                            <h2>Добавить/изменить</h2>
                            <div style="width: 500px;">
                                <div class="form-group">
                                    <label for="">Специальность</label>

                                    <multiselect
                                            v-model="speciality_ids"
                                            :options="specialities"
                                            :multiple="true"
                                            :close-on-select="false"
                                            :clear-on-select="false"
                                            :preserve-search="true"
                                            placeholder=""
                                            label="name"
                                            track-by="id"
                                            :preselect-first="false"
                                    ></multiselect>
                                </div>

                                <div class="form-group">
                                    <label for="">Базовое образование</label>
                                    <select class="form-control" v-model="base_education">
                                        <option value=""></option>
                                        <option value="all">- все -</option>
                                        @foreach($baseEducations as $bKey => $baseEducation)
                                            <option value="{{$bKey}}">{{$baseEducation}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="">Форма обучения</label>
                                    <select class="form-control" v-model="study_form">
                                        <option value=""></option>
                                        <option value="all">- все -</option>
                                        @foreach($studyForms as $eKey => $educationForm)
                                            <option value="{{$eKey}}">{{$educationForm}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="">Срок</label>
                                    <select class="form-control" v-model="type">
                                        <option value=""></option>
                                        @foreach($types as $pKey => $type)
                                            <option value="{{$pKey}}">{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="">Семестр</label>
                                    <select class="form-control" style="width:70px;" v-model="semester">
                                        <option value=""></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="">От</label>
                                    <input type="date" value="" class="form-control text-right" style="width: 150px;" v-model="from">
                                </div>

                                <div class="form-group">
                                    <label for="">До</label>
                                    <input type="date" value="" class="form-control text-right" style="width: 150px;" v-model="to">
                                </div>

                                <button class="btn btn-primary btn-lg" v-on:click="save" :disabled="sending">Сохранить</button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="mainTab">
                        <table id="default-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th class="text-center width-300">
                                    Форма обучения
                                    <select class="form-control" id="def_study_form_select">
                                        <option value=""></option>
                                        @foreach($studyForms as $studyFormId => $studyForm)
                                            <option value="{{$studyFormId}}">{{$studyForm}}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center width-200">
                                    Срок
                                    <select class="form-control" id="def_type_select">
                                        <option value=""></option>
                                        <option value="{{\App\Semester::TYPE_STUDY}}">Семестр</option>
                                        @foreach($types as $typeId => $type)
                                            <option value="{{$typeId}}">{{$type}}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center width-50">
                                    Семестр
                                    <select class="form-control" id="def_semester_select">
                                        <option value=""></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </th>
                                <th class="text-center width-150">От</th>
                                <th class="text-center width-150">До</th>
                                <th>Изм.</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="editModal" :class="{show:editModalShow}">
            <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" onclick="vueApp.cancelEdit();" :disabled="sending"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Изменить срок</h4>
                    </div>

                    <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;" id="modalInfoBody">
                        <div><strong>Специальность:</strong> <span v-text="editData.speciality"></span></div>
                        <div><strong>Базовое образование:</strong> <span v-text="editData.base_education"></span></div>
                        <div><strong>Форма обучения:</strong> <span v-text="editData.study_form"></span></div>
                        <div><strong>Срок:</strong> <span v-text="editData.type"></span></div>
                        <div><strong>Семестр:</strong> <span v-text="editData.semester"></span></div>

                        <table class="table">
                            <tr>
                                <th></th>
                                <th>Текущие</th>
                                <th>Новые</th>
                            </tr>
                            <tr>
                                <td><strong>От:</strong></td>
                                <td><span v-text="editData.from"></span></td>
                                <td><input type="date" value="" class="form-control text-right" style="width: 150px;" v-model="editData.new_from" :disabled="sending"></td>
                            </tr>
                            <tr>
                                <td><strong>До:</strong></td>
                                <td><span v-text="editData.to"></span></td>
                                <td><input type="date" value="" class="form-control text-right" style="width: 150px;" v-model="editData.new_to" :disabled="sending"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary btn-lg" onclick="vueApp.saveEdit();" :disabled="sending">Сохранить</button>
                        <button class="btn btn-link" onclick="vueApp.cancelEdit();" :disabled="sending">Отмена</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="editDefaultModal" :class="{show:editDefaultModalShow}">
            <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" onclick="vueApp.cancelEditDefault();" :disabled="sending"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Изменить срок по умолчанию</h4>
                    </div>

                    <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;" id="modalInfoBody">
                        <div><strong>Форма обучения:</strong> <span v-text="editDefaultData.study_form"></span></div>
                        <div><strong>Срок:</strong> <span v-text="editDefaultData.type"></span></div>
                        <div><strong>Семестр:</strong> <span v-text="editDefaultData.semester"></span></div>

                        <table class="table">
                            <tr>
                                <th></th>
                                <th>Текущие</th>
                                <th>Новые</th>
                            </tr>
                            <tr>
                                <td><strong>От:</strong></td>
                                <td><span v-text="editDefaultData.from"></span></td>
                                <td><input type="date" value="" class="form-control text-right" style="width: 150px;" v-model="editDefaultData.new_from" :disabled="sending"></td>
                            </tr>
                            <tr>
                                <td><strong>До:</strong></td>
                                <td><span v-text="editDefaultData.to"></span></td>
                                <td><input type="date" value="" class="form-control text-right" style="width: 150px;" v-model="editDefaultData.new_to" :disabled="sending"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary btn-lg" onclick="vueApp.saveDefaultEdit();" :disabled="sending">Сохранить</button>
                        <button class="btn btn-link" onclick="vueApp.cancelEditDefault();" :disabled="sending">Отмена</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.min.js"></script>

    <script type="text/javascript">
        Vue.component('vue-multiselect', window.VueMultiselect.default);

        $(document).ready(function () {
            // Spec Semesters
            window.table = $('#main-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{route('adminSpecialitySemesterAjax')}}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        var id;

                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            id = json.data[i][7];

                            json.data[i][7] = '<div class="btn-group">';
                            json.data[i][7] += '<button class="btn btn-default" onclick="vueApp.edit(' + id + ', \'' + json.data[i][0] + '\', \'' + json.data[i][1] + '\', \'' + json.data[i][2] + '\', \'' + json.data[i][3] + '\', \'' + json.data[i][4] + '\', \'' + json.data[i][5] + '\', \'' + json.data[i][6] + '\')"><i class="md md-edit"></i></button>';
                            json.data[i][7] += '</div>';
                        }

                        return json.data;
                    }
                }
            });

            // Spec. semesters
            $('#speciality_select').on('change', function () {
                table.column(0)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#base_education_select').on('change', function () {
                table.column(1)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#study_form_select').on('change', function () {
                table.column(2)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#type_select').on('change', function () {
                table.column(3)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#semester_select').on('change', function () {
                table.column(4)
                    .search($(this).val(), false, false)
                    .draw();
            });

            window.default_table = $('#default-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{route('adminDefaultSemesterAjax')}}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        var id;

                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            id = json.data[i][5];

                            json.data[i][5] = '<div class="btn-group">';
                            json.data[i][5] += '<button class="btn btn-default" onclick="vueApp.edit_default(' + id + ', \'' + json.data[i][0] + '\', \'' + json.data[i][1] + '\', \'' + json.data[i][2] + '\', \'' + json.data[i][3] + '\', \'' + json.data[i][4] + '\')"><i class="md md-edit"></i></button>';
                            json.data[i][5] += '</div>';
                        }

                        return json.data;
                    }
                }
            });

            // default
            $('#def_study_form_select').on('change', function () {
                default_table.column(0)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#def_type_select').on('change', function () {
                default_table.column(1)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#def_semester_select').on('change', function () {
                default_table.column(2)
                    .search($(this).val(), false, false)
                    .draw();
            });
        });

        var vueApp = new Vue({
            components: {
                Multiselect: window.VueMultiselect.default
            },
            el: '#main',
            data: {
                speciality_ids: [],
                specialities: [
                    @foreach($specialities as $specialityId => $speciality)
                        {
                            id : "{{$specialityId}}",
                            name: "{{$speciality}}"
                        },
                    @endforeach
                ],
                study_form: null,
                base_education: null,
                type: null,
                semester: null,
                from: null,
                to: null,
                sending : false,
                editModalShow:false,
                editData : {
                    id : null,
                    speciality : null,
                    base_education : null,
                    study_form : null,
                    type : null,
                    semester : null,
                    from : null,
                    to : null,
                    new_from : null,
                    new_to : null
                },
                editDefaultModalShow:false,
                editDefaultData : {
                    id : null,
                    study_form : null,
                    type : null,
                    semester : null,
                    from : null,
                    to : null,
                    new_from : null,
                    new_to : null
                }
            },
            methods: {
                save: function () {
                    if (!this.speciality_ids || !this.study_form || !this.base_education || !this.type || !this.semester || !this.from || !this.to) {
                        bootbox.alert("Необходимо заполнить все поля");
                        return;
                    }

                    if (Date.parse(this.from) > Date.parse(this.to)) {
                        bootbox.alert("Проверьте даты От и До");
                        return;
                    }

                    this.sending = true;
                    var self = this;

                    var ids = [];

                    $.each(this.speciality_ids, function() {
                        ids.push(this.id);
                    });

                    axios.post('{{route('adminSpecialitySemesterSave')}}', {
                        speciality_ids: ids,
                        study_form: this.study_form,
                        base_education: this.base_education,
                        type: this.type,
                        semester: this.semester,
                        from: this.from,
                        to: this.to
                    })
                        .then(function (response) {
                            if (response.data.success) {
                                $('#main-table-ajax').DataTable().ajax.reload();
                            } else {
                                bootbox.alert(response.data.error);
                            }

                            self.sending = false;
                        });
                },
                edit : function(id, speciality, base_education, study_form, type, semester, from, to) {
                    this.editData.id = id;
                    this.editData.speciality = speciality;
                    this.editData.base_education = base_education;
                    this.editData.study_form = study_form;
                    this.editData.type = type;
                    this.editData.semester = semester;
                    this.editData.from = from;
                    this.editData.to = to;
                    this.editData.new_from = this.dateToISO(from);
                    this.editData.new_to = this.dateToISO(to);

                    this.editModalShow = true;
                },
                cancelEdit: function() {
                    this.editModalShow = false;
                },
                dateToISO(date) {
                    var array = date.split('.');
                    return array[2] + '-' + array[1] + '-' + array[0];
                },
                saveEdit : function() {
                    this.sending = true;
                    var self = this;

                    if (Date.parse(this.editData.new_from) > Date.parse(this.editData.new_to)) {
                        bootbox.alert("Проверьте даты От и До");
                        this.sending = false;
                        return;
                    }

                    axios.post('{{route('adminSpecialitySemesterEdit')}}', {
                        id: this.editData.id,
                        from: this.editData.new_from,
                        to: this.editData.new_to
                    })
                        .then(function (response) {
                            if (response.data.success) {
                                self.cancelEdit();
                                $('#main-table-ajax').DataTable().ajax.reload();
                            } else {
                                bootbox.alert(response.data.error);
                            }

                            self.sending = false;
                        });
                },
                edit_default : function(id, study_form, type, semester, from, to) {
                    this.editDefaultData.id = id;
                    this.editDefaultData.study_form = study_form;
                    this.editDefaultData.type = type;
                    this.editDefaultData.semester = semester;
                    this.editDefaultData.from = from;
                    this.editDefaultData.to = to;
                    this.editDefaultData.new_from = this.dateToISO(from);
                    this.editDefaultData.new_to = this.dateToISO(to);

                    this.editDefaultModalShow = true;
                },
                cancelEditDefault: function() {
                    this.editDefaultModalShow = false;
                },
                saveDefaultEdit : function() {
                    this.sending = true;
                    var self = this;

                    if (Date.parse(this.editDefaultData.new_from) > Date.parse(this.editDefaultData.new_to)) {
                        bootbox.alert("Проверьте даты От и До");
                        this.sending = false;
                        return;
                    }

                    axios.post('{{route('adminDefaultSemesterEdit')}}', {
                        id: this.editDefaultData.id,
                        from: this.editDefaultData.new_from,
                        to: this.editDefaultData.new_to
                    })
                        .then(function (response) {
                            if (response.data.success) {
                                self.cancelEditDefault();
                                $('#default-table-ajax').DataTable().ajax.reload();
                            } else {
                                bootbox.alert(response.data.error);
                            }

                            self.sending = false;
                        });
                }
            }
        });
    </script>
@endsection
