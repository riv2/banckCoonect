@extends("admin.admin_app")

@section("content")
    <div id="main" class="library">
        <div class="page-header">
            <h2>Статистика каталога:</h2>
        </div>
        <a href="{{ route('library.page') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        <div class="row">
            <div class="col-md-12">
                <div id="literature_statistic_chart" style="width: 100%; height: 500px;"></div>
            </div>
        </div>

        <div class="row margin-t30">
            <div class="col-md-12">
                <h4 class="margin-0">Статистика скачиваний:</h4>
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
                                    <th>Имя</th>
                                    <th>Дата</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="inputW-300">Имя</th>
                                    <th class="inputW-150">Дата</th>
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        let dataTable = $('#library_literature_catalog').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('library.downloads.statistic.datatable') }}",
                type: "post",
            },
            columns: [
                { data: 'name', width: "300px" },
                { data: 'created_at', width: "100px" },
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
    </script>
    <script type="text/javascript">
        axios.post('{{ route('library.statistic.chart') }}')
            .then(response => {
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = google.visualization.arrayToDataTable(response.data.chart);

                    var options = {
                        title: 'Статистика посещений каталога',
                        hAxis: {title: 'Месяц',  titleTextStyle: {color: '#333'}},
                        vAxis: {
                            minValue: 0,
                            format: '0'
                        }
                    };

                    var chart = new google.visualization.AreaChart(document.getElementById('literature_statistic_chart'));
                    chart.draw(data, options);
                }
            });
    </script>
@endsection