@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">

            @if(\App\Services\Auth::user()->hasRight('orders','create'))
                <div class="pull-right">
                    <a href="{{ route('adminOrderEdit', ['id' => 'new']) }}" class="btn btn-primary">Добавить приказ <i class="fa fa-plus"></i></a>
                </div>
            @endif

            <h2>Приказы</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">

                <table id="main-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Наименование</th>
                        <th class="text-center width-150">Номер</th>
                        <th class="text-center width-150">Дата</th>
                        <th class="text-center width-150">Действие</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($orderList as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->orderName->name ?? '' }}</td>
                            <td>{{ $order->number }}</td>
                            <td>{{ $order->date }}</td>
                            <td>
                                @if(\App\Services\Auth::user()->hasRight('orders','read') && !\App\Services\Auth::user()->hasRight('orders','edit'))
                                    <a class="btn btn-default" href="{{ route('adminOrderEdit', ['id' => $order->id]) }}"><i class="md md-visibility "></i></a>
                                @endif
                                @if(\App\Services\Auth::user()->hasRight('orders','edit'))
                                    <a class="btn btn-default" href="{{ route('adminOrderEdit', ['id' => $order->id]) }}"><i class="md md-edit"></i></a>
                                @endif
                                @if(\App\Services\Auth::user()->hasRight('orders','delete'))
                                    <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a href="{{ route('adminOrderDelete', ['id' => $order->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>

    <script type="text/javascript">

        $(document).ready(function() {
            var table = $('#main-table').DataTable();

            $("#main-table thead th").each( function ( i ) {

                if (i == 0 || i == 2) {
                    var select = $('<select class="form-control"><option value=""></option></select>')
                        .appendTo( $(this) )
                        .on( 'change', function () {
                            var val = $(this).val();

                            table.column( i )
                                .search( val ? '^'+$(this).val()+'$' : val, true, false )
                                .draw();
                        } );

                    table.column( i ).data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' );
                    } );
                }
            } );
        } );

    </script>

@endsection