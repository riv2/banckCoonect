@extends('student.mg_application.main')

@section('part')
<div class="col-12">

    <h3>{{ __('Address') }}</h3>

    @if($profile->alien == 1 )
        <div class="form-group country">
            <label for="region_id" class="control-label">{{__('Country')}}</label>
            <select class="form-control" name="country_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                @foreach($countries as $item)
                    <option @if($item->id == $mgApplication->country_id) selected @endif value="{{$item->id}}">{{ __($item->name) }}</option>
                @endforeach
            </select>
        </div>
    @else
        <div class="form-group regions">
            <label for="region_id" class="control-label">{{__('Region')}}</label>
            <select class="form-control" name="region_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                @foreach($regions as $item)
                    <option @if($item->id == $mgApplication->region_id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="form-group cities">
        <label for="city_id" class="control-label">{{__('Locality')}}</label>
        <div class="autocomplete">
            <input id="city" type="text" class="form-control" name="city" required autofocus>
            {{--<select class="selectpicker" name="city_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                @foreach($cities as $item)
                    <option region="{{$item->region_id}}" value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </select>--}}
        </div>
    </div>

    <div class="form-group">
        <label for="street" class="control-label">{{__('Street')}}</label>
        <input id="street" type="text" class="form-control" name="street" value="{{ old('street') }}" required autofocus>
    </div>

    <div class="form-group">
        <label for="building_number" class="control-label">{{__('Buliding number')}}</label>
        <input id="building_number" type="text" class="form-control" name="building_number" value="{{ old('building_number') }}" required autofocus>
    </div>

    <div class="form-group">
        <label for="apartment_number" class="control-label">{{__('Apartment number')}}</label>
        <input id="apartment_number" type="text" class="form-control" name="apartment_number" value="{{ old('apartment_number') }}" autofocus>
    </div>

</div>
@endsection