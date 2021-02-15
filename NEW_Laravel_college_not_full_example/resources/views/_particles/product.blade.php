<div class="row container-realestate">
           	  @foreach($properties as $i => $property) 	
             	 <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="property-container">
              <div class="property-image">
                 
                <a href="{{URL::to('properties/'.$property->property_slug)}}">
	                <img src="{{ URL::asset('upload/properties/'.$property->featured_image.'-s.jpg') }}" alt="{{ $property->property_name }}">
                </a>
                <div class="property-price">
                  <h4>{{ getPropertyTypeName($property->property_type)->types }}</h4>
                  <span class="digits">@if($property->sale_price) {{$property->sale_price}} @else {{$property->rent_price}} @endif</span> {{getcong('currency_sign')}}
                </div>
                <div class="property-status">
                  @if ($property->property_purpose == 'rent')
                  	<span>{{__('Rent')}}</span>
                  @else
                  	<span>{{__('Sell')}}</span>
                  @endif
                </div>
              </div>
              <div class="property-features">
                <span><i class="fa fa-home"></i> {{$property->area}} m2</span>
                <span><i class="fa fa-th-large"></i> {{$property->bedrooms}}</span>
                <span><i class="fa fa-building"></i> {{$property->bathrooms}}</span>
                <span><i class="fa fa-eye"></i> {{$property->hits}}</span>
              </div>
              <div class="property-content">
                <h3><a href="{{URL::to('properties/'.$property->property_slug)}}">{{ str_limit($property->property_name,35) }}</a> <small>{{ str_limit($property->address,40) }}</small></h3>
              </div>
            </div>
          </div>
              <!-- break -->
           	  @endforeach
           	  
              
            </div>