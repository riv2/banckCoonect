<div class="page-title no-border">
			<h1>{{__('Our services')}}</h1>
			<h3><span>{{__('Real estate services in Prague, Czech Republic by #1 agent Balkiya Gumarova.')}}</span></h3>
		</div>
		<section id="sec4">
			<div class="row">
				
				@foreach ($services as $service)
				<div class="col-md-4">
					<div class="services-box box-item">
						<a href="{{ route('service', ['url' => $service->url]) }}">
							<span class="overlay"></span>
							<img src="{{$service->image}}" alt="" class="respimg">
						</a>
						<div class="services-info">
							<a href="{{ route('service', ['url' => $service->url]) }}"><h4>{{$service->name}}</h4></a>
							{!! $service->description !!}
							
						</div>
					</div>
				</div>
				@endforeach
				
				
			</div>
		</section>