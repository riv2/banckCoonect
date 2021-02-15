<?php
$profileTeacher = $userTeacher->teacherProfile ?? null;
?>

@if($profileTeacher)
    <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
        <label for="photo" class="col-md-3 control-label">Фото</label>
        <div class="col-md-3">
            @if($profileTeacher->photo)
                <img
                        src="{{ \App\Services\Avatar::getPublicPath($profileTeacher->photo) ?? '' }}"
                        style="height: 150px"
                />
            @endif

            <input id="photo" type="file" class="form-control" name="photo" value="{{ isset($profileTeacher->photo)? $profileTeacher->photo:'' }}"
                   autofocus maxlength="12">

            @if ($errors->has('photo'))
                <span class="help-block">
                    <strong>{{ $errors->first('photo') }}</strong>
                </span>
            @endif
        </div>
    </div>
@endif

<div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
    <label for="iin" class="col-md-3 control-label">Email</label>

    <div class="col-md-3">
        {{ $userTeacher->email }}
    </div>
</div>

<div class="form-group{{ $errors->has('fio') ? ' has-error' : '' }}">
    <label for="fio" class="col-md-3 control-label">{{__('Full name')}}</label>

    <div class="col-md-3">
        <input id="fio" type="text" class="form-control" name="fio" value="{{ isset($profileTeacher->fio)? $profileTeacher->fio:''  }}" required autofocus maxlength="50">

        @if ($errors->has('fio'))
            <span class="help-block">
                <strong>{{ $errors->first('fio') }}</strong>
            </span>
        @endif
    </div>
</div>

@if($profileTeacher)

    <div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
        <label for="iin" class="col-md-3 control-label">{{__('ITN')}}</label>

        <div class="col-md-3">
            <input id="iin" type="text" class="form-control" name="iin"
                   value="{{ isset($profileTeacher->iin)? $profileTeacher->iin:'' }}" autofocus maxlength="12">

            @if ($errors->has('iin'))
                <span class="help-block">
                <strong>{{ $errors->first('iin') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('bdate') ? ' has-error' : '' }}">
        <label for="bdate" class="col-md-3 control-label">{{__('Birth date')}} </label>

        <div class="col-md-3">
            <input id="bdate" type="date" class="form-control" name="bdate" value="{{ !empty($profileTeacher->bdate) ? $profileTeacher->bdate->format('Y-m-d') : '' }}" autofocus>

            @if ($errors->has('bdate'))
                <span class="help-block">
                    <strong>{{ $errors->first('bdate') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <label for="doctype" class="col-md-3 control-label">Тип документа</label>
        <div class="col-md-3">
                <input type="radio" name="doctype" id="pass" {{ $profileTeacher->doctype == \App\Teacher\ProfileTeacher::DOCTYPE_PASS ? 'checked' : '' }} value="pass">
                {{__('Passport')}}<br />
                <input type="radio" name="doctype" id="id" {{ $profileTeacher->doctype == \App\Teacher\ProfileTeacher::DOCTYPE_ID ? 'checked' : '' }} value="id">
                {{__('ID')}}
        </div>
    </div>

    <div class="form-group{{ $errors->has('docnumber') ? ' has-error' : '' }}">
        <label for="docnumber" class="col-md-3 control-label">{{__('Document number')}}</label>

        <div class="col-md-3">
            <input id="docnumber" type="text" class="form-control" name="docnumber" value="{{ isset($profileTeacher->docnumber)? $profileTeacher->docnumber:'' }}" autofocus maxlength="9">

            @if ($errors->has('docnumber'))
                <span class="help-block">
                    <strong>{{ $errors->first('docnumber') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('issuing') ? ' has-error' : '' }}">
        <label for="issuing" class="col-md-3 control-label">{{__('Issuing authority')}}</label>

        <div class="col-md-3">
            <input id="issuing" type="text" class="form-control" name="issuing" value="{{ isset($profileTeacher->issuing)? $profileTeacher->issuing:'' }}" autofocus>

            @if ($errors->has('issuing'))
                <span class="help-block">
                    <strong>{{ $errors->first('issuing') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('issuedate') ? ' has-error' : '' }}">
        <label for="issuedate" class="col-md-3 control-label">{{__('Date of issue')}}</label>

        <div class="col-md-3">
            <input id="issuedate" type="date" class="form-control" name="issuedate" value="{{ !empty($profileTeacher->issuedate) ? $profileTeacher->issuedate->format('Y-m-d') : '' }}" autofocus>

            @if ($errors->has('issuedate'))
                <span class="help-block">
                    <strong>{{ $errors->first('issuedate') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <label for="sex" class="col-md-3 control-label">{{__('Sex')}}</label>
        <div class="col-md-3">
            <input type="radio" name="sex" id="male" {{ $profileTeacher->sex == 'male' ? 'checked' : '' }} value="male">
            {{__('Male')}}<br />
            <input type="radio" name="sex" id="female" {{ $profileTeacher->sex == 'female' ? 'checked' : '' }} value="female">
            {{__('Female')}}
        </div>
    </div>

    <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
        <label for="mobile" class="col-md-3 control-label">{{__('Mobile phone')}}</label>

        <div class="col-md-3">
            <input id="mobile" type="string" class="form-control" name="mobile" value="{{ !empty($profileTeacher->mobile) ? $profileTeacher->mobile : '' }}" autofocus minlength="7">

            @if ($errors->has('mobile'))
                <span class="help-block">
                    <strong>{{ $errors->first('mobile') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('facebook') ? ' has-error' : '' }}">
        <label for="facebook" class="col-md-3 control-label">Facebook</label>

        <div class="col-md-3">
            <input id="facebook" type="string" class="form-control" name="facebook" value="{{ !empty($profileTeacher->user->facebook) ? $profileTeacher->user->facebook : '' }}" autofocus minlength=>

            @if ($errors->has('facebook'))
                <span class="help-block">
                    <strong>{{ $errors->first('facebook') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('insta') ? ' has-error' : '' }}">
        <label for="finsta" class="col-md-3 control-label">Instagram</label>

        <div class="col-md-3">
            <input id="insta" type="string" class="form-control" name="insta" value="{{ !empty($profileTeacher->user->insta) ? $profileTeacher->user->insta : '' }}" autofocus>

            @if ($errors->has('insta'))
                <span class="help-block">
                    <strong>{{ $errors->first('insta') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <hr>
    @include('admin.pages.teachers.edit.education')
    <hr>

    <div class="field_vocational form-group{{ $errors->has('education_document[institution_type]') ? ' has-error' : '' }}">
        <label for="status" class="col-md-3 control-label">Статус</label>
        <div class="col-md-3">
            <select class="selectpicker" name="status" data-live-search="true" data-size="5"
                    title="{{ __('Please select') }}">
                <option value="moderation" @if($profileTeacher->status == 'moderation') selected @endif>Не проверен</option>
                <option value="active" @if($profileTeacher->status == 'active') selected @endif>Проверен</option>
                <option value="block" @if($profileTeacher->status == 'block') selected @endif>Заблокирован</option>
            </select>

            @if ($errors->has('education_document[institution_type]'))
                <span class="help-block">
                        <strong>{{ $errors->first('typevocational') }}</strong>
                    </span>
            @endif
        </div>
    </div>
@endif

<hr>

<div class="form-group">
    <label class="col-sm-3 control-label">Дисциплины</label>
    <div class="col-sm-9">
        <discipline-list v-bind:select-discipline-list="relatedDisciplineList" v-bind:main-group-list="mainGroupList"></discipline-list>
    </div>
</div>
<hr>

