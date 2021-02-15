<!DOCTYPE html>
<html lang="en">
	<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{getcong('site_name')}} Администрирование</title>
	
	<link href="{{ URL::asset('upload/'.getcong('site_favicon')) }}" rel="shortcut icon" type="image/x-icon" />

	<link rel="stylesheet" href="{{ URL::asset('admin_assets/css/style.css') }}">
	
	<script src="{{ URL::asset('admin_assets/js/jquery.js') }}"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>
<body>
  <div class="container-fluid">
      
<div id="main">
    <div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login">

            <div class="logo" href="#" align="">
              
            <a href="{{ URL::to('/') }}"><img src="{{ URL::asset('upload/'.getcong('site_logo')) }}" alt="Лого"></a>
            </div>
            <div class="panel panel-default panel-shadow">
               <!-- <div class="avatar">
                    <img src="images/guy.jpg" alt="guy" class="img-circle img-responsive"/>
                </div>-->
                {!! Form::open(array('url' => '/login','class'=>'','id'=>'loginform','role'=>'form')) !!}
                    <div class="panel-body">
                    	
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
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Ваш email">
                        </div>
                        <div class="form-group margin-none">
                            <div class="media">
                                <div class="media-body media-middle">
                                    <label for="password">Пароль</label>
                                </div>
                                <div class="media-right media-middle">
                                    <a href="{{ URL::to('/password/email') }}" class="small pull-right">Забыли?</a>
                                </div>
                            </div>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Пароль">
                        </div>
                        <div class="form-group checkbox">                            
                            <input type="checkbox" name="remember" id="checkbox1" />
                            <label for="checkbox1">Запомнить меня</label>
                             
                        </div>
                         
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Авторизоваться <i class="md md-lock-open"></i></button>
                    </div>
                {!! Form::close() !!} 
            </div>
           
        </div>
    </div>
</div>
</div>


  </div>
	
	 <!-- Plugins -->
  <script src="{{ URL::asset('admin_assets/js/plugins.js') }}"></script>

  <!-- App Scripts -->
  <script src="{{ URL::asset('admin_assets/js/scripts.js') }}"></script>

</body>
 
</html>
