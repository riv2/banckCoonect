@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">

            @if(\App\Services\Auth::user()->hasRight('modules','create'))
                <div class="pull-right">
                    <a href="{{ route('adminModuleEdit', ['id' => 'new']) }}" class="btn btn-primary">Добавить модуль <i class="fa fa-plus"></i></a>
                </div>
            @endif

            <h2>Модули</h2>
        </div>

        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table id="main-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th style="width: 150px;">
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <script type="text/javascript">
        const HAS_EDIT_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('modules', 'edit'), true) }};
        const HAS_DELETE_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('modules', 'delete'), true) }};

        $(document).ready(function() {
            let table = $('#main-table').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminModuleListAjax') }}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            json.data[i][2] = '<div class="btn-group">';
                            if (HAS_EDIT_RIGHT) {
                                json.data[i][2] += '<a class="btn btn-default" href="/modules/edit/' + json.data[i][0] + '" title="Редактировать"><i class="md md-edit"></i></a>';
                            }

                            if (HAS_DELETE_RIGHT) {
                                json.data[i][2] += '<button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="/modules/delete/' + json.data[i][0] + '"><i class="md md-delete"></i> Удалить</a></li></ul>';
                            }

                            json.data[i][2] += '</div>';
                        }

                        return json.data;
                    }
                }
            });
        });
    </script>
@endsection