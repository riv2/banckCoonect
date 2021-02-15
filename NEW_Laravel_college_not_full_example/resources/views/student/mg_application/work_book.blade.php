@extends('student.mg_application.main')

@section('part')
<div class="form-group">
    <label for="work_book" class="control-label">{{__('Employment history')}}</label>
    <div class="col-md-12">
        <student-document-option name="work_book">
            <div class="col-md-12 no-padding-left">
                <input id="work_book" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('work_book')" class="form-control" name="work_book" required autofocus>
            </div>
        </student-document-option>
    </div>
</div>
@endsection