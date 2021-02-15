@extends('student.bc_application.main')

@section('part')
<div class="form-group">
    <label for="military" class="control-label">{{__('Military enlistment office')}}</label>
    <div class="col-md-7">
        <student-document-option name="military" v-bind:active="{{$bcApplication->military_photo ? 'true' : 'false'}}">
            <div class="col-md-7 no-padding-left">
                <div class="col-md-6 col-xs-6 no-padding-left">
                    <img id="military-img" style="width:100%; margin-bottom:10px; margin-top:10px; display:none;" />
                </div>
                <input id="military" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="military" @change="checkImageValid('military')" value="{{ old('military') }}" required autofocus>
            </div>
        </student-document-option>
    </div>
</div>
@endsection
