@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Вакансии</h2>
                </div> 
                <div class="col-md-2 text-right">
                    <button v-on:click="createVacancyModal" class="margin-top btn btn-primary btn-lg" id="createPosition">
                        Создать Вакансию
                    </button>
                </div>
            </div>
        </div>

        <div class="panel panel-default panel-shadow margin-top">
            <div class="panel-body">
                <table id="vacancyDatatable" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Имя</th>
                            <th>Описание</th>
                            <th>Отдел</th>
                            <th>График работы</th>
                            <th>Тип занятости</th>
                            <th>Ставка</th>
                            <th>Оклад</th>
                            <th class="text-center width-100">Действие</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="inputW-50">Id</th>
                            <th class="inputW-150">Имя</th>
                            <th class="inputW-150">Описание</th>
                            <th class="inputW-150">Отдел</th>
                            <th class="inputW-150">График работы</th>
                            <th class="inputW-150">Тип занятости</th>
                            <th class="inputW-100">Ставка</th>
                            <th class="inputW-100">Оклад</th>
                            <th class="inputW-100">Действие</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="vacancyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><span id="positionType">Новая</span> Вакансия</h4>
                    </div>
                    <div class="modal-body">
                        <form id="vacancyForm">
                            <div class="form-group">
                                <label>Выберите Должность</label>
                                <select class="form-control" id="positionsList" name="position_id" required data-live-search="true">
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Выберите График</label>
                                <select class="form-control" name="schedule_id">
                                    @foreach($work_schedule as $shedule)
                                        <option value="{{ $shedule->id }}">{{ $shedule->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Выберите Тип Занятости</label>
                                <select class="form-control" name="employment">
                                    <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_MAIN }}">{{ App\EmployeesUsersPosition::EMPLOYMENT_MAIN }}</option>
                                    <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME }}">{{ App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME }}</option>
                                </select>
                            </div>
                            <div class="form-group margin-top">
                                <label>Ставка:</label>
                                <input class="form-control" type="text" name="price" placeholder="Ставка">
                            </div>
                            <div class="form-group margin-top">
                                <label>Оклад:</label>
                                <input class="form-control" type="text" name="salary" placeholder="Оклад">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary" id="addNewVacancy">Сохранить Вакансию</button>
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
            createVacancyModal: function(event){
                $('#vacancyModal').modal('show');
                $('#vacancyModal .modal-body input').val('');
                $('#vacancyModal .modal-body select').val('');
            }
        }
    });
    
    $(document).ready(function(){
        $('#positionsList').selectpicker();
        var dataTable = $('#vacancyDatatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('employees.vacancy.datatable') }}",
                type: "post",
            },
            columns: [
                { data: 'id', width: "25px" },
                { data: 'name', width: "100px" },
                { data: 'description', width: "100px" },
                { data: 'department', width: "100px" },
                { data: 'schedule', width: "100px" },
                { data: 'employment', width: "100px" },
                { data: 'price', width: "100px" },
                { data: 'salary', width: "100px" },
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

        $('#vacancyModal').on('click', '#addNewVacancy', function(){
            let formData = new FormData(document.forms.vacancyForm);

            $('#vacancyForm input').removeClass('is-invalid');
            $('#vacancyForm textarea').removeClass('is-invalid');
            $('#vacancyForm .invalid-feedback').remove();

            axios.post('{{ route('employees.add.new.vacancy') }}', formData)
                .then(response => { 
                    if(response.data.status == 'success'){
                        $('#vacancyModal').modal('hide');
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
        });
    });
</script>

@endsection