<div class="form-group{{ $errors->has('education_document[level]') ? ' has-error' : '' }}">
    <label for="education_document[level]" class="col-md-3 control-label">{{__('Education')}}</label>
    <div class="col-md-3">
        <select class="selectpicker" id="education-level" name="education_document[level]" data-live-search="true" data-size="5"
                title="{{ __('Please select') }}">
            <option value="none" @if(!$educationDocument) selected @endif>Нету</option>
            <option value="secondary" @if($educationDocument && $educationDocument->level == \App\UserEducationDocument::LEVEL_SECONDARY) selected @endif>{{__('High school')}}</option>
            <option value="secondary_special" @if($educationDocument && $educationDocument->level == \App\UserEducationDocument::LEVEL_SECONDARY_SPECIAL) selected @endif>{{__('Vocational education')}}</option>
            <option value="higher" @if($educationDocument && $educationDocument->level == \App\UserEducationDocument::LEVEL_HIGHER) selected @endif>{{__('Bachelor')}}</option>
        </select>

        @if ($errors->has('education_document[level]'))
            <span class="help-block">
                <strong>{{ $errors->first('education_document[level]') }}</strong>
            </span>
        @endif
    </div>
</div>
<div id="educationData" style="@if(!($educationDocument && $educationDocument->level))display: none; @endif">
    <div class="field_all form-group{{ $errors->has('education_document[doc_number]') ? ' has-error' : '' }}">
        <label for="education_document[doc_number]" class="col-md-3 control-label">{{__('Number')}}</label>

        <div class="col-md-3">
            <input id="numeducation" type="text" class="form-control" name="education_document[doc_number]"
                   value="{{ old('education_document[doc_number]', isset($educationDocument->doc_number) ? $educationDocument->doc_number : '') }}" autofocus>

            @if ($errors->has('education_document[doc_number]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[doc_number]') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_all form-group{{ $errors->has('education_document[doc_series]') ? ' has-error' : '' }}">
        <label for="education_document[doc_series]" class="col-md-3 control-label">{{__('Series')}}</label>

        <div class="col-md-3">
            <input id="sereducation" type="text" class="form-control" name="education_document[doc_series]"
                   value="{{ old('education_document[doc_series]', isset($educationDocument->doc_series) ? $educationDocument->doc_series : '') }}"  autofocus>

            @if ($errors->has('education_document[doc_series]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[doc_series]') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_all form-group{{ $errors->has('education_document[institution_name]') ? ' has-error' : '' }}">
        <label for="education_document[institution_name]" class="col-md-3 control-label">{{__('Institution name')}}</label>

        <div class="col-md-3">
            <input id="nameeducation" type="text" class="form-control" name="education_document[institution_name]"
                   value="{{ old('education_document[institution_name]', isset($educationDocument->institution_name) ? $educationDocument->institution_name : '') }}"  autofocus>

            @if ($errors->has('education_document[institution_name]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[institution_name]') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_all form-group{{ $errors->has('education_document[date]') ? ' has-error' : '' }}">
        <label for="education_document[date]" class="col-md-3 control-label">{{__('Issue date')}}</label>

        <div class="col-md-3">
            <input id="dateeducation" type="date" class="form-control" name="education_document[date]"
                   value="{{ old('education_document[date]', isset($educationDocument->date) ? $educationDocument->date->format('Y-m-d') : '') }}"  autofocus maxlength="9">

            @if ($errors->has('education_document[date]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[date]') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_all form-group{{ $errors->has('education_document[city]') ? ' has-error' : '' }}">
        <label for="education_document[city]" class="col-md-3 control-label">{{__('City of issue')}}</label>

        <div class="col-md-3">
            <input id="cityeducation" type="text" class="form-control" name="education_document[city]"
                   value="{{ old('education_document[city]', isset($educationDocument->city) ? $educationDocument->city : '') }}" autofocus>

            @if ($errors->has('education_document[city]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[city]') }}</strong>
                </span>
            @endif
        </div>
    </div>{{----}}

    <div class="field_all form-group{{ $errors->has('education_document[supplement_file]') ? ' has-error' : '' }}">
        <label for="education_document[supplement_file]" class="col-md-3 control-label">{{__('Diploma supplement')}}</label>

        <div class="col-md-3">
            @if(isset($educationDocument->supplement_file_name) && file_exists(public_path('images/uploads/diploma/' . $educationDocument->supplement_file_name)))
            <a href="/images/uploads/diploma/{{ $educationDocument->supplement_file_name }}" target="_blank">Посмотреть приложение</a>
            @endif
            <input id="atteducation" type="file" class="form-control" name="education_document[supplement_file]"
                   value="{{ old('education_document[supplement_file]') }}"  autofocus>

            @if ($errors->has('education_document[supplement_file]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[supplement_file]') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_vocational field_bachelor form-group{{ $errors->has('education_document[speciality]') ? ' has-error' : '' }}"
         style="@if($educationDocument && $educationDocument->level != \App\UserEducationDocument::LEVEL_SECONDARY_SPECIAL)display: none; @endif"
    >
        <label for="education_document[speciality]" class="col-md-3 control-label">{{__('Specialty')}}</label>

        <div class="col-md-3">
            <input id="eduspecialty" type="text" class="form-control" name="education_document[speciality]"
                   value="{{ old('education_document[speciality]', isset($educationDocument->speciality) ? $educationDocument->speciality : '') }}" autofocus>

            @if ($errors->has('education_document[speciality]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[speciality]') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_vocational form-group{{ $errors->has('education_document[institution_type]') ? ' has-error' : '' }}"
         style="@if($educationDocument && $educationDocument->level != \App\UserEducationDocument::LEVEL_SECONDARY_SPECIAL)display: none; @endif"
    >
        <label for="education_document[institution_type]" class="col-md-3 control-label">{{__('Type of educational institution')}}</label>
        <div class="col-md-3">
            <select class="selectpicker" name="education_document[institution_type]" data-live-search="true" data-size="5"
                    title="{{ __('Please select') }}">
                <option value="colledge" @if($educationDocument && $educationDocument->institution_type == 'colledge') selected @endif>{{__('Colledge')}}</option>
                <option value="technical_school" @if($educationDocument && $educationDocument->institution_type == 'technical_school') selected @endif>{{__('Technical school')}}</option>
                <option value="specialized_school" @if($educationDocument && $educationDocument->institution_type == 'specialized_school') selected @endif>{{__('Specialized school')}}</option>
            </select>

            @if ($errors->has('education_document[institution_type]'))
                <span class="help-block">
                    <strong>{{ $errors->first('typevocational') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_bachelor form-group{{ $errors->has('education_document[degree]') ? ' has-error' : '' }}"
         style="@if($educationDocument && $educationDocument->level != \App\UserEducationDocument::LEVEL_HIGHER)display: none; @endif"
    >
        <label for="education_document[degree]" class="col-md-3 control-label">{{__('Degree')}}</label>

        <div class="col-md-3">
            <input id="edudegree" type="text" class="form-control" name="education_document[degree]"
                   value="{{ old('education_document[degree]', isset($educationDocument->degree) ? $educationDocument->degree : '') }}"
                   autofocus>

            @if ($errors->has('education_document[degree]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[degree]') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_all form-group{{ $errors->has('education_document[specialization]') ? ' has-error' : '' }}">
        <label for="education_document[specialization]" class="col-md-3 control-label">{{__('Specialization')}}</label>

        <div class="col-md-3">
            <input id="eduspecialization" type="text" class="form-control" name="education_document[specialization]"
                   value="{{ old('education_document[specialization]', isset($educationDocument->specialization) ? $educationDocument->specialization : '') }}" autofocus>

            @if ($errors->has('education_document[specialization]'))
                <span class="help-block">
                    <strong>{{ $errors->first('education_document[specialization]') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="field_all form-group">
        <label for="education_document[kz_holder]" class="col-md-3 control-label">{{__('Issued in Kazakhstan')}}</label>
        <div class="col-md-3">
            <input class="education-kz_holder" type="radio" name="education_document[kz_holder]" id="inkz" {{ old('education_document[kz_holder]', isset($educationDocument->kz_holder) ? $educationDocument->kz_holder : '') ? 'checked' : '' }} value="1">
            {{__('Yes')}}<br/>
            <input class="education-kz_holder" type="radio" name="education_document[kz_holder]" id="No" {{ !old('education_document[kz_holder]', isset($educationDocument->kz_holder) ? $educationDocument->kz_holder : '') ? 'checked' : '' }} value="0">
            {{__('No')}}
        </div>
    </div>
    <div id="nostrificationBlock"
         style="@if($educationDocument && $educationDocument->kz_holder)display: none; @endif"
    >
        <div class="field_all form-group{{ $errors->has('education_document[nostrification]') ? ' has-error' : '' }}">
            <label for="education_document[nostrification]" class="col-md-3 control-label">{{__('Nostrification')}}</label>

            <div class="col-md-3">
                <input id="nostrification" type="text" class="form-control" name="education_document[nostrification]"
                       value="{{ old('education_document[nostrification]', isset($educationDocument->nostrification) ? $educationDocument->nostrification : '') }}" autofocus>

                @if ($errors->has('education_document[nostrification]'))
                    <span class="help-block">
                        <strong>{{ $errors->first('education_document[nostrification]') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="field_all form-group{{ $errors->has('education_document[nostrification_file]') ? ' has-error' : '' }}">
            <label for="education_document[nostrification_file]" class="col-md-3 control-label"> </label>

            <div class="col-md-3">
                @if(isset($educationDocument->nostrification_file_name) && file_exists(public_path('images/uploads/diploma/' . $educationDocument->nostrification_file_name)))
                    <a href="/images/uploads/diploma/{{ $educationDocument->nostrification_file_name }}" target="_blank">Посмотреть приложение</a>
                @endif
                <input id="nostrificationattach" type="file" class="form-control" name="education_document[nostrification_file]"
                       value="{{ old('education_document[nostrification_file]') }}" autofocus>

                @if ($errors->has('education_document[nostrification_file]'))
                    <span class="help-block">
                        <strong>{{ $errors->first('education_document[nostrification_file]') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>