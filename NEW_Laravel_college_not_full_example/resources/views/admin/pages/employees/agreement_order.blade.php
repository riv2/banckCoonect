@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> Просмотр приказа </h2>
            <a href="{{ route('employees.orders.page') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>

        <div class="panel panel-default" id="main-panel">
            <div class="panel-body form-horizontal">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Наименование</label>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" value="{{ $order->orderName->name ?? ''}}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Номер приказа</label>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" value="{{$order->number ?? ''}}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Дата приказа</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" value="{{$order->order_date ?? '' }}" disabled>
                    </div>
                </div>
                @isset(optional($order)->file)
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Файл</label>
                        <div class="col-sm-9 padding-0">
                            <div class="coll">
                                <div class="col-sm-12">
                                    <label>{{ $order->file }}</label>
                                </div>
                                <div class="col-sm-12">
                                    <a href="{{ route('employees.download.order', ['name' => $order->file]) }}">
                                        <button type="button" class="btn btn-primary">Скачать приказ</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset
                <div class="form-group">
                    <label class="col-sm-3 control-label">Список голосующих</label>
                    <div class="col-sm-4">
                        <select disabled class="form-control" multiple>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->user_id }}">{{ $employee->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr>
            </div>
        </div>
    </div>
@endsection

