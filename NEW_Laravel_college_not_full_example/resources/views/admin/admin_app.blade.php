<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{getcong('site_name')}} Администрирование</title>

    <link href="{{ URL::asset('upload/'.getcong('site_favicon')) }}" rel="shortcut icon" type="image/x-icon"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js-bootstrap-css/1.2.1/typeaheadjs.min.css">
    <link rel="stylesheet" href="{{ URL::asset('admin_assets/css/style.css') }}">

    <script src="{{ URL::asset('admin_assets/js/jquery.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
   
    @yield('style')
</head>

<body id="app-admin-panel" class="sidebar-push sticky-footer">

<!-- BEGIN TOPBAR -->

@include("admin.topbar")

<!-- END TOPBAR -->

<!-- BEGIN SIDEBAR -->

@include("admin.sidebar")

<!-- END SIDEBAR -->
<div class="container-fluid add-scrolling">

	@if(Session::has('messages'))
		<div class="content-wrapper height-auto">
			<div class="row no-margin">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					@foreach(Session::get('messages') as $flashMessages)
						<div class="alert margin-20 {{$flashMessages['class']}}">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							{!!$flashMessages['message']!!}
						</div>
					@endforeach
					<?php session()->remove('messages'); ?>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
	@endif

    @if(Session::has('flash_success'))
        <div class="content-wrapper height-auto margin-20">
            <div class="row no-margin">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="alert alert-success margin-20">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {!! Session::get('flash_success') !!}
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    @endif

    @if(Session::has('flash_error'))
        <div class="content-wrapper height-auto margin-20">
            <div class="row no-margin">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="alert alert-error margin-20">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {!! Session::get('flash_error') !!}
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    @endif

    @yield("content")
<!--
    <div class="footer">
        <a href="{{ URL::to('/dashboard') }}" class="brand">
            {{getcong('site_name')}}
        </a>
    </div>
-->
</div>


<div class="overlay-disabled"></div>


<!-- Plugins -->
<script src="{{ URL::asset('admin_assets/js/plugins.js') }}"></script>
<script src="{{ URL::asset('admin_assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('admin_assets/js/dataTables.rowGroup.min.js') }}"></script>

<!-- Loaded only in index.html for demographic vector map-->
<script src="{{ URL::asset('admin_assets/js/jvectormap.js') }}"></script>

<!-- App Scripts -->
<script src="{{ URL::asset('admin_assets/js/scripts.js') }}"></script>
<script src="{{ URL::asset('admin_assets/js/vue.js') }}"></script>

<script src="{{ URL::asset('admin_assets/js/axios.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
<script src="/assets/js/bootstrap-vue.js"></script>

@yield('scripts')
@yield('scripts_js')
@yield('scripts_sidebar')

</body>

</html>   
     		   
