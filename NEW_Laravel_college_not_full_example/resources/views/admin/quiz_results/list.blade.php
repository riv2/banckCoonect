@extends("admin.admin_app")

@section("title", "Результаты тестирования")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Результаты тестирования</h2>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table id="main-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
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
                        <th>Дисциплина</th>
                        <th>
                            Вид контроля
                            <select class="form-control" id="type_select">
                                <option value=""></option>
                                @foreach($types as $typeId => $type)
                                    <option value="{{$typeId}}">{{$type}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>Дата, время сдачи</th>
                        <th>Результат</th>
                        <th></th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            let table = $('#main-table-ajax').DataTable({
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
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{route('adminQuizResultsListAjax')}}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            json.data[i][10] = '<div class="btn-group">';
                            json.data[i][10] += '<a class="btn btn-default" href="/quiz_results/view/' + json.data[i][0] + '" title="Просмотр"><i class="glyphicon glyphicon-info-sign"></i></a>';
                            json.data[i][10] += '</div>';
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

            $('#type_select').on('change', function () {
                table.column(7)
                    .search($(this).val(), false, false)
                    .draw();
            });
        });
    </script>
@endsection
