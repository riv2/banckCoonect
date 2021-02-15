@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>Приказы</h2>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('employees.edit.order') }}" class="btn btn-primary margin-t20">Добавить приказ</a>
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
                                    <th>ID</th>
                                    <th>Наименование</th>
                                    <th>Номер</th>
                                    <th>Дата</th>
                                    <th>Статус</th>
                                    <th class="text-center width-150">Действие</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="inputW-50">Id</th>
                                    <th class="inputW-300">Наименование</th>
                                    <th class="inputW-150">Номер</th>
                                    <th class="inputW-150">Дата</th>
                                    <th class="inputW-150">Статус</th>
                                    <th class="inputW-50">Действие</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <!-- Vote Modal -->
        <div class="modal fade" id="voteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open([
                        'url' => route('order.agreement.vote'),
                    ]) !!}
                        <input type="hidden" id="orderIDField" name="order_id">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Согласование</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Решение</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="vote" id="exampleRadios1" value="approved" checked>
                                    <label class="form-check-label" for="exampleRadios1">
                                        Подтвердить
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="vote" id="exampleRadios2" value="declined">
                                    <label class="form-check-label" for="exampleRadios2">
                                        Отклонить
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Комментарий</label>
                                <textarea class="form-control" name="comment" placeholder="Оставьте комментарий к вашему решению" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-primary">Проголосовать</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        let dataTable = $('#userDecreeDatatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('orders.datatable') }}",
                type: "post",
            },
            columns: [
                { data: 'id', width: "25px" },
                { data: 'name', width: "300px" },
                { data: 'number', width: "150px" },
                { data: 'order_date', width: "100px" },
                { data: 'status', width: "100px" },
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

        $(document).ready(function(){
            $('table').on('click', '.openModal', function(){
                var id = $(this).attr('data-order-id');
                $('#voteModal #orderIDField').val(id);
                $('#voteModal').modal('show');
            });
        });
    </script>
@endsection