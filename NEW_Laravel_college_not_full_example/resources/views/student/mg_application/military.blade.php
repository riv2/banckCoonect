@extends('student.mg_application.main')

@section('part')
<div class="form-group">
    <label for="military" class="control-label">{{__('Military enlistment office')}}</label>
    <div class="col-md-12">
        <student-document-option name="military">
            <div class="col-md-12 no-padding-left">
                <input id="military" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('military')" class="form-control" name="military" value="{{ old('military') }}" autofocus>
            </div>
        </student-document-option>
    </div>
</div>
@endsection