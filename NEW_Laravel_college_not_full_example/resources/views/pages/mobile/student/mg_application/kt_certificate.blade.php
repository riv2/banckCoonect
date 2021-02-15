@extends('student.mg_application.main')

@section('part')
    <div class="form-group">
        <label for="military" class="control-label">{{__('KT certificate')}}</label>
        <student-document-option name="military" active="true">
            <div class="col-12 no-padding-left ">
                <input id="kt_number" placeholder="{{ __('Number') }}" type="text" class="form-control" name="kt_number" value="{{ old('kt_number') }}" required autofocus>
            </div>
        </student-document-option>
    </div>
@endsection
