@extends("admin.admin_app")

@section("content")

<div id="main">
	<div class="page-header">
		<h2> {{ isset($question->id) ? 'Редактировать: '. $question->id : 'Добавить статью' }}</h2>
		
		<a href="{{ route('adminQuestions', [$disciplineId]) }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
	  
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
                {!! Form::open(array('url' => array( route('POSTQuestionAdd', [$disciplineId, $questionId]) ),'class'=>'form-horizontal padding-15','name'=>'Question_form','id'=>'Question_form','role'=>'form','enctype' => 'multipart/form-data')) !!} 
                <input type="hidden" name="id" value="{{ isset($question->id) ? $question->id : null }}">
                <input type="hidden" id="answersCount" name="count" value="{{ isset($answers) ? count($answers) : null }}">
                
                <div role="tabpanel">
			    
				    
				    <div class="tab-content tab-content-default">  
					    
					  <div role="tabpanel" class="tab-pane active" id="cz"> 
                
		                <div class="form-group">
		                    <label for="" class="col-sm-3 control-label">Вопрос</label>
		                    <div class="col-sm-9">
		                        <textarea type="text" name="question" class="form-control summernote" rows="2">{{ isset($question->question) ? $question->question : null }}</textarea>
		                    </div>
		                </div>

		                @foreach($answers as $key => $answer)
		                <div class="answerBlock">
			                <div class="form-group">
			                    <div for="" class="col-sm-3 control-label">
			                    	<div class="answerTitle">Ответ {{$key = $key+1}} </div><br />
			                    	<label>
	                                    <input type="checkbox" name="correct_{{$key}}" {{ isset($answer->points) && $answer->points>0 ? 'checked' : null }}> Правильный
	                                </label>

	                                <div class="form-group">
					                    <label for="" class="col-sm-3 control-label">Баллов</label>
					                    <div class="col-sm-9">
					                        <input type="text" name="points_{{$key}}" value="{{ isset($answer->points) ? $answer->points : null }}" class="form-control">
					                    </div>
					                </div>

			                    </div>

			                    <input type="hidden" name="id_{{$key}}" value="{{ isset($answer->id) ? $answer->id : null }}">
			                    
			                    <div class="col-sm-9">
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
                
                <a class=" col-md-offset-3 btn btn-default-light" id="addAnswer">Добавить еще ответ</a>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                    	<button type="submit" class="btn btn-primary">{{ isset($question->id) ? 'Редактировать' : 'Добавить' }}</button>
                         
                    </div>
                </div>
                
                {!! Form::close() !!} 
            </div>
        </div>
   
    
</div>

<script type="text/javascript">

	var summernoteHeight = 150;
	
	$( document ).ready(function(){
		$('.summernote').summernote({ height: summernoteHeight});	
	});
	
	$("#addAnswer").on('click',function(){
		var block = $('.answerBlock');
		var count = $('#answersCount');

		count.val( function(i, oldval) {
		    return ++oldval;
		});

		var newBlock = block.first().clone();
		newBlock.insertAfter(block.last());
		newBlock.find("input").removeAttr('value').removeAttr('checked');

		newBlock.find('.summernote').code('').summernote('destroy');
		newBlock.find('.note-editor').remove();
		newBlock.find('.summernote').summernote({ height: summernoteHeight});

		newBlock.find(".answerTitle").text('Ответ ' + count.val());
		newBlock.find("input[type='checkbox']").attr('name', 'correct_' + count.val()).prop('checked', false);
		newBlock.find("input[name='points_1']").attr('name', 'points_' + count.val()).attr('value', '');

		newBlock.find("input[name='id_1']").attr('value', null);
		newBlock.find("textarea[name='answer_1']").attr('name', 'answer_' + count.val()).text('');
		
		
	});
	
</script>

@endsection