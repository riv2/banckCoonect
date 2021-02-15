@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Гости</h2>
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
                <table id="data-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Телефон</th>
                        <th>Дата регистрации</th>
                        <th>ФИО</th>
                        <th class="text-center width-100">Действие</th>
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
        const HAS_DELETE_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('guests', 'delete'), true) }};

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
                    url: "{{ route('adminGuestListAjax') }}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {

                            json.data[i][4] = '<div class="btn-group">';

                            if (HAS_DELETE_RIGHT) {
                                json.data[i][4] += '<button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="/guests/delete/' + json.data[i][0] + '"><i class="md md-delete"></i> Удалить</a></li></ul>';
                            }

                            json.data[i][4] += '</div>';
                        }

                        return json.data;
                    }
                }
            });
        });
    </script>
@endsection