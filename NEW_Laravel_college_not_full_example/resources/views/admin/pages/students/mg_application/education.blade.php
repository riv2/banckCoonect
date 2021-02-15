<?php
$hasRightEdit = \App\Services\Auth::user()->hasRight('students', 'edit')
?>

@if( true )

    <div class="col-md-12 form-group">
        <div class="col-md-2 ">
            <label class="pull-right text-right">{{__('Number')}}</label>
        </div>
        <div class="col-md-4">
            <input  @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="mgApplication[numeducation]" value="{{ $student->mgApplication->numeducation ?? '' }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2 ">
            <label class="pull-right text-right">{{__('Series')}}</label>
        </div>
        <div class="col-md-4">
            <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="mgApplication[sereducation]" value="{{ $student->mgApplication->sereducation ?? '' }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2 ">
            <label class="pull-right text-right">{{__('Institution name')}}</label>
        </div>
        <div class="col-md-10">
            <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="mgApplication[nameeducation]" value="{{ $student->mgApplication->nameeducation ?? '' }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2 ">
            <label class="pull-right text-right">{{__('Issue date')}}</label>
        </div>
        <div class="col-md-3">
            <input @if(!$hasRightEdit) disabled @endif type="date" class="form-control" name="mgApplication[dateeducation]" value="{{ $student->mgApplication->dateeducation ?? '' }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2">
            <label class="pull-right text-right">{{__('City of issue')}}</label>
        </div>
        <div class="col-md-10">
            <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="mgApplication[cityeducation]" value="{{ $student->mgApplication->cityeducation ?? ''}}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2">
            <label class="pull-right text-right">{{__('Type of education')}}</label>
        </div>
        <div class="col-md-10">
            <select @if(!$hasRightEdit) disabled @endif class="form-control" name="mgApplication[education]">
                <option @if( \App\MgApplications::EDUCATION_HIGH_SCHOOL == $student->mgApplication->education ) selected @endif value="{{ \App\MgApplications::EDUCATION_HIGH_SCHOOL }}" > {{__( \App\MgApplications::EDUCATION_HIGH_SCHOOL )}} </option>
                <option @if( \App\MgApplications::EDUCATION_VOCATIONAL_EDUCATION == $student->mgApplication->education ) selected @endif value="{{ \App\MgApplications::EDUCATION_VOCATIONAL_EDUCATION }}" > {{__( \App\MgApplications::EDUCATION_VOCATIONAL_EDUCATION )}} </option>
                <option @if( \App\MgApplications::EDUCATION_BACHELOR == $student->mgApplication->education ) selected @endif value="{{ \App\MgApplications::EDUCATION_BACHELOR }}" > {{__( \App\MgApplications::EDUCATION_BACHELOR )}} </option>
                <option @if( \App\MgApplications::EDUCATION_HIGHER == $student->mgApplication->education ) selected @endif value="{{ \App\MgApplications::EDUCATION_HIGHER }}" > {{__( \App\MgApplications::EDUCATION_HIGHER )}} </option>
            </select>
        </div>
    </div>

@else
    <view-text label="{{__('Number')}}">{{$student->mgApplication->numeducation}}</view-text>
    <view-text label="{{__('Number')}}">{{$student->mgApplication->numeducation}}</view-text>
    <view-text label="{{__('Series')}}">{{$student->mgApplication->sereducation}}</view-text>
    <view-text label="{{__('Institution name')}}">{{$student->mgApplication->nameeducation ?? ''}}</view-text>
    <view-date label="{{__('Issue date')}}">{{$student->mgApplication->dateeducation}}</view-date>
    <view-text label="{{__('City of issue')}}">{{$student->mgApplication->cityeducation ?? ''}}</view-text>
    <view-text label="{{__('Education')}}">{{ __($student->mgApplication->education) }}</view-text>
@endif
{{--
<view-doc label="{{__('Diploma')}}"
          name="mgApplication[diploma_photo]"
          @if($student->studentProfile->diploma_photo)
          status="{{$student->studentProfile->diploma_photo->status}}"
          file-name="{{$student->studentProfile->diploma_photo->getPublicFileName()}}"
          delivered="{{ (bool)$student->studentProfile->diploma_photo->delivered }}"
          deliveredname="mgApplication[delivered][diploma_photo]"
        @endif
></view-doc>
<view-doc label="{{__('Diploma supplement')}}"
          name="mgApplication[atteducation_status]"
          @if($student->studentProfile->doc_atteducation)
          status="{{$student->studentProfile->doc_atteducation->status}}"
          file-name="{{$student->studentProfile->doc_atteducation->getPublicFileName()}}"
          delivered="{{ (bool)$student->studentProfile->doc_atteducation->delivered }}"
          deliveredname="mgApplication[delivered][atteducation_status]"
          @endif
></view-doc>
<view-doc label="{{__('Diploma supplement back')}}"
          name="mgApplication[atteducation_status_back]"
          @if($student->studentProfile->doc_atteducation_back)
          status="{{$student->studentProfile->doc_atteducation_back->status}}"
          file-name="{{$student->studentProfile->doc_atteducation_back->getPublicFileName()}}"
          delivered="{{ (bool)$student->studentProfile->doc_atteducation_back->delivered }}"
          deliveredname="mgApplication[delivered][atteducation_status_back]"
        @endif
></view-doc>
--}}
@if($student->mgApplication->bceducation == 'vocational_education' || $student->mgApplication->bceducation == 'secondary_special')
    <view-text label="{{__('Specialty')}}">{{$student->mgApplication->eduspecialty}}</view-text>
    <view-text label="{{__('Type of educational institution')}}">{{__(ucfirst($student->mgApplication->typevocational))}}</view-text>
@endif

@if($student->mgApplication->bceducation == 'bachelor' || $student->mgApplication->bceducation == 'higher')
    <view-text label="{{__('Degree')}}">{{$student->mgApplication->edudegree}}</view-text>
@endif

<view-text label="{{__('Issued in Kazakhstan')}}">
    @if($student->mgApplication->kzornot == true)
        Да
    @else
        Нет
    @endif
</view-text>

@if(!$student->mgApplication->kzornot)
    <view-block label="Нострификация">
        <div class="form-group subform" >
{{--
            <view-doc label="{{__('Документ')}}"
                      name="mgApplication[nostrification_status]"
                      @if($student->studentProfile->doc_nostrificationattach)
                      status="{{$student->studentProfile->doc_nostrification->status}}"
                      file-name="{{$student->studentProfile->doc_nostrificationattach->getPublicFileName()}}"
                      delivered="{{ (bool)$student->studentProfile->doc_nostrificationattach->delivered }}"
                      deliveredname="mgApplication[delivered][nostrification_status]"
                    @endif
            ></view-doc>
            --}}
            @if($student->mgApplication->nostrification)
                <view-text label="{{__('Nostrification')}}">{{$student->mgApplication->nostrification}}</view-text>
            @endif
        </div>
    </view-block>
@endif