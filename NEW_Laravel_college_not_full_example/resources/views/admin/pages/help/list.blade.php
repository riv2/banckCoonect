@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Запросы обратной связи</h2>
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
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($helpList as $help)
                        <tr>
                            <td>{{ $help->id ?? '' }}</td>
                            <td>{{ $help->user->fio ?? ''}}</td>
                            <td>{{ $help->phone }}</td>

                            <td class="text-center">

                                <div class="btn-group">
                                    @if(\App\Services\Auth::user()->hasRight('helps','edit'))
                                        <a class="btn btn-default" href="{{ route('adminHelpInfo', ['id' => $help->id]) }}"><i class="md md-visibility "></i></a>
                                    @endif
                                    @if(\App\Services\Auth::user()->hasRight('helps','delete'))
                                        <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="{{ route('adminHelpDelete', ['id' => $help->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
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