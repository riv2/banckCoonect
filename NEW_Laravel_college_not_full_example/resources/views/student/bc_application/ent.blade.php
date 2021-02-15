@extends('student.bc_application.main')

@section('part')
<div class="form-group">
    <label class="col-md-4 control-label">{{__('ENT certificate data')}}</label>
    <div class="col-md-8">
        <student-document-option name="ent" v-bind:active="true">
            <div class="col-md-12 subform">

                <div class="form-group">
                    <label for="ikt" class="control-label">{{__('IKT number')}}</label>
                    <div id="load-ent" class="col-md-8 fade"> <img style="width: 100%;display:none;" src="{{ URL::asset('assets/img/load.gif') }}" /></div>
                    <input id="ikt" type="text" class="form-control" name="ikt" value="{{ $bcApplication->ikt }}" autofocus maxlength="9">
                </div>

                <div class="form-group">
                    <label for="iin" class="control-label">{{__('IIN number')}}</label>
                    <input id="iin" type="hidden" name="iin" value="{{$profile->iin}}">
                    <input id="idTestType" type="hidden" name="idTestType">
                    <p>{{ chunk_split($profile->iin, 3, ' ') }}</p>
                </div>
                <div class="col-md-12">
                    <div id="ENTResult"></div>
                </div>
            </div>
        </student-document-option>
    </div>
</div>
@endsection