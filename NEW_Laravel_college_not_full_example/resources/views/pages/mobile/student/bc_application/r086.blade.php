@extends('student.bc_application.main')

@section('part')
<div class="form-group">
    <label for="r086" class="control-label">{{__('Reference 086')}}</label>
    <div class="col-12">
        <student-document-option name="r086" v-bind:active="{{$bcApplication->r086_photo ? 'true' : 'false'}}">
            <div class="col-md-7 no-padding-left" style="margin-bottom: 10px;">
                <div class="col-12 no-padding" style="margin-bottom: 10px;">
                    <div class="col-12">
                        <label>{{ __('Front side') }}</label>
                    </div>
                    <div class="col-md-6 col-xs-6 no-padding-left">
                        <img id="r063-img" style="width:100%; margin-bottom:10px; margin-top:10px; display:none;" />
                    </div>
                    <input id="r086" type="file" class="form-control" @change="checkImageValid('r086')" name="r086" value="{{ old('r086') }}" required autofocus>
                </div>
                <div class="col-12 no-padding">
                    <div class="col-12">
                        <label>{{ __('Back side') }}</label>
                    </div>
                    <div class="col-md-6 col-xs-6 no-padding-left">
                        <img id="r086-back-img" style="width:100%; margin-bottom:10px; margin-top:10px; display:none;" />
                    </div>
                    <input id="r086_back" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" @change="checkImageValid('r086_back')" name="r086_back" value="{{ old('r086_back') }}" required autofocus>
                </div>
            </div>
        </student-document-option>
    </div>
</div>
@endsection
