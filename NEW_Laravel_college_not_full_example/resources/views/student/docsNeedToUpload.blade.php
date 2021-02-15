@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{__('Document list page')}}</div>
                    <div class="panel-body">
                        
                    {!! Form::open(array('url' => array( route('docsNeedToUploadPost') ),'role'=>'form','enctype' => 'multipart/form-data')) !!} 

                        <input type="hidden" name="type" value="{{$application->type}}">

                        @if(\App\Services\Auth::user()->bcApplication)
                        <div class="form-group col-md-12">
                            <label for="r063" class="col-md-4 control-label">{{__('Reference 063')}}</label>
                            <div class="col-md-7">
                                @if(isset($application->docs->r063_photo['filename']))
                                    @if( in_array($application->docs->r063_photo['status'], ['moderation', 'allow']) )
                                        <a href="{{$application->docs->r063_photo['path'].$application->docs->r063_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-success">✓ {{ __('Document uploaded') }}
                                        @if( $application->docs->r063_photo['status'] == 'allow' )
                                            {{ __('and allow') }}
                                        @endif
                                        </a>
                                    @else
                                        @if($application->docs->r063_photo['status'] == 'disallow')
                                            <a href="{{$application->docs->r063_photo['path'].$application->docs->r063_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-danger"> {{ __('Document disallow') }} </a>
                                        @endif
                                    @endif
                                @endif
                                <input id="r063" type="file" class="form-control col-md-6" name="r063">
                            </div>
                        </div>
                        @endif

                        <div class="form-group col-md-12">
                            <label for="residenceregistration" class="col-md-4 control-label">{{__('Residence registration')}}</label>
                            <div class="col-md-7">
                                @if(isset($application->docs->residence_registration_photo))
                                    @if( in_array($application->docs->residence_registration_photo['status'], ['moderation', 'allow']))
                                        <a href="{{$application->docs->residence_registration_photo['path'].$application->docs->residence_registration_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-success">✓ {{ __('Document uploaded') }}
                                        @if( $application->docs->residence_registration_photo['status'] == 'allow' )
                                            {{ __('and allow') }}
                                        @endif
                                        </a>
                                    @else
                                        @if($application->docs->residence_registration_photo['status'] == 'disallow')
                                            <a href="{{$application->docs->residence_registration_photo['path'].$application->docs->residence_registration_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-danger"> {{ __('Document disallow') }} </a>
                                        @endif
                                    @endif
                                @endif
                                <input id="residenceregistration" type="file" class="form-control" name="residenceregistration">

                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="military" class="col-md-4 control-label">{{__('Military enlistment office')}}</label>
                            <div class="col-md-7">
                                @if(isset($application->docs->military_photo))
                                    @if( in_array($application->docs->military_photo['status'], ['moderation', 'allow']) )
                                        <a href="{{$application->docs->military_photo['path'].$application->docs->military_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-success">✓ {{ __('Document uploaded') }}
                                        @if( $application->docs->military_photo['status'] == 'allow' )
                                            {{ __('and allow') }}
                                        @endif
                                        </a>
                                    @else
                                        @if($application->docs->military_photo['status'] == 'disallow')
                                            <a href="{{$application->docs->military_photo['path'].$application->docs->military_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-danger"> {{ __('Document disallow') }} </a>
                                        @endif
                                    @endif
                                @endif
                                <input id="military" type="file" class="form-control" name="military">

                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="r086" class="col-md-4 control-label">{{__('Reference 086')}}</label>
                            <div class="col-md-7 subform">

                                        <div class="col-md-12 no-padding" style="margin-bottom: 10px;">
                                            <label>{{ __('Front side') }}</label>
                                            @if(isset($application->docs->r086_photo))
                                                @if( in_array($application->docs->r086_photo['status'], ['moderation', 'allow']))
                                                    <a href="{{$application->docs->r086_photo['path'].$application->docs->r086_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-success">✓ {{ __('Document uploaded') }}
                                                    @if( $application->docs->r086_photo['status'] == 'allow' )
                                                        {{ __('and allow') }}
                                                    @endif
                                                    </a>
                                                @else
                                                    @if($application->docs->r086_photo['status'] == 'disallow')
                                                        <a href="{{$application->docs->r086_photo['path'].$application->docs->r086_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-danger"> {{ __('Document disallow') }} </a>
                                                    @endif
                                                @endif
                                            @endif
                                            <input id="r086" type="file" class="form-control" name="r086">
                                        </div>
                                        <div class="col-md-12 no-padding">
                                            <label>{{ __('Back side') }}</label>
                                            @if(isset($application->docs->r086_photo_back))
                                                @if( in_array($application->docs->r086_photo_back['status'], ['moderation', 'allow']) )
                                                    <a href="{{$application->docs->r086_photo_back['path'].$application->docs->r086_photo_back['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-success">✓ {{ __('Document uploaded') }}
                                                    @if( $application->docs->r086_photo_back['status'] == 'allow' )
                                                        {{ __('and allow') }}
                                                    @endif
                                                    </a>
                                                @else
                                                    @if($application->docs->r086_photo_back['status'] == 'disallow')
                                                        <a href="{{$application->docs->r086_photo_back['path'].$application->docs->r086_photo_back['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-danger"> {{ __('Document disallow') }} </a>
                                                    @endif
                                                @endif
                                            @endif
                                            <input id="r086_back" type="file" class="form-control" name="r086_back">
                                        </div>


                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="atteducation" class="col-md-4 control-label">{{__('Diploma supplement')}}</label>
                            <div class="col-md-7 subform">
                                    <div class="col-md-12 no-padding" style="margin-bottom: 10px;">
                                        <label>{{ __('Front side') }}</label>
                                        @if(isset($application->docs->atteducation_photo))
                                            @if( in_array($application->docs->atteducation_photo['status'], ['moderation', 'allow']) )
                                                <a href="{{$application->docs->atteducation_photo['path'].$application->docs->atteducation_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-success">✓ {{ __('Document uploaded') }}
                                                @if( $application->docs->atteducation_photo['status'] == 'allow' )
                                                    {{ __('and allow') }}
                                                @endif
                                                </a>
                                            @else
                                                @if($application->docs->atteducation_photo['status'] == 'disallow')
                                                    <a href="{{$application->docs->atteducation_photo['path'].$application->docs->atteducation_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-danger"> {{ __('Document disallow') }} </a>
                                                @endif
                                            @endif
                                        @endif
                                        <input id="atteducation" type="file" class="form-control" name="atteducation">
                                    </div>
                                    <div class="col-md-12 no-padding">
                                        <label>{{ __('Back side') }}</label>
                                        @if(isset($application->docs->atteducation_photo_back))
                                            @if( in_array($application->docs->atteducation_photo_back['status'], ['moderation', 'allow']) )
                                                <a href="{{$application->docs->atteducation_photo_back['path'].$application->docs->atteducation_photo_back['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-success">✓ {{ __('Document uploaded') }}
                                                @if( $application->docs->atteducation_photo_back['status'] == 'allow' )
                                                    {{ __('and allow') }}
                                                @endif
                                                </a>
                                            @else
                                                @if($application->docs->atteducation_photo_back['status'] == 'disallow')
                                                    <a href="{{$application->docs->atteducation_photo_back['path'].$application->docs->atteducation_photo_back['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-danger"> {{ __('Document disallow') }} </a>
                                                @endif
                                            @endif
                                        @endif
                                        <input id="atteducation_back" type="file" class="form-control" name="atteducation_back">
                                    </div>
                            </div>
                        </div>

                        @if(!$application->kzornot)
                        <div class="form-group col-md-12">
                            <label for="nostrificationattach" class="col-md-4 control-label">{{__('Nostrification')}}</label>
                            <div class="col-md-7">
                                @if(isset($application->docs->nostrificationattach_photo))
                                    @if( in_array($application->docs->nostrificationattach_photo['status'], ['moderation', 'allow']) )
                                        <a href="{{$application->docs->nostrificationattach_photo['path'].$application->docs->nostrificationattach_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-success">✓ {{ __('Document uploaded') }}
                                            @if( $application->docs->nostrificationattach_photo['status'] == 'allow' )
                                                 {{ __('and allow') }}
                                            @endif
                                        </a>
                                    @else
                                        @if($application->docs->nostrificationattach_photo['status'] == 'disallow')
                                            <a href="{{$application->docs->nostrificationattach_photo['path'].$application->docs->nostrificationattach_photo['filename']}}-b.jpg" target="_blank" class="col-xs-12 btn alert-danger"> {{ __('Document disallow') }} </a>
                                        @endif
                                    @endif
                                @endif
                                <input id="nostrificationattach" type="file" class="form-control" name="nostrificationattach">

                            </div>
                        </div>
                        @endif

                        <input type="submit" value="{{ __('Upload files') }}" class="btn btn-info">


                    {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
