@extends("admin.admin_app")

@section('title', 'Дисциплины')

@section("content")
    <div id="main">
        <div class="page-header">
            @if(\App\Services\Auth::user()->hasRight('disciplines','create'))
                <div class="pull-right">
                    <a href="{{ route('disciplineAdd') }}" class="btn btn-primary">Добавить дисциплину <i class="fa fa-plus"></i></a>
                </div>
            @endif

            <h2>Дисциплины</h2>
        </div>

        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        @if(Session::has('error_message'))
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            {{ Session::get('error_message') }}
        </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table id="data-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Кредиты</th>
                        <th>Статус расчета</th>
                        <th class="text-center width-150">Действие</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        const HAS_READ_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('themes', 'read'), true) }};
        const HAS_EDIT_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('disciplines', 'edit'), true) }};
        const HAS_DELETE_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('disciplines', 'delete'), true) }};

        $(document).ready(function () {
            let dataTable = $('#data-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminDisciplineListAjax') }}", // json datasource
                    type: "post",  // method  , by default get
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            if (json.data[i][2] == null) {
                                json.data[i][2] = 0;
                            }
                            else {
                                json.data[i][2] += '&nbsp;<sub>ECTS</sub>';
                            }

                            json.data[i][4] = '<div class="btn-group">';
                            if (HAS_READ_RIGHT) {
                                json.data[i][4] += '<a class="btn btn-default" href="/disciplines/' + json.data[i][0] + '/themes"><i class="md md-blur-circular"></i></a>';
                            }
                            if (HAS_EDIT_RIGHT) {
                                json.data[i][4] += '<a class="btn btn-default" href="/disciplines/edit/' + json.data[i][0] + '" title="Редактировать"><i class="md md-edit"></i></a>';
                            }
                            if (HAS_DELETE_RIGHT) {
                                json.data[i][4] += '<button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="/disciplines/edit/delete/' + json.data[i][0] + '"><i class="md md-delete"></i>Удалить</a></li></ul>';
                            }

                            json.data[i][4] += '</div>';
                        }

                        return json.data;
                    },
                },
                "createdRow": function (row, data, dataIndex) {
                    if (data[3] == '{{ \App\Discipline::RECALCULATION_STATUS_OK }}') {
                        $(row).addClass("success");
                    } else {
                        $(row).addClass("danger");
                    }
                }
            });
        });
    </script>
@endsection