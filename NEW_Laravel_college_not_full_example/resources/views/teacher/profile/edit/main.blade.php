
<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
    <label for="photo" class="col-md-4 control-label">{{__('Photo')}}</label>

    <div class="col-md-6">

        @if($profileTeacher->photo)
            <img
                    src="{{ \App\Services\Avatar::getPublicPath($profileTeacher->photo) ?? '' }}"
                    alt=" {{ $profileTeacher->fio ?? '' }}"
                    style="height: 150px"
            />
        @endif

        <input id="photo" type="file" class="form-control" name="photo" value="{{ isset($profileTeacher->photo)? $profileTeacher->photo:'' }}"
               @if(!$profileTeacher->photo)
               required
               @endif
               autofocus maxlength="12">

        @if ($errors->has('photo'))
            <span class="help-block">
                                        <strong>{{ $errors->first('photo') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
    <label for="iin" class="col-md-4 control-label">{{__('ITN')}}</label>

    <div class="col-md-6">
        <input id="iin" type="text" class="form-control" name="iin"
               value="{{ isset($profileTeacher->iin)? $profileTeacher->iin:'' }}" required autofocus maxlength="12">

        @if ($errors->has('iin'))
            <span class="help-block">
                                        <strong>{{ $errors->first('iin') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('fio') ? ' has-error' : '' }}">
    <label for="fio" class="col-md-4 control-label">{{__('Full name')}}</label>

    <div class="col-md-6">
        <input id="fio" type="text" class="form-control" name="fio" value="{{ isset($profileTeacher->fio)? $profileTeacher->fio:''  }}" required autofocus maxlength="50">

        @if ($errors->has('fio'))
            <span class="help-block">
                                        <strong>{{ $errors->first('fio') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('bdate') ? ' has-error' : '' }}">
    <label for="bdate" class="col-md-4 control-label">{{__('Birth date')}} </label>

    <div class="col-md-6">
        <input id="bdate" type="date" class="form-control" name="bdate" value="{{ !empty($profileTeacher->bdate) ? $profileTeacher->bdate->format('Y-m-d') : '' }}" required autofocus>

        @if ($errors->has('bdate'))
            <span class="help-block">
                                        <strong>{{ $errors->first('bdate') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="doctype" class="col-md-4 control-label">{{__('Document type')}}</label>
    <div class="col-md-6">
        <div class="checkbox">
            <label>
                <input type="radio" name="doctype" id="pass" {{ $profileTeacher->doctype == \App\Teacher\ProfileTeacher::DOCTYPE_PASS ? 'checked' : '' }} value="pass">
                <label for="pass">{{__('Passport')}}</label><br />
                <input type="radio" name="doctype" id="id" checked {{ $profileTeacher->doctype == \App\Teacher\ProfileTeacher::DOCTYPE_ID ? 'checked' : '' }} value="id">
                <label for="id">{{__('ID')}}</label>
            </label>
        </div>
    </div>
</div>

<div class="form-group{{ $errors->has('docnumber') ? ' has-error' : '' }}">
    <label for="docnumber" class="col-md-4 control-label">{{__('Document number')}}</label>

    <div class="col-md-6">
        <input id="docnumber" type="text" class="form-control" name="docnumber" value="{{ isset($profileTeacher->docnumber)? $profileTeacher->docnumber:'' }}" required autofocus maxlength="9">

        @if ($errors->has('docnumber'))
            <span class="help-block">
                                        <strong>{{ $errors->first('docnumber') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('issuing') ? ' has-error' : '' }}">
    <label for="issuing" class="col-md-4 control-label">{{__('Issuing authority')}}</label>

    <div class="col-md-6">
        <input id="issuing" type="text" class="form-control" name="issuing" value="{{ isset($profileTeacher->issuing)? $profileTeacher->issuing:'' }}" required autofocus>

        @if ($errors->has('issuing'))
            <span class="help-block">
                                        <strong>{{ $errors->first('issuing') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('issuedate') ? ' has-error' : '' }}">
    <label for="issuedate" class="col-md-4 control-label">{{__('Date of issue')}}</label>

    <div class="col-md-6">
        <input id="issuedate" type="date" class="form-control" name="issuedate" value="{{ !empty($profileTeacher->issuedate) ? $profileTeacher->issuedate->format('Y-m-d') : '' }}" required autofocus>

        @if ($errors->has('issuedate'))
            <span class="help-block">
                                        <strong>{{ $errors->first('issuedate') }}</strong>
                                    </span>
        @endif
    </div>
</div>


<div class="form-group">
    <label for="sex" class="col-md-4 control-label">{{__('Sex')}}</label>
    <div class="col-md-6">
        <div class="checkbox">
            <label>
                <input type="radio" name="sex" id="male" {{ $profileTeacher->sex == \App\Teacher\ProfileTeacher::SEX_MALE ? 'checked' : '' }} value="male">
                <label for="male">{{__('Male')}}</label><br />
                <input type="radio" name="sex" id="female" {{ $profileTeacher->sex == \App\Teacher\ProfileTeacher::SEX_FEMALE ? 'checked' : '' }} value="female">
                <label for="female">{{__('Female')}}</label>
            </label>
        </div>
    </div>
</div>


<div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
    <label for="mobile" class="col-md-4 control-label">{{__('Mobile phone')}}</label>

    <div class="col-md-6">
        <input id="mobile" type="string" class="form-control" name="mobile" value="{{ !empty($profileTeacher->mobile) ? $profileTeacher->mobile : '' }}" required autofocus minlength="7">

        @if ($errors->has('mobile'))
            <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
        @endif
    </div>
</div>

