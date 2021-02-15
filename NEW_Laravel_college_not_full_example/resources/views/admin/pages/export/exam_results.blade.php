@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Экспорт: Результаты студентов</h2>
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
                <div class="col-md-2">
                    <form action="{{ route('adminExportExamResults') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group">
                            <label>Год поступления:</label>

                            <select class="form-control" name="year">
                                @for($year = date('Y', time()); $year >= 2015; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>

                        </div>
                        <div class="form-group">
                            <label>Курс:</label>
                            <select class="form-control" name="course">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Экспорт</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>

@endsection
