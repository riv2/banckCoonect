@extends('student.bc_application.main')

@section('part')
<div class="form-group">
    <label for="r063" class="control-label">{{__('Reference 063')}}</label>
    <div class="col-12">
        <student-document-option name="r063" v-bind:active="{{$bcApplication->r063_photo ? 'true' : 'false'}}">
            <div class="col-md-7 no-padding-left">
                <div class="col-md-6 col-xs-6 no-padding-left">
                    <img id="r063-img" style="width:100%; margin-bottom:10px; margin-top:10px; display:none;" />
                </div>
                <input id="r063" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" @change="checkImageValid('r063')" name="r063" value="{{ old('r063') }}" required autofocus>
            </div>
        </student-document-option>
    </div>
</div>
@endsection
