@extends("admin.admin_app")

@section("content")
    <div id="main" class="library">
        @if(Session::has('literature_success_add'))
            <div class="margin-top alert alert-success" role="alert">
                {{ Session::get('literature_success_add') }}
            </div>
        @endif
        <div class="page-header">
            <h2>Библиотека:</h2>
        </div>
        <div class="row">
        	<div class="col-md-3">
        		<h3>Каталог Литературы</h3>
        	</div>
            <div class="col-md-3">
                <a href="{{ route('literature.reports.page') }}" class="btn btn-primary btn-block margin-t20">Отчётность</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('literature.statistic.page') }}" class="btn btn-primary btn-block margin-t20">Статистика</a>
            </div>
        	<div class="col-md-3">
        		<a href="{{ route('add.literature.to.catalog.page') }}" class="btn btn-primary btn-block margin-t20">Добавить запись</a>
        	</div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default panel-shadow margin-top">
                    <div class="panel-body">
                        <table 
                        	id="library_literature_catalog" 
                        	class="table table-striped table-hover dt-responsive" 
                        	style="table-layout: fixed;" 
                        	cellspacing="0" 
                        	width="100%"
						>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Имя</th>
                                    <th>Вид издания</th>
                                    <th>Год издания</th>
                                    <th>Автор</th>
                                    <th>Действие</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="inputW-50">Id</th>
                                    <th class="inputW-300">Имя</th>
                                    <th class="inputW-150">Вид издания</th>
                                    <th class="inputW-150">Год издания</th>
                                    <th class="inputW-50">Автор</th>
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
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/api/page.jumpToData().js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            let dataTable = $('#library_literature_catalog').DataTable({
                stateSave: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('library.catalog.datatable') }}",
                    type: "post",
                },
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12 position-relative'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-1 searchSection'><'col-sm-12 col-md-2 searchSectionButton'><'col-sm-12 col-md-4 d-flex justify-content-end'p>>",
                columns: [
                    { data: 'id', width: "25px" },
                    { data: 'name', width: "300px" },
                    { data: 'publication_type', width: "100px" },
                    { data: 'publication_year', width: "100px" },
                    { data: 'author', width: "150px" },
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

            var str = '<input type="text" id="searchPaginationPage" class="form-control margin-t10">';

            $('.searchSection').html(str);
            $('.searchSectionButton').html('<button class="btn btn-sm btn-default margin-t10" id="searchPaginationPageButton">Search</button>');

            $('#main').on('click', '#searchPaginationPageButton', function(){
                var page = $('#searchPaginationPage').val();
                page = page - 1;
                dataTable.page( page ).draw( 'page' );
            });
        });
    </script>
@endsection