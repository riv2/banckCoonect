@extends('layouts.app_old')

@section('title', __('Journal'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('Journal')</div>

                    <div class="panel-body">
                        <table id="data-table" class="table table-striped table-hover dt-responsive">
                            @foreach($disciplines as $i => $discipline)
                                <tr>
                                    <td>
                                        <a href="{{route('teacherDiscipline', ['id' => $discipline->id])}}">{{$discipline->name}}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
