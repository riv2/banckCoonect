@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Приказы</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default panel-shadow margin-top">
                    <div class="panel-body">
                        <table id="userDecreeDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Имя</th>
                                    <th>Приказ</th>
                                    <th>Дата</th>
                                    <th>Подпись</th>
                                    <th class="text-center width-100">Действие</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="inputW-50">Id</th>
                                    <th class="inputW-300">Имя</th>
                                    <th class="inputW-200">Приказ</th>
                                    <th class="inputW-150">Дата</th>
                                    <th class="inputW-150">Подпись</th>
                                    <th class="inputW-50">Действие</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        var id = $('#user_id').val();
        let dataTable = $('#userDecreeDatatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('employees.users.decree.datatable') }}",
                type: "post",
            },
            columns: [
                { data: 'id', width: "25px" },
                { data: 'user_name', width: "150px" },
                { data: 'decree', width: "150px" },
                { data: 'created_at', width: "100px" },
                { data: 'is_signed', width: "50px" },
                { data: 'action', width: "50px" }
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