@extends('layouts.app_old')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{{ __('Queze themes page') }}</div>

                <div class="panel-body">


                    @if(Session::has('flash_message'))
                        <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span></button>
                            {{ Session::get('flash_message') }}
                        </div>
                    @endif
     
                    <div class="panel panel-default panel-shadow">
                        <div class="panel-body">
                             
                            <table id="data-table" class="table table-striped table-hover dt-responsive" cellspacing="0">
                                
                                @foreach($allDisciplines as $i => $discipline)
                                   <tr>
                                    
                                        <td>
                                            <a href="{{ route('teacherQuestions', ['id' => $discipline->id]) }}">
                                                {{ $discipline->name }}</a>
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
    </div>
</div>
@endsection
