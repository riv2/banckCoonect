@extends('layouts.app')

@section('title', __('My Resume'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">@lang('My Resume')</h2>
            </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <table id="vacancyResumeDatatable" class="table table-striped table-hover dt-responsive text-center" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Вакансия</th>
                                <th>Статус</th>
                                <th>Последнее изменение</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="requirementModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Список требований</h4>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="modalClose">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" language="javascript" src="https://nightly.datatables.net/responsive/js/dataTables.responsive.min.js">
    </script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let dataTable = $('#vacancyResumeDatatable').DataTable({
            "initComplete": function(settings, json) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: { 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('vacancy.resume.datatable') }}",
                type: "post",
            },
            columns: [
                { data: 'vacancy'},
                { data: 'status'},
                { data: 'updated_at'},
                { data: 'action' }
            ],
            "drawCallback": function( settings ) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        $(document).ready(function(){
            $('#vacancyDatatable').on('click', '.showRequirements', function(){
                var id = $(this).attr('data-id');

                $.ajax({
                    url: '{{ route("get.requirements") }}',
                    type: 'post',
                    data: {
                        id: id
                    },
                    success: data => {
                        $('#requirementModal .modal-body').empty();
                        $('#requirementModal').modal('show');
                        $.each(data.requirements, function(field, value) {
                            var str = '<div class="form-group row">' +
                                        '<label for="staticEmail" class="col-sm-3 col-form-label">Требование:</label>' +
                                        '<div class="col-sm-9 margin-t10">'+value.name+'</div>' +
                                    '</div>' +
                                    '<div class="form-group row">' +
                                        '<label for="staticEmail" class="col-sm-3 col-form-label">Описание:</label>' +
                                        '<div class="col-sm-9 margin-t10">'+value.description+'</div>' +
                                    '</div>';
                                    
                            if(value.start_date){
                                str += '<div class="form-group row">' +
                                        '<label for="staticEmail" class="col-sm-3 col-form-label">Начало:</label>' +
                                        '<div class="col-sm-9 margin-t10">'+value.start_date+'</div>' +
                                    '</div>';
                            }
                            if(value.end_date){
                                str += '<div class="form-group row">' +
                                        '<label for="staticEmail" class="col-sm-3 col-form-label">Конец:</label>' +
                                        '<div class="col-sm-9 margin-t10">'+value.end_date+'</div>' +
                                    '</div>';
                            }
                            if(field !== data.requirements.length - 1){
                                str += '<hr>';
                            }
                            $('#requirementModal .modal-body').append(str);
                        });
                    }
                });
            });
        });
    </script>
@endsection
