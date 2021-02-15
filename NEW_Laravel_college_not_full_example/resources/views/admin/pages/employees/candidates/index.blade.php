@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Кандидаты</h2>
                </div> 
            </div>
        </div>

        @if (count($errors) > 0)
            <div class="margin-top alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }} <br>
                        </li>
                    @endforeach
                </ul> 
            </div>
        @endif

        <div class="panel panel-default panel-shadow margin-top">
            <div class="panel-body">
                <table id="employeesDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>ФИО</th>
                            <th>Должность</th>
                            <th>Статус</th>
                            <th class="text-center width-100">Действие</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="inputW-50">Id</th>
                            <th class="inputW-300">ФИО</th>
                            <th class="inputW-150">Должность</th>
                            <th class="inputW-150">Статус</th>
                            <th class="inputW-150">Действие</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-12 margin-b20" id="buttons">
                <button class="btn btn-info" onclick="showOrderModal()">Добавить кандидатов в приказ</button>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- Modals -->
        <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                {!! Form::open([
                    'id' => 'employeesToOrderForm'
                ]) !!}
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Добавление в приказ</h4>
                        </div>
                        <div class="modal-body">
                            <select class="form-control filter-select" name="order_id" data-live-search="true">
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}">
                                        {{ $order->orderName->name.'. №: '.$order->number.'. Дата: '.$order->order_date }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" v-on:click="addUsersToOrder" class="btn btn-primary">Добавить кандидатов в приказ</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>

        <div class="modal fade" id="verdictModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open([
                        'url' => route('employees.verdict.candidate.interview'),
                        'id' => 'verdictForm'
                    ]) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Причина</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="verdictFormResumeId" name="resume_id" value="">
                            <input type="hidden" id="verdictFormVerdict" name="verdict" value="">
                            <div class="form-group">
                                <label>Причина:</label>
                                <textarea class="form-control" name="reason" placeholder="Опишите причину" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Подтвердить</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    var main = new Vue({
        el: '#main',
        methods: {
            addUsersToOrder: function(event){
                let formData = new FormData(document.forms.employeesToOrderForm);
                let userList = getSelectedUser();

                $.each(userList, function(key, value) {
                    formData.append('employees[]', value);
                });

                axios.post('{{ route('add.candidates.to.order') }}', formData)
                    .then(response => { 
                        if(response.data.status == 'success'){
                            $('#orderModal').modal('hide');
                            $.each($("input[name='selectUserList']:checked"), function () {
                                $(this).removeAttr('checked');
                            });
                            Swal.fire({
                                title: 'Done!',
                                text: 'Сотрудники добавлены в приказ успешно!',
                                icon: 'success',
                                confirmButtonText: 'Закрыть'
                            }).then(confirmButtonText => {});
                        }
                    });
            }
        }
    });

    $(document).ready(function(){
        let dataTable = $('#employeesDatatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('employees.candidate.datatable') }}",
                type: "post",
            },
            columns: [
                { data: 'id', width: "25px" },
                { data: 'name', width: "200px" },
                { data: 'position', width: "100px" },
                { data: 'status', width: "100px" },
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

        $('table').on('click', '.verdictResume', function(){
            var id = $(this).attr('data-resume-id');
            var verdict = $(this).attr('data-verdict');
            $('#verdictModal #verdictFormResumeId').val(id);
            $('#verdictModal #verdictFormVerdict').val(verdict);

            if(verdict == 'declined'){
                $('#verdictModal').modal('show');
            } else {
                $('#verdictForm').submit();
            }
            
        });
    });

    function showOrderModal() {
        let userList = getSelectedUser();

        if (userList.length == 0) {
            Swal.fire({
                title: 'Oops!',
                text: 'Необходимо выбрать сотрудников',
                icon: 'warning',
                confirmButtonText: 'Закрыть'
            }).then(confirmButtonText => {});
            return;
        }

        $('#orderModal').modal('show');
    }

    function getSelectedUser() {
        let favorite = [];

        $.each($("input[name='selectUserList']:checked"), function () {
            favorite.push($(this).val());
        });

        return favorite;
    }
</script>
@endsection