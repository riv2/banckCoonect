@extends("admin.admin_app")

@section("title", "Учебный план")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Учебный план</h2>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table id="main-table-ajax" class="table table-striped table-hover dt-responsive">
                    <thead>
                    <tr>
                        <th class="text-center width-150">ID</th>
                        <th class="text-center width-150">ФИО</th>
                        <th>
                            Специальность
                            <select class="form-control" id="speciality_select">
                                <option value=""></option>
                                @foreach($specialities as $specialityId => $speciality)
                                    <option value="{{$specialityId}}">{{$speciality}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th class="text-center width-150">
                            Год поступления
                            <select class="form-control" id="year_select">
                                <option value=""></option>
                                @foreach($years as $year)
                                    <option value="{{$year}}">{{$year}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            Базовое обр-е
                            <select class="form-control" id="base_education_select">
                                <option value=""></option>
                                @foreach($baseEducations as $baseEducationId => $baseEducation)
                                    <option value="{{$baseEducationId}}">{{$baseEducation}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            Форма обучения
                            <select class="form-control" id="study_form_select">
                                <option value=""></option>
                                @foreach($studyForms as $studyFormId => $studyForm)
                                    <option value="{{$studyFormId}}">{{$studyForm}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            Язык обучения
                            <select class="form-control" id="lang_select">
                                <option value=""></option>
                                @foreach($langs as $langId => $lang)
                                    <option value="{{$langId}}">{{$lang}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th></th>
                        <th class="text-center width-100" style="padding-right:8px;"><input type="checkbox" onchange="select_all_toggle(this);"></th>
                    </tr>
                    </thead>
                </table>

                <div class="col-md-12" id="buttons">
                    <button class="btn btn-primary btn-lg" v-on:click="getPlan()">Составить план</button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            window.table = $('#main-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{route('adminStudyPlanAjax')}}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        var lastSemester = '';
                        var confirmed;

                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            lastSemester = json.data[i][7];
                            confirmed =  json.data[i][8];

                            json.data[i][7] = '<div class="btn-group">';
                            json.data[i][7] += '<a class="btn btn-default" href="/study_plan/view/' + json.data[i][0] + '" title="Просмотр"><i class="glyphicon glyphicon-info-sign"></i></a> ' + lastSemester + (confirmed ? ' <span class="label label-success">Утверджён</span>' : '');
                            json.data[i][7] += '</div>';

                            json.data[i][8] = '<div style="width:100%; text-align: center">';
                            json.data[i][8] += '<input name="selectUserList" value="' + json.data[i][0] + '" type="checkbox" />';
                            json.data[i][8] += '</div>';
                        }

                        return json.data;
                    }
                }
            });

            $('#speciality_select').on('change', function () {
                table.column(2)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#year_select').on('change', function () {
                table.column(3)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#base_education_select').on('change', function () {
                table.column(4)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#study_form_select').on('change', function () {
                table.column(5)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#lang_select').on('change', function () {
                table.column(6)
                    .search($(this).val(), false, false)
                    .draw();
            });
        });

        function select_all_toggle(obj) {
            $("input[name='selectUserList']").prop('checked', $(obj).prop('checked'))
        }

        function buttonsOff() {
            $('div#buttons button').prop('disabled', true);
        }

        function buttonsOn() {
            $('div#buttons button').prop('disabled', false);
        }

        function getSelectedUser() {
            let favorite = [];

            $.each($("input[name='selectUserList']:checked"), function () {
                favorite.push($(this).val());
            });

            return favorite;
        }

        var vueApp = new Vue({
            el: '#main',
            // data: {
            //
            // },
            methods: {
                getPlan: function () {
                    var users = getSelectedUser();

                    if (users.length == 0) {
                        alert('Необходимо выбрать студентов');
                        return;
                    }

                    buttonsOff();

                    axios.post('{{route('adminStudyPlanMake')}}', {
                        "users": users
                    })
                        .then(function (response) {
                            if (response.data.success) {
                                $('#main-table-ajax').DataTable().ajax.reload();
                            } else {
                                alert(response.data.error);
                            }

                            buttonsOn();
                        });
                }
            }
        });
    </script>
@endsection
