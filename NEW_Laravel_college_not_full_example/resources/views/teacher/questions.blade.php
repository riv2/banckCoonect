@extends('layouts.app_old')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"> {{ __('Questions page') }} </div>

                <div class="panel-body">


                    <div class="pull-right">
                        <a href="{{ route('teacherQuestionAdd', ['id' => $disciplineId]) }}" class="btn btn-primary"> {{ __('add question') }} <i class="fa fa-plus"></i></a>
                    </div>
     

     
                    <div class="panel panel-default panel-shadow">
                        <div class="panel-body">
                             
                            <table id="data-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                

                                <tbody>
                                @foreach($allQuestions as $i => $question)
                                   <tr>
                                    <td>{!! $question->question !!}</td>
                                    
                                    <td class="text-center" style="min-width: 200px;">
                                    
                                    <div class="btn-group">
                                        <a class="btn btn-default" href="{{ route('teacherQuestionEdit', [$disciplineId, $question->id]) }}"> {{ __('Edit') }} </a>
                                        
                                        
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> {{ __('Delete') }} <span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu"> 
                                            
                                            
                                            <li><a href="{{ route('teacherQuestionDelete', [$question->id]) }}"><i class="md md-delete"></i> {{ __('Delete') }} </a></li>
                                        </ul>
                                    </div> 
                                    
                                </td>
                                    
                                </tr>
                               @endforeach
                                 
                                </tbody>
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
