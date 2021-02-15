@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Заявления на отчисление</h2>
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
                        <th style="width: 50px;">ID</th>
                        <th>ФИО</th>
                        <th>Курс</th>
                        <th>Статус</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($list as $item)
                        <tr>
                            <td>{{ $item->id ?? '' }}</td>
                            <td>{{ $item->studentProfile->fio ?? ''}}</td>
                            <td>{{ $item->studentProfile->course ?? ''}}</td>
                            <td>{{ __('application_status_' . $item->status ?? '')}}</td>
                            <td class="text-center">

                                <div class="btn-group">
                                    @if(\App\Services\Auth::user()->hasRight('applications','edit'))
                                        <a class="btn btn-default" href="{{ route('adminApplicationEdit', ['type' => $item->key, 'id' => $item->id]) }}"><i class="md md-edit"></i></a>
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