@extends('student.mg_application.main')

@section('part')
<div class="form-group">
    <label for="r063" class="control-label">{{__('Reference 063')}}</label>
    <div class="col-12">
        <student-document-option name="r063">
            <div class="col-md-7 no-padding-left">
                <input id="r063" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('r063')" class="form-control" name="r063" value="{{ old('r063') }}" required autofocus>
            </div>
        </student-document-option>
    </div>
</div>
@endsection