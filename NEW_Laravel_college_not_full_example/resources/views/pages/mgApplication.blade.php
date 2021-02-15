@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">mg. Application page</div>

                <div class="panel-body">

                	<form class="form-horizontal" method="POST" action="{{ route('userProfile') }}">
{{--
                		<div class="form-group{{ $errors->has('nationality') ? ' has-error' : '' }}">
                            <label for="nationality" class="col-md-4 control-label">{{__('Nationality')}}</label>
                            <div class="col-md-6">
				                    <select class="selectpicker" multiple name="nationality" data-live-search="true" data-size="5" title="{{ __('Please select') }}">
										@foreach($fieldLists['nationality'] as $item)
											<option value="{{$item->id}}">
                                                @if(App::isLocale('en'))
                                                {{$item->name}}
                                                @endif
                                                @if(App::isLocale('ru'))
                                                {{$item->name_ru}}
                                                @endif
                                                @if(App::isLocale('kz'))
                                                {{$item->name_kz}}
                                                @endif
                                            </option>
										@endforeach
									</select>

                                @if ($errors->has('nationality'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nationality') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
--}}
                        <div class="form-group{{ $errors->has('citizenship') ? ' has-error' : '' }}">
                            <label for="citizenship" class="col-md-4 control-label">{{__('Citizenship')}}</label>
                            <div class="col-md-6">
				                    <select class="selectpicker" multiple name="citizenship" data-live-search="true" data-size="5" title="{{ __('Please select') }}">
										@foreach($fieldLists['citizenship'] as $item)
											<option value="{{$item->id}}">{{$item->name}}</option>
										@endforeach
									</select>

                                @if ($errors->has('citizenship'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('citizenship') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('familyStatus') ? ' has-error' : '' }}">
                            <label for="familyStatus" class="col-md-4 control-label">{{__('Family status')}}</label>
                            <div class="col-md-6">
				                    <select class="selectpicker" multiple name="familyStatus" data-live-search="true" data-size="5" title="{{ __('Please select') }}">
										@foreach($fieldLists['familyStatus'] as $item)
											<option value="{{$item->id}}">{{$item->name}}</option>
										@endforeach
									</select>

                                @if ($errors->has('familyStatus'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('familyStatus') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="col-md-12">{{_("Address")}}</div>

                        <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }}">
                            <label for="region" class="col-md-4 control-label">{{__('Region')}}</label>

                            <div class="col-md-6">
                                <input id="region" type="text" class="form-control" name="region" value="{{ old('region') }}" required autofocus>

                                @if ($errors->has('region'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('region') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('locality') ? ' has-error' : '' }}">
                            <label for="locality" class="col-md-4 control-label">{{__('Locality')}}</label>

                            <div class="col-md-6">
                                <input id="locality" type="text" class="form-control" name="locality" value="{{ old('locality') }}" required autofocus>

                                @if ($errors->has('locality'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('locality') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('street') ? ' has-error' : '' }}">
                            <label for="street" class="col-md-4 control-label">{{__('Street')}}</label>

                            <div class="col-md-6">
                                <input id="street" type="text" class="form-control" name="street" value="{{ old('street') }}" required autofocus>

                                @if ($errors->has('street'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('street') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('bulidingnum') ? ' has-error' : '' }}">
                            <label for="bulidingnum" class="col-md-4 control-label">{{__('Buliding number')}}</label>

                            <div class="col-md-6">
                                <input id="bulidingnum" type="text" class="form-control" name="bulidingnum" value="{{ old('bulidingnum') }}" required autofocus>

                                @if ($errors->has('bulidingnum'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('bulidingnum') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('apartmentnum') ? ' has-error' : '' }}">
                            <label for="apartmentnum" class="col-md-4 control-label">{{__('Apartment number')}}</label>

                            <div class="col-md-6">
                                <input id="apartmentnum" type="text" class="form-control" name="apartmentnum" value="{{ old('apartmentnum') }}" required autofocus>

                                @if ($errors->has('apartmentnum'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apartmentnum') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group{{ $errors->has('residenceregistration') ? ' has-error' : '' }}">
                            <label for="residenceregistration" class="col-md-4 control-label">{{__('Residence registration')}}</label>

                            <div class="col-md-6">
                                <input id="residenceregistration" type="file" class="form-control" name="residenceregistration" value="{{ old('residenceregistration') }}" required autofocus>

                                @if ($errors->has('residenceregistration'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('residenceregistration') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('rectal') ? ' has-error' : '' }}">
                            <label for="rectal" class="col-md-4 control-label">{{__('Military enlistment office')}}</label>

                            <div class="col-md-6">
                                <input id="rectal" type="file" class="form-control" name="rectal" value="{{ old('rectal') }}" required autofocus>

                                @if ($errors->has('rectal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('rectal') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('r086') ? ' has-error' : '' }}">
                            <label for="r086" class="col-md-4 control-label">{{__('Reference 086')}}</label>

                            <div class="col-md-6">
                                <input id="r086" type="file" class="form-control" name="r086" value="{{ old('r086') }}" required autofocus>

                                @if ($errors->has('r086'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('r086') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('r063') ? ' has-error' : '' }}">
                            <label for="r063" class="col-md-4 control-label">{{__('Reference 063')}} </label>

                            <div class="col-md-6">
                                <input id="r063" type="file" class="form-control" name="r063" value="{{ old('r063') }}" required autofocus>

                                @if ($errors->has('r063'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('r063') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12">{{_("Certificate confirming English language")}} </div>

                        <div class="form-group">
                            <label for="haveEng" class="col-md-4 control-label">{{__('Certificate')}} </label>
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="radio" name="haveEng" id="havecertificate" {{ old('haveEng') ? 'checked' : '' }}> 
                                        <label for="havecertificate">{{__('Have certificate')}}</label><br />
                                        <input type="radio" name="haveEng" id="donthavecertificate" {{ old('haveEng') ? 'checked' : '' }}> 
                                        <label for="donthavecertificate">{{__('Don\'t have certificate')}}</label>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('numsertificate') ? ' has-error' : '' }}">
                            <label for="numsertificate" class="col-md-4 control-label">{{__('Number')}}</label>

                            <div class="col-md-6">
                                <input id="numsertificate" type="text" class="form-control" name="numsertificate" value="{{ old('numsertificate') }}" required autofocus>

                                @if ($errors->has('numsertificate'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('numsertificate') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('sersertificate') ? ' has-error' : '' }}">
                            <label for="sersertificate" class="col-md-4 control-label">{{__('Series')}}</label>

                            <div class="col-md-6">
                                <input id="sersertificate" type="text" class="form-control" name="sersertificate" value="{{ old('sersertificate') }}" required autofocus>

                                @if ($errors->has('sersertificate'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sersertificate') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('datesertificate') ? ' has-error' : '' }}">
                            <label for="datesertificate" class="col-md-4 control-label">{{__('Issue date')}}</label>

                            <div class="col-md-6">
                                <input id="datesertificate" type="date" class="form-control" name="datesertificate" value="{{ old('datesertificate') }}" required autofocus>

                                @if ($errors->has('datesertificate'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('datesertificate') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    	
                    	<div class="form-group{{ $errors->has('certificateEng') ? ' has-error' : '' }}">
                            <label for="certificateEng" class="col-md-4 control-label">{{__('Photo certificate')}} </label>

                            <div class="col-md-6">
                                <input id="certificateEng" type="file" class="form-control" name="certificateEng" value="{{ old('certificateEng') }}" required autofocus>

                                @if ($errors->has('certificateEng'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('certificateEng') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('mgeducation') ? ' has-error' : '' }}">
                            <label for="mgeducation" class="col-md-4 control-label">{{__('Education')}}</label>
                            <div class="col-md-6">
                                    <select class="selectpicker" multiple name="mgeducation" data-live-search="true" data-size="5" title="{{ __('Please select') }}">
                                       <option value="high_school">{{__('high school')}}</option>
                                       <option value="bachelor">{{__('Bachelor')}}</option>
                                       <option value="bagistracy">{{__('Magistracy')}}</option>
                                    </select>

                                @if ($errors->has('mgeducation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mgeducation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('nostrification') ? ' has-error' : '' }}">
                            <label for="nostrification" class="col-md-4 control-label">{{__('Nostrification')}}</label>

                            <div class="col-md-6">
                                <input id="nostrification" type="text" class="form-control" name="nostrification" value="{{ old('nostrification') }}" required autofocus>

                                @if ($errors->has('nostrification'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nostrification') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('perslist') ? ' has-error' : '' }}">
                            <label for="perslist" class="col-md-4 control-label">{{__('Personal list')}} </label>

                            <div class="col-md-6">
                                <input id="perslist" type="file" class="form-control" name="perslist" value="{{ old('perslist') }}" required autofocus>

                                @if ($errors->has('perslist'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('perslist') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpubl') ? ' has-error' : '' }}">
                            <label for="scienpubl" class="col-md-4 control-label">{{__('Scientific publications')}}</label>
                            <div class="col-md-6">
                                    <select class="selectpicker" multiple name="scienpubl" data-live-search="true" data-size="5" title="{{ __('Please select') }}">
                                       <option value="research_article">{{__('Research Article')}}</option>
                                       <option value="publication">{{__('Publication')}}</option>
                                       <option value="monograph">{{__('Monograph')}}</option>
                                       <option value="tutorial">{{__('Tutorial')}}</option>
                                       <option value="glossary">{{__('Glossary')}}</option>
                                       <option value="directory">{{__('Directory')}}</option>
                                       <option value="other">{{__('Other')}}</option>
                                    </select>

                                @if ($errors->has('scienpubl'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpubl') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpublname') ? ' has-error' : '' }}">
                            <label for="scienpublname" class="col-md-4 control-label">{{__('Name')}}</label>

                            <div class="col-md-6">
                                <input id="scienpublname" type="text" class="form-control" name="scienpublname" value="{{ old('scienpublname') }}" required autofocus>

                                @if ($errors->has('scienpublname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpublname') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpublplace') ? ' has-error' : '' }}">
                            <label for="scienpublplace" class="col-md-4 control-label">{{__('Placement')}}</label>

                            <div class="col-md-6">
                                <input id="scienpublplace" type="text" class="form-control" name="scienpublplace" value="{{ old('scienpublplace') }}" required autofocus>

                                @if ($errors->has('scienpublplace'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpublplace') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpublyear') ? ' has-error' : '' }}">
                            <label for="scienpublyear" class="col-md-4 control-label">{{__('Year of publication')}}</label>
                            <div class="col-md-6">
                                    {{ Form::selectYear('year', date('Y'), date('Y')-28, isset($scienpublyear->year)?$scienpublyear->year:'', ['class' => 'selectpicker', 'data-size'=>'5', 'title' => __('Please select')] ) }}

                                @if ($errors->has('scienpublyear'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpublyear') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpublnum') ? ' has-error' : '' }}">
                            <label for="scienpublnum" class="col-md-4 control-label">{{__('Issue number')}}</label>

                            <div class="col-md-6">
                                <input id="scienpublnum" type="text" class="form-control" name="scienpublnum" value="{{ old('scienpublnum') }}" autofocus>

                                @if ($errors->has('scienpublnum'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpublnum') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpublatt') ? ' has-error' : '' }}">
                            <label for="scienpublatt" class="col-md-4 control-label">{{__('Attach file')}} </label>

                            <div class="col-md-6">
                                <input id="scienpublatt" type="file" class="form-control" name="scienpublatt" value="{{ old('scienpublatt') }}" required autofocus>

                                @if ($errors->has('scienpublatt'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpublatt') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpublcoll') ? ' has-error' : '' }}">
                            <label for="scienpublcoll" class="col-md-4 control-label">{{__('Collaborators')}}</label>

                            <div class="col-md-6">
                                <input id="scienpublcoll" type="text" class="form-control" name="scienpublcoll" value="{{ old('scienpublcoll') }}" autofocus>

                                @if ($errors->has('scienpublcoll'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpublcoll') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpubllang') ? ' has-error' : '' }}">
                            <label for="scienpubllang" class="col-md-4 control-label">{{__('git pull')}}</label>
                            <div class="col-md-6">
                                    <select class="selectpicker" multiple name="scienpubllang" data-live-search="true" data-size="5" title="{{ __('Please select') }}">
                                       <option value="scienpublkaz">{{__('Kaz')}}</option>
                                       <option value="scienpublrus">{{__('Rus')}}</option>
                                       <option value="scienpubleng">{{__('Eng')}}</option>
                                       <option value="scienpublother">{{__('Other')}}</option>
                                    </select>

                                @if ($errors->has('scienpubllang'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpubllang') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('scienpublisbn') ? ' has-error' : '' }}">
                            <label for="scienpublisbn" class="col-md-4 control-label">{{__('Availability of ISBN')}}</label>

                            <div class="col-md-6">
                                <input id="scienpublisbn" type="text" class="form-control" name="scienpublisbn" value="{{ old('scienpublisbn') }}" autofocus>

                                @if ($errors->has('scienpublisbn'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scienpublisbn') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('emplhistory') ? ' has-error' : '' }}">
                            <label for="emplhistory" class="col-md-4 control-label">{{__('Employment history')}} </label>

                            <div class="col-md-6">
                                <input id="emplhistory" type="file" class="form-control" name="emplhistory" value="{{ old('emplhistory') }}" required autofocus>

                                @if ($errors->has('emplhistory'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('emplhistory') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection