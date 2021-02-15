@extends("admin.admin_app")

@section('title', 'Отмена покупок дисциплин')

@section("content")
    <div id="or_cabinet">

        <div id="main">
            <div class="page-header">
                <h2>Отмена покупок дисциплин</h2>
            </div>
            @if(Session::has('flash_message'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    {{ Session::get('flash_message') }}
                </div>
            @endif

            <div id="alert-danger" class="alert alert-danger hide">
                <div id="alert-danger-message"></div>
            </div>

            <div id="alert-success" class="alert alert-success hide">
                <div id="alert-success-message"></div>
            </div>

            <div class="panel panel-default panel-shadow">
                <div class="panel-body">

                    <div class="tab-content">
                        <div class="col-md-12 no-padding">
                            <table id="data-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ФИО</th>
                                    <th>Дисциплина</th>
                                    <th>Дата</th>
                                    <th>Статус</th>
                                    <th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#notificationText').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                ],
                height: {
                    type: String,
                    default: '150'
                }
            });
            $('#notificationText').on('summernote.change', function (e) {
                $('#notificationText').val($('#notificationText').summernote().code());
            });

            $('#notificationText').on('summernote.blur', function (e) {
                $('#notificationText').val($('#notificationText').summernote().code());
            });

            let dataTable = $('#data-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminDisciplinePayCancelListAjax') }}", // json datasource
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    type: "post",  // method  , by default get
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    }
                }
            });

            $("#data-table-ajax thead th").each(function (i) {
                if (i < 5) {
                    $('<input type="text" class="form-control" />')
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();

                            dataTable.column(i)
                                .search(val ? '^' + $(this).val() + '$' : val, true, false)
                                .draw();
                        });
                }
            });
        });

        function payCancelStatus(orderId, status)
        {
            $.ajax({
                url: '{{ route('adminDisciplinePayCancelChangeStatus') }}',
                type: "POST",
                data: {
                    order_id: orderId,
                    status: status,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data){
                    if(data.status){
                        if(status == 'approve')
                        {
                            $('#btn-approve-' + orderId).css('display', 'none');
                            $('#btn-decline-' + orderId).css('display', 'block');
                            $('#status-' + orderId).html('подтверждено');

                        }
                        if(status == 'decline')
                        {
                            $('#btn-approve-' + orderId).css('display', 'block');
                            $('#btn-decline-' + orderId).css('display', 'none');
                            $('#status-' + orderId).html('отклонено');
                        }
                    }
                },
                dataType: 'json'
            });
        }
    </script>
@endsection