@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> </h2>

            <a href="{{ route('adminEntranceTestsList') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

        </div>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-body">
                {!! Form::open(array('url' => array( route('adminEntranceTestsEdit', ['id' => $entrance->id == 0 ? 'add' : $entrance->id]) ),'class'=>'form-horizontal padding-15','name'=>'entrance_test_form','id'=>'entrance_test_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Наименование</label>
                    <div class="col-sm-9">
                        <input type="text" required name="name" value="{{ $entrance->name }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Проходной балл</label>
                    <div class="col-sm-1">
                        <input type="number" step="0.01" required name="total_points" value="{{ $entrance->total_points }}" class="form-control">
                    </div>
                </div>

                <hr>

                @include('admin.pages.vue_modules.quize.quize_list')

                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>


        </div>
    </div>

@endsection