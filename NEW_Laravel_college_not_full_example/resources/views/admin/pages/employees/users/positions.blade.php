@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Должности Сотрудника - {{ $user->name == '' ? $user->fio : $user->name }}</h2>
                </div>
            </div>
        </div>

        <div class="panel panel-default panel-shadow margin-top">
            <div class="panel-body">
                <table id="userPositionsDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Должность</th>
                            <th>График работы</th>
                            <th>Тип занятости</th>
                            <th>Ставка</th>
                            <th>Оклад</th>
                            <th>Организация</th>
                            <th>Тип расчёта з\п</th>
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
    $(document).ready(function(){
        let dataTable = $('#userPositionsDatatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: {{ $user->id }}
                },
                url: "{{ route('employees.user.positions.datatable') }}",
                type: "post",
            },
            columns: [
                { data: 'id', width: "25px" },
                { data: 'position_id', width: "100px" },
                { data: 'schedule', width: "100px" },
                { data: 'employment', width: "100px" },
                { data: 'price', width: "100px" },
                { data: 'salary', width: "100px" },
                { data: 'organization', width: "100px" },
                { data: 'payroll_type', width: "100px" },
                { data: 'action', width: "100px" }
            ],
            "drawCallback": function( settings ) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            initComplete: function () {
                $('[data-toggle="tooltip"]').tooltip();
                this.api().columns().every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    $(input).appendTo($(column.footer()).empty())
                    .on('change', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });
                });
            }
        });
    });
</script>
@endsection