@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__("Specified address")}}</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route( \App\Teacher\ProfileTeacher::REGISTRATION_STEP_ADD_ADRESS_POST) }}">
                            {{ csrf_field() }}

                            <div class="form-group country">
                                <div class="col-md-12">
                                    <h4> {{__('Actual_address')}} </h4>
                                </div>
                            </div>

                            @if($profile->alien == 1 )
                                <div class="form-group country">
                                    <label for="region_id" class="col-md-4 control-label">{{__('Country')}}</label>
                                    <div class="col-md-8">
                                        <select class="selectpicker" name="country_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                            @foreach($country as $itemCountry)
                                                <option value="{{$itemCountry->id}}">{{$itemCountry->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="form-group regions">
                                    <label for="region_id" class="col-md-4 control-label">{{__('Region')}}</label>
                                    <div class="col-md-8">
                                        <select class="selectpicker" name="region_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                            @foreach($region as $itemRegion)
                                                <option value="{{$itemRegion->id}}">{{$itemRegion->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group cities">
                                <label for="city_id" class="col-md-4 control-label">{{__('Locality')}}</label>
                                <div class="col-md-8 ">
                                    <!-- <input id="city" type="text" class="form-control autocomplete" name="city" required autofocus> -->
                                    <select class="selectpicker" name="city_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                        @foreach($city as $itemCity)
                                            <option value="{{$itemCity->id}}">{{$itemCity->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="street" class="col-md-4 control-label">{{__('Street')}}</label>
                                <div class="col-md-8">
                                    <input id="street" type="text" class="form-control" name="street" value="" required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="building_number" class="col-md-4 control-label">{{__('Buliding number')}}</label>
                                <div class="col-md-4">
                                    <input id="building_number" type="text" class="form-control" name="building_number" value="" required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="apartment_number" class="col-md-4 control-label">{{__('Apartment number')}}</label>
                                <div class="col-md-4">
                                    <input id="apartment_number" type="text" class="form-control" name="apartment_number" value="{{ $bcApplication->apartment_number ?? '' }}" autofocus>
                                </div>
                            </div>


                            <div class="form-group country">
                                <div class="col-md-12">
                                    <h4> {{__('Home_address')}} </h4>
                                </div>
                            </div>
                            @if($profile->alien == 1 )
                                <div class="form-group country">
                                    <label for="region_id" class="col-md-4 control-label">{{__('Country')}}</label>
                                    <div class="col-md-8">
                                        <select class="selectpicker" name="home_country_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                            @foreach($country as $itemCountry)
                                                <option value="{{$itemCountry->id}}">{{$itemCountry->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="form-group regions">
                                    <label for="region_id" class="col-md-4 control-label">{{__('Region')}}</label>
                                    <div class="col-md-8">
                                        <select class="selectpicker" name="home_region_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                            @foreach($region as $itemRegion)
                                                <option value="{{$itemRegion->id}}">{{$itemRegion->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group cities">
                                <label for="city_id" class="col-md-4 control-label">{{__('Locality')}}</label>
                                <div class="col-md-8 ">
                                    <!-- <input id="city" type="text" class="form-control autocomplete" name="city" required autofocus> -->
                                    <select class="selectpicker" name="home_city_id" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                        @foreach($city as $itemCity)
                                            <option value="{{$itemCity->id}}">{{$itemCity->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="street" class="col-md-4 control-label">{{__('Street')}}</label>
                                <div class="col-md-8">
                                    <input id="street" type="text" class="form-control" name="home_street" value="" required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="building_number" class="col-md-4 control-label">{{__('Buliding number')}}</label>
                                <div class="col-md-4">
                                    <input id="building_number" type="text" class="form-control" name="home_building_number" value="" required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="apartment_number" class="col-md-4 control-label">{{__('Apartment number')}}</label>
                                <div class="col-md-4">
                                    <input id="apartment_number" type="text" class="form-control" name="home_apartment_number" value="{{ $bcApplication->apartment_number ?? '' }}" autofocus>
                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('front') ? ' has-error' : '' }}">
                                <label for="address_card" class="col-md-4 control-label">{{__('Address card')}}</label>

                                <div class="col-md-6">
                                    <label class="btn btn-xs btn-default btn-upload-file" for="address_card">{{__('Selected file')}}</label>
                                    <input style="visibility:hidden;" id="address_card" type="file" class="form-control" name="address_card" value="" autofocus>
                                    @if ($errors->has('address_card'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_card') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" id="sendButton">
                                        {{__("Send")}}
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

