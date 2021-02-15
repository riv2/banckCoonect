@extends("app")

@section('head_title', 'Login | '.getcong('site_name') )
@section('head_url', Request::url())

@section("content")
<div id="content" class="registration_page">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
				<h1>Log in</h1>
				@if(Session::has('flash_message'))
					<div class="alert alert-success">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						{{ Session::get('flash_message') }}
					</div>
				@endif
				<div class="message">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
				</div>
				<div>
					
					
					{!! Form::open(array('url' => 'password/email','class'=>'','id'=>'passwordform','role'=>'form')) !!}
                    <div class="panel-body">
                    	
                    	@if(Session::has('flash_message'))
				    <div class="alert alert-success">
				    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span></button>
				        {{ Session::get('flash_message') }}
				    </div>
				@endif
                    	<div class="message">
												<!--{!! Html::ul($errors->all(), array('class'=>'alert alert-danger errors')) !!}-->
							                    	@if (count($errors) > 0)
											    <div class="alert alert-danger">
											    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
											        <ul>
											            @foreach ($errors->all() as $error)
											                <li>{{ $error }}</li>
											            @endforeach
											        </ul>
											    </div>
											@endif
							                    	
							                    </div>
                    	
                        <div class="form-group">
                            <label for="email">Reset password</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
                        </div>
                       <!--<div class="form-group">                            
                            <a href="{{ URL::to('admin/') }}" class="small pull-right">Логин?</a>
                             
                        </div>-->
                         
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary"> Send e-mail with link <i class="md md-lock-open"></i></button>
                    </div>
                {!! Form::close() !!} 
					
					
				</div>
			</div>
		</div>
	</div>
</div>

@endsection