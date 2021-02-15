@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">

            @if(\App\Services\Auth::user()->hasRight('buildings','create'))
            <div class="pull-right">
                <a href="{{ route('adminBuildingEdit', ['id' => 'add']) }}" class="btn btn-primary">Добавить здание <i class="fa fa-plus"></i></a>
            </div>
            @endif

            <h2>Здания</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-warning">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">

                <table id="data-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Название</th>
                        <th>Адрес</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($buildingList as $building)
                        <tr>
                            <td>{{ $building->id }}</td>
                            <td>{{ $building->name }}</td>
                            <td>{{ $building->address }}</td>

                            <td class="text-center">

                                <div class="btn-group">
                                    @if(\App\Services\Auth::user()->hasRight('buildings','edit'))
                                        <a class="btn btn-default" href="{{ route('adminBuildingEdit', ['id' => $building->id]) }}"><i class="md md-edit"></i></a>
                                    @endif
                                    @if(\App\Services\Auth::user()->hasRight('buildings','delete'))
                                        <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="{{ route('adminBuildingDelete', ['id' => $building->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
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



@endsection