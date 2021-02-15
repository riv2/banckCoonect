@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> </h2>

            <a href="{{ URL::to('/buildings') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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
                {!! Form::open(array('url' => array( route('adminBuildingEdit', ['id' => isset($building->id) ? $building->id : 'add']) ),'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Наименование</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" value="{{ isset($building->name) ? $building->name : ''}}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="col-sm-3 control-label">Адрес</label>
                        <div class="col-sm-9">
                            <input type="text" name="address" value="{{ isset($building->address) ? $building->address : ''}}" class="form-control">
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <div class="col-md-offset-3 col-sm-9 ">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </div>

                    {!! Form::close() !!}
            </div>


        </div>

        <script type="text/javascript">
            $("#serviceType").change(function(){
                var placesList = $('#placesList');

                if( $(this).find('select').val() == 'Master' ) placesList.show(150);
                else placesList.hide(150);
            });

        </script>

@endsection