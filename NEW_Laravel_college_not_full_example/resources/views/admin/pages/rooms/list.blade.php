@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">

            @if(\App\Services\Auth::user()->hasRight('rooms','create'))
            <div class="pull-right">
                <a href="{{ route('adminRoomEdit', ['id' => 'add']) }}" class="btn btn-primary">Добавить аудиторию <i class="fa fa-plus"></i></a>
            </div>
            @endif

            <h2>Аудитории</h2>
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

                <table id="data-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Номер</th>
                        <th>Тип</th>
                        <th>Здание</th>
                        <th>Этаж</th>
                        <th>Вместимость</th>
                        <th>Кодиционер</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($roomList as $room)
                        <tr>
                            <td>{{ $room->id ?? '' }}</td>
                            <td>{{ $room->number ?? ''}}</td>
                            <td>{{ __('room_type_' . $room->type ?? '')}}</td>
                            <td>{{ $room->building->name ?? '' }}</td>
                            <td>{{ $room->floor ?? ''}}</td>
                            <td>{{ $room->seats_count ?? ''}}</td>
                            <td>{{ isset($room->conditioner) && $room->conditioner ? 'Да' : 'Нет'}}</td>

                            <td class="text-center">

                                <div class="btn-group">
                                    @if(\App\Services\Auth::user()->hasRight('rooms','edit'))
                                        <a class="btn btn-default" href="{{ route('adminRoomEdit', ['id' => $room->id]) }}"><i class="md md-edit"></i></a>
                                    @endif
                                    @if(\App\Services\Auth::user()->hasRight('rooms','delete'))
                                        <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="{{ route('adminRoomDelete', ['id' => $room->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
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