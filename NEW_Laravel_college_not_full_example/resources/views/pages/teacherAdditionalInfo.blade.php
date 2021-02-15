@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__("bc. Application page")}}</div>

                    <div class="panel-body">

                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('teacherAdditionalInfoPost') }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="exampleInputEmail1">{{__("Your teaching skills")}}:</label>
                                <input name="skills" type="text" placeholder="{{__('Type your skills separated by comma guitar, embroidery, drawing)')}}" style="width:100%">
                            </div>


                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{__("Send")}}
                                    </button>
                                </div>
                            </div>

                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
