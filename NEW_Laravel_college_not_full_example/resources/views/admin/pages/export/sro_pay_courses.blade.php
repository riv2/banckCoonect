@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Экспорт: СРО курсовые (купленные) </h2>
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
                <div class="col-md-6">
                    <form class="form-inline" action="{{ route('adminExportSROPayCoursesPost') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
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