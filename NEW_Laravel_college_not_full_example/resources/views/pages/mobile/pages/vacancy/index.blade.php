@extends('layouts.app')

@section('title', __('Vacancies'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">@lang('Vacancies')</h2>
            </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body table-responsive">
                    <table id="vacancyDatatable" class="table table-striped table-hover dt-responsive text-center" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Описание</th>
                                <th>График</th>
                                <th>Трудоустройство</th>
                                <th>Ставка</th>
                                <th>Оклад</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
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

        let dataTable = $('#vacancyDatatable').DataTable({
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
                url: "{{ route('vacancy.datatable') }}",
                type: "post",
            },
            columns: [
                { data: 'name'},
                { data: 'description'},
                { data: 'schedule_id'},
                { data: 'employment'},
                { data: 'price'},
                { data: 'salary'},
                { data: 'action' }
            ],
            "drawCallback": function( settings ) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    </script>
@endsection
