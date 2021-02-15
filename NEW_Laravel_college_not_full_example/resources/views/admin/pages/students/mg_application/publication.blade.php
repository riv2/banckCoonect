<?php
$hasRightEdit = \App\Services\Auth::user()->hasRight('students', 'edit')
?>

@foreach($student->mgApplication->publications as $k => $publication)
    <div class="form-group subform">
        <view-text label="{{__('Type')}}">{{__( str_replace('_', ' ', ucfirst($publication->type)))}}</view-text>
        <view-text label="{{__('Name')}}">{{$publication->name}}</view-text>
        <view-text label="{{__('Placement')}}">{{$publication->place}}</view-text>
        <view-text label="{{__('Year of publication')}}">{{$publication->year}}</view-text>
        <view-text label="{{__('Issue number')}}">{{$publication->issue_number}}</view-text>

        @if($publication->file_name)
            <div class="col-md-2">
                <label class="pull-right text-right">Прикрепленный файл</label>
            </div>
            <div class="col-md-10">
                <a target="_blank" href="/images/uploads/student_publication/{{$publication->file_name}}-b.jpg">
                    <img src="/images/uploads/student_publication/{{$publication->file_name}}-s.jpg" class="doc-preview" />
                </a>
            </div>
        @endif

        <view-text label="{{__('Collaborators')}}">{{$publication->colleagues}}</view-text>
        <view-text label="{{__('Publication language')}}">{{$publication->lang}}</view-text>
        <view-text label="{{__('Availability of ISBN')}}">{{$publication->isbn}}</view-text>
    </div>
@endforeach