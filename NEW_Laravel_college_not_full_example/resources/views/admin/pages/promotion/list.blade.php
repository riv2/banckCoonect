@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">

            <h2>Заявки на акции</h2>
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
                        <th>ID</th>
                        <th>Наименование</th>
                        <th>ФИО студента</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($promotionUserList as $promo)
                        <tr>
                            <td>{{ $promo->id }}</td>
                            <td>{{ __($promo->name) }}</td>
                            <td>{{ $promo->fio }}</td>

                            <td class="text-center">
                                @if(\App\Services\Auth::user()->hasRight('promotions','edit'))
                                    <div class="btn-group">
                                        <a class="btn btn-default" href="{{ route('adminPromotionInfo', ['id' => $promo->id]) }}"><i class="md md-edit"></i></a>
                                    </div>
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
@endsection