@extends("admin.admin_app")

@section('title', __('Users'))

@section("content")
    <div id="main">
        <div class="page-header">

        	@if(\App\Services\Auth::user()->hasRight('users','create'))
            <!--<div class="pull-right">
			<a href="{{ route('userAdd') }}" class="btn btn-primary">Добавить пользователя <i class="fa fa-plus"></i></a>
			</div>-->
            @endif

            <h2>Пользователи</h2>
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
                        <th>Фото</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Телефон</th>
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
        const HAS_EDIT_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('users', 'edit'), true) }};
        const HAS_DELETE_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('users', 'delete'), true) }};

        $(document).ready(function () {
            let dataTable = $('#data-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminUserListAjax') }}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            if (json.data[i][1] != null) {
                                json.data[i][1] = '<img src="/upload/members/' + json.data[i][1] + '-s.jpg" width="80" alt="person">';
                            }

                            json.data[i][5] = '<div class="btn-group">';
                            if (HAS_EDIT_RIGHT) {
                                json.data[i][5] += '<a class="btn btn-default" href="/users/edit/' + json.data[i][0] + '" title="Редактировать"><i class="md md-edit"></i></a>';
                            }

                            if (HAS_DELETE_RIGHT) {
                                json.data[i][5] += '<button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="/users/delete/' + json.data[i][0] + '"><i class="md md-delete"></i> Удалить</a></li></ul>';
                            }

                            json.data[i][5] += '</div>';
                        }

                        return json.data;
                    }
                }
            });
        });
    </script>
@endsection