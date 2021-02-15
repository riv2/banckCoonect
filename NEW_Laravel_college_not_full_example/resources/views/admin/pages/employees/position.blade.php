@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-9 col-xs-8">
                    <h2>Должности</h2>
                </div> 
                <div class="col-md-3 col-xs-4 text-right">
                    <button class="margin-top btn btn-primary btn-lg" id="createPosition">
                        Создать Должность
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 text-right">
                <label class="help-block" >Фильтр по имени: </label>
            </div>
            <div class="col-md-3 text-left">
                <input class="form-control" type="text" id="datatableFilterName">
            </div>
            <div class="col-md-3 text-right">
                <label class="help-block" >Фильтр по имени Отдела: </label>
            </div>
            <div class="col-md-3 text-left">
                <input class="form-control" type="text" id="datatableFilterSuperviser">
            </div>
        </div>
        <div class="panel panel-default panel-shadow margin-top">
            <div class="panel-body">
                <table id="positionDatatable" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Имя</th>
                        <th>Описание</th>
                        <th>Курирующий Отдел</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="positionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Должность</h4>
                    </div>
                    <div class="modal-body">
                        <form id="departmentForm">
                            @csrf
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="managerPosition" value="true">
                                <label class="form-check-label" for="defaultCheck1">
                                    Руководящая должность
                                </label>
                            </div>
                            <input type="hidden" name="id" id="id">
                            <div class="form-group margin-top">
                                <label>Имя Должности:</label>
                                <input class="form-control" type="text" name="name" placeholder="Имя Должности">
                            </div>
                            <div class="form-group margin-top">
                                <label>Описание Должности:</label>
                                <textarea class="form-control" type="text" name="description" placeholder="Описание:"></textarea>
                            </div>
                            <div class="form-group margin-top">
                                <label>Отдел:</label>
                                <select class="form-control" id="positionDepartment" name="department_id">
                                    <option value="">Выберите отдел</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" selected>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group margin-top">
                                <label>Роли:</label>
                                <select class="form-control" id="positionRoles" name="roles[]" data-live-search="true" multiple>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->title_ru }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="modalClose">Закрыть</button>
                        <button type="button" v-on:click="createPosition" class="btn btn-primary" id="addNewPosition" data-new="true">Сохранить Должность</button>
                    </div>
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
                createPosition: function (event) {
                    let url = '';
                    let formData = new FormData(document.forms.departmentForm);

                    $('#departmentForm input').removeClass('is-invalid');
                    $('#departmentForm input').removeClass('is-valid');
                    $('#departmentForm .invalid-feedback').remove();

                    if(event.currentTarget.getAttribute('data-new') == 'true'){
                        url = '{{ route('addNewPosition') }}';
                    }else{
                        url = '{{ route('editPosition') }}';
                    }

                    axios.post(url, formData)
                       .then(response => { 
                            if(response.data.status == 'success'){
                                $('#positionModal').modal('hide');
                                Swal.fire({
                                    title: 'Done!',
                                    text: 'Должность сохранена',
                                    icon: 'success',
                                    confirmButtonText: 'Закрыть'
                                }).then(confirmButtonText => {
                                    dataTable.draw();
                                });
                            }
                        })
                        .catch(error => {
                            $.each(error.response.data.errors, function(field, errors) {
                                $.each(errors, function(index, error){
                                    $('[name="' + field + '"]').after('<div class="invalid-feedback">' + error + '</div>');
                                });

                                $('[name="' + field + '"]').addClass('is-invalid');
                            });
                        });
                }
            }
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#positionRoles').selectpicker({
                    dropupAuto: false
            });

            $('#createPosition').click(function(){
                $('#positionModal').modal('show');
                $('#positionModal .modal-body input').val('');
                $('#positionModal .modal-body option').removeAttr('selected');
                $('#positionModal .modal-body textarea').val('');
                $('#positionModal .modal-footer #addNewPosition').attr('data-new', 'true');
            });

            $('.table').on('click', '.editPosition', function(){
                let id = $(this).attr('data-position-id');
                $.ajax({
                    url: '{{ route('getPosition') }}',
                    data: { id: id},
                    type: 'POST',
                    success: function(data){
                        $('#positionModal .modal-body option').removeAttr('selected');
                        $('#positionModal').modal('show');
                        $('#positionModal .modal-body input[name="name"]').val(data.name);
                        $("#positionModal .modal-body select#positionDepartment").val(data.department_id);
                        $("#positionModal .modal-body select#positionRoles").selectpicker('val', data.roles_ids);
                        $('#positionModal .modal-body input#id').val(data.id);
                        $('#positionModal .modal-body textarea').val(data.description);
                        $('#positionModal .modal-footer #addNewPosition').attr('data-new', 'false');
                        if(data.managerial == 1){
                            $('#positionModal .modal-body input[name="managerPosition"]').prop('checked', true);
                        }
                    }
                });
            });

            var dataTable = $('#positionDatatable').DataTable({
                "initComplete": function(settings, json) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('positionDatatable') }}",
                    type: "post",
                },
                columns: [
                    { data: 'id'},
                    { data: 'name'},
                    { data: 'description'},
                    { data: 'department_id'},
                    { data: 'action'}
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('#datatableFilterName').change(function(){
                dataTable
                    .columns(1)
                    .search($(this).val())
                    .draw();
            });

            $('#datatableFilterSuperviser').change(function(){
                dataTable
                    .columns(3)
                    .search($(this).val())
                    .draw();
            });
        });
    </script>
@endsection                 