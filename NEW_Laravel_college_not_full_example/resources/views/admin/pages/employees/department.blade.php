@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Отделы</h2>
                </div> 
                <div class="col-md-2 text-right">
                    <button class="margin-top btn btn-primary btn-lg" id="createDepartments">
                        Создать Отдел
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 text-right">
                <label class="help-block" >Фильтр по имени Отдела: </label>
            </div>
            <div class="col-md-3 text-left">
                <input class="form-control" type="text" id="datatableFilterName">
            </div>
            <div class="col-md-3 text-right">
                <label class="help-block" >Фильтр по имени Курирующего Отдела: </label>
            </div>
            <div class="col-md-3 text-left">
                <input class="form-control" type="text" id="datatableFilterSuperviser">
            </div>
        </div>
        <div class="panel panel-default panel-shadow margin-top">
            <div class="panel-body">
                <table id="departmentDatatable" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
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
    </div>

    <!-- Department Modal -->
    <div class="modal fade" id="departmentsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Отдел</h4>
                </div>
                <div class="modal-body">
                    <form id="departmentForm">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group margin-top">
                            <label>Имя Отдела:</label>
                            <input class="form-control" type="text" name="name" placeholder="Имя Отдела">
                        </div>
                        <div class="form-group margin-top">
                            <label>Описание Отдела:</label>
                            <textarea class="form-control" type="text" name="description" placeholder="Описание:"></textarea>
                        </div>
                        <div class="form-group margin-top">
                            <label>Курирующий Отдел: (опционально)</label>
                            <select id="selectSuperviser" class="form-control" name="superviser">
                                <option desabled="true" value="">Выберите курирующий отдел</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="selecterSearchUsers" style="display: none;">
                            <label>Выберите главу отдела:</label><br>
                            <select 
                                name="manager_position_id" 
                                id="filter_group" 
                                class="form-control filter-select" 
                                data-live-search="true" 
                                data-width="auto"
                            >
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="checkSpec" id="sectorCheck" value="on">
                            <label class="form-check-label" for="file">
                                Сектор
                            </label>
                        </div>
                        <div id="sectorBlock" style="display: none;">
                            <div class="form-group">
                                <label class="btn-block">Выберите специальность бакалавриат</label>
                                <select 
                                    id="speciality_bc_select" 
                                    class="form-control speciality-select" 
                                    data-live-search="true" 
                                    name="speciality_bc[]" 
                                    multiple
                                >
                                    @foreach($specialities as $speciality)
                                        <option 
                                            value="{{ $speciality->id }}"
                                            @if(in_array($speciality->id, $selectedSpecialities)) {{ 'disabled' }} @endif
                                        >
                                            {{ $speciality->code_number.$speciality->code_char.$speciality->code.' '.$speciality->year.' - '.$speciality->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="btn-block">Выберите специальность магистратура</label>
                                <select 
                                    id="speciality_mg_select" 
                                    class="form-control speciality-select" 
                                    data-live-search="true" 
                                    name="speciality_mg[]" 
                                    multiple
                                >
                                    @foreach($specialities as $speciality)
                                        <option 
                                            value="{{ $speciality->id }}"
                                            @if(in_array($speciality->id, $selectedSpecialities)) {{ 'disabled' }} @endif
                                        >
                                            {{ $speciality->code_number.$speciality->code_char.$speciality->code.' '.$speciality->year.' - '.$speciality->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="modalClose">Закрыть</button>
                    <button type="button" class="btn btn-primary" id="addNewDepartment" data-new="true">Сохранить отдел</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            var selectIds = {!! json_encode($selectedSpecialities) !!};

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#sectorCheck').change(function () {
                $('#sectorBlock').fadeToggle();
                $('.speciality-select').selectpicker({
                    dropupAuto: false
                });
            });

            $('#createDepartments').click(function(){
                $('#departmentsModal').modal('show');
                $('#selecterSearchUsers').hide();
                $('#departmentsModal .modal-body input').val('');
                $('#departmentsModal .modal-body textarea').val('');
                $('#departmentsModal .modal-footer #addNewDepartment').attr('data-new', 'true');
                $('#sectorCheck').attr('checked', false);
                $('#sectorBlock').hide();
                $('.speciality-select option').removeAttr('selected');
                $('.speciality-select').selectpicker('refresh');
            });

            $('.table').on('click', '.editDepartment', function(){
                let id = $(this).attr('data-department-id');
                $('#departmentsModal #filter_group').empty();

                $.ajax({
                    url: '{{ route('getDepartment') }}',
                    data: { id: id},
                    type: 'POST',
                    success: function(data){
                        $.each(selectIds, function(key, val){
                            $('#speciality_bc_select option[value="'+val+'"]').removeAttr('selected').attr('disabled', true);
                        });
                        
                        $.each(selectIds, function(key, val){
                            $('#speciality_mg_select option[value="'+val+'"]').removeAttr('selected').attr('disabled', true);
                        });

                        $('.speciality-select').selectpicker('deselectAll');
                        $('#departmentsModal').modal('show');
                        $('#departmentsModal .modal-body input[name="name"]').val(data.department.name);
                        $("#departmentsModal .modal-body select#selectSuperviser").val(data.department.superviser);
                        $('#departmentsModal .modal-body input#id').val(data.department.id);
                        $('#departmentsModal .modal-body textarea').val(data.department.description);
                        $('#departmentsModal .modal-footer #addNewDepartment').attr('data-new', 'false');

                        if(data.department.is_sector == 1){
                            $('#sectorCheck').prop('checked', true);
                            $('#sectorBlock').show();
                            $.each(data.speciality_bc, function(index, value){
                                $('#speciality_bc_select option[value="'+value+'"]').attr('selected', 'selected').attr('disabled', false);
                            });
                            $.each(data.speciality_mg, function(index, value){
                                $('#speciality_mg_select option[value="'+value+'"]').attr('selected', 'selected').attr('disabled', false);
                            });
                        } else {
                            $('#sectorCheck').attr('checked', false);
                            $('#sectorBlock').hide();
                        }

                        if(data.positions.length > 0){
                            var selected = '';
                            $('#departmentsModal #filter_group').prepend('<option></option>');

                            $.each(data.positions, function(index, position){
                                if(data.department.manager_position_id === position.id){
                                    selected = 'selected';
                                } else {
                                    selected = '';
                                }

                                $('#departmentsModal #filter_group').prepend(
                                    '<option value="'+ position.id +'" '+ selected +' > '+ position.name +'</option>'
                                );
                                var isLastElement = index == data.positions.length -1;

                                if (isLastElement) {
                                    $('#departmentsModal #selecterSearchUsers').show();
                                } 
                            });
                        } else {
                            $('#departmentsModal #filter_group').prepend('<option></option>');
                            $('#departmentsModal #selecterSearchUsers').show();
                        }

                        $('.filter-select').selectpicker('refresh');
                        $('.speciality-select').selectpicker('refresh');
                        $('.speciality-select').selectpicker({
                            dropupAuto: false
                        });
                        
                    }
                });
            });
            $('.modal').on('click', '#addNewDepartment', function(){
                let url = '';
                let formData = new FormData(document.forms.departmentForm);

                $('#departmentForm input').removeClass('is-invalid');
                $('#departmentForm input').removeClass('is-valid');
                $('#departmentForm .invalid-feedback').remove();

                if($(this).attr('data-new') == 'true'){
                    url = '{{ route('addNewDepartment') }}';
                }else{
                    url = '{{ route('editDepartment') }}';
                }

                $.ajax({
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function(data){
                        if (data == 'success') {
                            $('#modalClose').click();
                            Swal.fire({
                                title: 'Done!',
                                text: 'Отдел сохранён',
                                icon: 'success',
                                confirmButtonText: 'Закрыть'
                            }).then(confirmButtonText => {
                                dataTable.draw();
                            });
                        }
                    },
                    error: function(data){
                        if (data.status == 422) {
                            $.each(data.responseJSON.errors, function(field, errors) {
                                $.each(errors, function(index, error){
                                    $('[name="' + field + '"]').after('<div class="invalid-feedback">' + error + '</div>');
                                });

                                $('[name="' + field + '"]').addClass('is-invalid');
                            });
                        }
                    }
                });
            });
            
            let dataTable = $('#departmentDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('departmentDatatable') }}",
                    type: "post",
                },
                columns: [
                    { data: 'id'},
                    { data: 'name'},
                    { data: 'description'},
                    { data: 'superviser'},
                    { data: 'action'}
                ]
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