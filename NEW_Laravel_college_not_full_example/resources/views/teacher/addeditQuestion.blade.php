@extends('layouts.app_old')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"> {{ __('Edit question page') }} </div>

                <div class="panel-body">

     
                    <div class="panel panel-default">
            <div class="panel-body">
                {!! Form::open(array('url' => array( route('teacherPOSTQuestionAdd', [$disciplineId, $questionId]) ),'class'=>'form-horizontal padding-15','name'=>'Question_form','id'=>'Question_form','role'=>'form','enctype' => 'multipart/form-data')) !!} 
                <input type="hidden" name="id" value="{{ isset($question->id) ? $question->id : null }}">
                <input type="hidden" id="answersCount" name="count" value="{{ isset($answers) ? count($answers) : null }}">
                
                <div role="tabpanel">
                
                    
                    <div class="tab-content tab-content-default">  
                        
                      <div role="tabpanel" class="tab-pane active" id="cz"> 
                
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label"> {{ __('Question') }} </label>
                            <div class="col-sm-8">
                                <textarea type="text" name="question" class="form-control summernote" rows="2">{{ isset($question->question) ? $question->question : null }}</textarea>
                            </div>
                        </div>

                        @foreach($answers as $key => $answer)
                        <div class="answerBlock">
                            <div class="form-group">
                                <div for="" class="col-sm-4 control-label">
                                    <div class="answerTitle">{{ __('Answer') }} {{$key = $key+1}} </div><br />
                                    <label>
                                        <input type="checkbox" name="correct_{{$key}}" {{ isset($answer->points) && $answer->points>0 ? 'checked' : null }}> Correct
                                    </label>

                                    <div class="form-group">
                                        <label for="" class="col-sm-4 control-label"> {{ __('Points') }} </label>
                                        <div class="col-sm-8">
                                            <input type="text" name="points_{{$key}}" value="{{ isset($answer->points) ? $answer->points : null }}" class="form-control">
                                        </div>
                                    </div>

                                </div>

                                <input type="hidden" name="id_{{$key}}" value="{{ isset($answer->id) ? $answer->id : null }}">
                                
                                <div class="col-sm-8">
                                    <textarea type="text" name="answer_{{$key}}" class="form-control summernote" rows="1">{{ isset($answer->answer) ? $answer->answer : null }}</textarea>
                                </div>
                            </div>
                            
                            {{--<div class="form-group">
                                <label for="avatar" class="col-sm-3 control-label">Фото 1</label>
                                <div class="col-sm-9">
                                    <div class="media">
                                        <div class="media-left">
                                            @if(isset($Question->image))
                                             
                                                <img src="{{ URL::asset('images/uploads/Questions/'.$Question->image.'-s.jpg') }}" width="80" alt="person">
                                            @endif
                                                                            
                                        </div>
                                        <div class="media-body media-middle">
                                            <input type="file" name="image" class="filestyle"> 
                                        </div>
                                    </div>
                
                                </div>
                            </div>--}}
                        </div>

                        @endforeach
                        
                        
                    </div>
                
                <a class=" col-md-offset-3 btn btn-default-light" id="addAnswer">{{ __('Add another answer') }}</a>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">{{ isset($question->id) ? __('Edit') : __('Add') }}</button>
                         
                    </div>
                </div>
                
                {!! Form::close() !!} 
            </div>
        </div>
   


<script type="text/javascript">

    var summernoteHeight = 150;
    
    window.addEventListener("load", function(){
        $('.summernote').summernote({ height: summernoteHeight});   
    
    
        $("#addAnswer").on('click',function(){
            var block = $('.answerBlock');
            var count = $('#answersCount');

            count.val( function(i, oldval) {
                return ++oldval;
            });

            var newBlock = block.first().clone();
            newBlock.insertAfter(block.last());
            newBlock.find("input").removeAttr('value').removeAttr('checked');

            newBlock.find('.summernote').summernote('destroy');
            newBlock.find('.note-editor').remove();
            newBlock.find('.summernote').summernote({ height: summernoteHeight});

            newBlock.find(".answerTitle").text('Answer ' + count.val());
            newBlock.find("input[type='checkbox']").attr('name', 'correct_' + count.val()).prop('checked', false);
            newBlock.find("input[name='points_1']").attr('name', 'points_' + count.val()).attr('value', '');

            newBlock.find("input[name='id_1']").attr('value', null);
            newBlock.find("textarea[name='answer_1']").attr('name', 'answer_' + count.val()).text('');
            
            
        });

    });
    
</script>






                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
