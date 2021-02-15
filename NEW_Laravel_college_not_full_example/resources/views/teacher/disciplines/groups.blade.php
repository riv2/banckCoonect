@extends('layouts.app_old')

@section('title', __('Groups'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('Groups of discipline') "{{$discipline->name}}"</div>

                    <div class="panel-body">
                        <table id="data-table" class="table table-striped table-hover dt-responsive">
                            @foreach($groups as $i => $group)
                                <tr>
                                    <td>
                                        <a href="{{route('teacherGroup', ['group_id' => $group->id, 'discipline_id' => $discipline->id])}}">{{$group->name}}</a>
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
