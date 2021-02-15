@extends('student.mg_application.main')

@section('part')
<div class="form-group">
    <label for="r086" class="control-label">{{__('Reference 086')}}</label>
    <div class="col-12">
        <student-document-option name="r086">
            <div class="col-md-7  no-padding-left" style="margin-bottom: 10px;">
                <div class="col-12 no-padding" style="margin-bottom: 10px;">
                    <label>{{ __('Front side') }}</label>
                    <input id="r086" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('r086')" class="form-control" name="r086" value="{{ old('r086') }}" required autofocus>
                </div>
                <div class="col-12 no-padding">
                    <label>{{ __('Back side') }}</label>
                    <input id="r086_back" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('r086_back')" class="form-control" name="r086_back" value="{{ old('r086_back') }}" required autofocus>
                </div>
            </div>
        </student-document-option>
    </div>
</div>
@endsection