@extends('student.mg_application.main')

@section('part')
<div class="form-group">
    <label for="military" class="control-label">{{__('Certificate confirming English language')}}</label>
    <div class="col-12">
        <student-document-option name="eng_certificate">
            <div class="col-12 subform">
                <div class="form-group">
                    <label for="eng_certificate_number" class="control-label">{{__('Number')}}</label>
                    <div class="col-md-8">
                        <input id="eng_certificate_number" type="text" class="form-control" name="eng_certificate_number" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="eng_certificate_series" class="control-label">{{__('Series')}}</label>
                    <div class="col-md-8">
                        <input id="eng_certificate_series" type="text" class="form-control" name="eng_certificate_series" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="eng_certificate_date" class="control-label">{{__('Issue date')}}</label>
                    <div class="col-md-8">
                        <input id="eng_certificate_date" type="date" class="form-control" name="eng_certificate_date" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="eng_certificate_photo" class="control-label">{{__('Photo certificate')}}</label>
                    <div class="col-md-8">
                        <input id="eng_certificate_photo" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('eng_certificate_photo')" class="form-control" name="eng_certificate_photo" required autofocus>
                    </div>
                </div>
            </div>
        </student-document-option>
    </div>
</div>
@endsection