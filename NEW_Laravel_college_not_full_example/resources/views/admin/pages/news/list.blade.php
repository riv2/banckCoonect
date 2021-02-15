@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">

            @if(\App\Services\Auth::user()->hasRight('news','create'))
                <div class="pull-right">
                    <a href="{{ route('adminNewsEdit', ['id' => 'add']) }}" class="btn btn-primary">Добавить объявление <i class="fa fa-plus"></i></a>
                </div>
            @endif

            <h2>Объявления</h2>
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
                        <th>Заголовок</th>
                        <th>Дата</th>
                        <th class="text-center width-150">Действие</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($newsList as $i => $single)
                        <tr>
                            <td class="text-center">{{ $single->id }}</td>
                            <td>{{ $single->title }}</td>
                            <td class="text-center">{{ date('d.m.Y', strtotime($single->created_at)) }}</td>
                            <td class="text-center">

                                <div class="btn-group">

                                    @if(\App\Services\Auth::user()->hasRight('news','edit'))
                                        <a class="btn btn-default" href="{{ route('adminNewsEdit', ['id' => $single->id]) }}"><i class="md md-edit"></i></a>
                                    @endif
                                    @if(\App\Services\Auth::user()->hasRight('news','delete'))
                                        <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="{{ route('adminNewsDelete', ['id' => $single->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
                                        </ul>
                                    @endif
                                </div>

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
        } );

    </script>

@endsection