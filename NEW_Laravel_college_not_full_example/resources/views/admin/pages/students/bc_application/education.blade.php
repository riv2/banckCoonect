<?php
$hasRightEdit = \App\Services\Auth::user()->hasRight('students', 'edit')
?>

@if( true )
    <div class="col-md-12 form-group">
        <div class="col-md-2 ">
            <label class="pull-right text-right">{{__('Number')}}</label>
        </div>
        <div class="col-md-4">
            <input type="text" @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[numeducation]" value="{{ $student->bcApplication->numeducation ?? '' }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2 ">
            <label class="pull-right text-right">{{__('Series')}}</label>
        </div>
        <div class="col-md-4">
            <input type="text" @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[sereducation]" value="{{ $student->bcApplication->sereducation ?? '' }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2 ">
            <label class="pull-right text-right">{{__('Institution name')}}</label>
        </div>
        <div class="col-md-10">
            <input type="text" @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[nameeducation]" value="{{ $student->bcApplication->nameeducation ?? '' }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2 ">
            <label class="pull-right text-right">Дата выдачи документа об образование</label>
        </div>
        <div class="col-md-3">
            <input type="date" @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[dateeducation]" value="{{ date('Y-m-d', strtotime($student->bcApplication->dateeducation ?? time()) ) }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2">
            <label class="pull-right text-right">{{__('City of issue')}}</label>
        </div>
        <div class="col-md-10">
            <input type="text" @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[cityeducation]" value="{{ $student->bcApplication->cityeducation ?? '' }}" autocomplete="false" />
        </div>
    </div>

    <div class="col-md-12 form-group">
        <div class="col-md-2">
            <label class="pull-right text-right">{{__('Type of education')}}</label>
        </div>
        <div class="col-md-10">
            <select @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[education]">
                <option @if( \App\BcApplications::EDUCATION_HIGH_SCHOOL == $student->bcApplication->education ) selected @endif value="{{ \App\BcApplications::EDUCATION_HIGH_SCHOOL }}" > {{__( \App\BcApplications::EDUCATION_HIGH_SCHOOL )}} </option>
                <option @if( \App\BcApplications::EDUCATION_VOCATIONAL_EDUCATION == $student->bcApplication->education ) selected @endif value="{{ \App\BcApplications::EDUCATION_VOCATIONAL_EDUCATION }}" > {{__( \App\BcApplications::EDUCATION_VOCATIONAL_EDUCATION )}} </option>
                <option @if( \App\BcApplications::EDUCATION_BACHELOR == $student->bcApplication->education ) selected @endif value="{{ \App\BcApplications::EDUCATION_BACHELOR }}" > {{__( \App\BcApplications::EDUCATION_BACHELOR )}} </option>
                <option @if( \App\BcApplications::EDUCATION_HIGHER == $student->bcApplication->education ) selected @endif value="{{ \App\BcApplications::EDUCATION_HIGHER }}" > {{__( \App\BcApplications::EDUCATION_HIGHER )}} </option>
            </select>
        </div>
    </div>

@else
    <view-text label="{{__('Number')}}">{{ $student->bcApplication->numeducation ?? '' }}</view-text>
    <view-text label="{{__('Series')}}">{{ $student->bcApplication->sereducation ?? '' }}</view-text>
    <view-text label="{{__('Institution name')}}">{{ $student->bcApplication->nameeducation ?? '' }}</view-text>
    <view-date label="{{__('Issue date')}}">{{ $student->bcApplication->dateeducation ?? '' }}</view-date>
    <view-text label="{{__('City of issue')}}">{{ $student->bcApplication->cityeducation ?? '' }}</view-text>
    <view-text label="{{__('Education')}}">{{ __($student->bcApplication->education) }}</view-text>
@endif








@if( $student->bcApplication->bceducation == 'vocational_education' || $student->bcApplication->bceducation == 'secondary_special' )
    <view-text label="{{__('Specialty')}}">{{$student->bcApplication->eduspecialty}}</view-text>
    <view-text label="{{__('Type of educational institution')}}">{{__(ucfirst($student->bcApplication->typevocational))}}</view-text>
@endif

@if( ($student->bcApplication->bceducation == 'bachelor') || ($student->bcApplication->bceducation == 'higher') )
    <view-text label="{{__('Degree')}}">{{$student->bcApplication->edudegree}}</view-text>
@endif

<view-text label="{{__('Issued in Kazakhstan')}}">
    @if( !empty($student->bcApplication) && $student->bcApplication->kzornot != 'false')
        Да
    @else
        Нет
    @endif
</view-text>

@if($student->bcApplication->kzornot == 'false')
    <view-block label="Нострификация">
        <div class="form-group subform" >
{{--
            <view-doc label="{{__('Документ')}}"
                      name="bcApplication[nostrification_status]"
                      @if($student->studentProfile->doc_nostrification)
                      status="{{$student->studentProfile->doc_nostrification->status}}"
                      file-name="{{$student->studentProfile->doc_nostrification->getPublicFileName()}}"
                      delivered="{{ (bool)$student->studentProfile->doc_nostrification->delivered === 1 }}"
                      deliveredname="bcApplication[delivered][nostrification_status]"
                      @endif
            ></view-doc>
            <view-doc label="{{__('Документ')}}"
                      name="bcApplication[con_confirm]"
                      @if($student->studentProfile->doc_con_confirm)
                      status="{{$student->studentProfile->doc_con_confirm->status}}"
                      file-name="{{$student->studentProfile->doc_con_confirm->getPublicFileName()}}"
                      delivered="{{ (bool)$student->studentProfile->doc_con_confirm->delivered === 1 }}"
                      deliveredname="bcApplication[delivered][con_confirm]"
                    @endif
            ></view-doc>
            --}}
            @if($student->bcApplication->nostrification)
                <view-text label="{{__('Nostrification')}}">{{$student->bcApplication->nostrification}}</view-text>
            @endif
        </div>
    </view-block>
@endif