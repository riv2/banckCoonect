@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Экспорт: студенты</h2>
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
                <div class="col-xs-12 col-md-12">
                    <form class="form-inline" action="{{ route('adminExportStudents') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group">
                            <label>Год поступления:</label>

                            <select class="form-control" name="year">
                                <option value="0">Все года</option>
                                @for($year = date('Y', time()); $year >= 2015; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-primary">Экспорт</button>
                        </div>
                    </form>
                    <hr>
                    <form class="form-inline" action="{{ route('adminExportStudentsByDate') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group">
                            <h5>Выгрузка со статусом студента на выбранную дату:</h5>
                            <br>
                            <div class="form-group">
                                <label for="">Дата: </label>
                                <input type="date" class="form-control" name="date">
                            </div>
                            <button type="submit" class="btn btn-primary">Экспорт</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>

@endsection