@extends("admin.admin_app")

@section('title', 'Апелляции')

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Апелляции</h2>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table id="data-table-ajax" class="table table-striped table-hover dt-responsive">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>ФИО</th>
                        <th>Специальность</th>
                        <th>Базовое обр-е</th>
                        <th>Форма обучения</th>
                        <th>Дисциплина</th>
                        <th>Вид контроля</th>
                        <th>Дата, время сдачи</th>
                        <th>Результат </th>
                        <th>Статус</th>
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
        $(document).ready(function() {
            let dataTable = $('#data-table-ajax').DataTable({
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
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": false}
                ],
                "ajax":{
                    url :"{{route('adminAppealListAjax')}}",
                    type: "post",
                    error: function(){
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display","none");
                    }
                }
            });
        });
    </script>
@endsection