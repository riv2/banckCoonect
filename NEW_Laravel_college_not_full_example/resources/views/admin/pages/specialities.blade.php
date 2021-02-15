@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            @if(\App\Services\Auth::user()->hasRight('specialities','create'))
                <div class="pull-right">
                    <a href="{{ route('specialityAdd') }}" class="btn btn-primary">Добавить специальность <i class="fa fa-plus"></i></a>
                </div>
            @endif

            <h2>Образовательные программы</h2>
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
                <table id="main-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th class="text-center width-150">ID</th>
                        <th class="text-center width-150">
                            Код
                            <select class="form-control" id="code_select">
                                <option value=""></option>
                                @foreach($fullCodes as $fullCode)
                                    <option value="{{ $fullCode }}">{{ strtoupper($fullCode) }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>Название
                            <input id="name_filter" class="form-control" type="text" value="" />
                        </th>
                        <th>Направления подготовки</th>
                        <th class="text-center width-150">
                            Год
                            <select class="form-control" id="year_select">
                                <option value=""></option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th class="text-center width-150">Действие</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <script type="text/javascript">
        const HAS_EDIT_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('specialities', 'edit'), true) }};
        const HAS_DELETE_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('specialities', 'delete'), true) }};

        $(document).ready(function () {
            var table = $('#main-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminSpecialityListAjax') }}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (var i = 0, ien = json.data.length ; i < ien ; i++) {
                            json.data[i][5] = '<div class="btn-group">';
                            json.data[i][5] += '<a class="btn btn-default" href="/specialities/edit/' + json.data[i][0] + '" title="Редактировать"><i class="md md-edit"></i></a>';

                            if (HAS_DELETE_RIGHT) {
                                json.data[i][5] += '<button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="/specialities/delete/' + json.data[i][0] + '"><i class="md md-delete"></i> Удалить</a></li></ul>';
                            }

                            json.data[i][5] += '</div>';
                        }

                        return json.data;
                    }
                }
            });

            $('#code_select').on('change', function () {
                table.column(1)
                    .search($(this).val(), false, false)
                    .draw();
            });

            $('#name_filter').on('change', function () {
                if( $('#name_filter').val().length > 0 ){
                    console.log('change name filter');
                    table.column(2)
                        .search($(this).val(), false, false)
                        .draw();
                }
            });

            $('#year_select').on('change', function () {
                table.column(4)
                    .search($(this).val(), false, false)
                    .draw();
            });

        });
    </script>
@endsection
