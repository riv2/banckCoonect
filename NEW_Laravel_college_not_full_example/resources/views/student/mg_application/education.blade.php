@extends('student.mg_application.main')

@section('part')

    <div class="form-group">
        <label class="col-md-4 control-label">{{__('Higher education')}}</label>
        <student-education-block name="education-block" v-bind:active="true">
            <div class="col-md-12 subform">

                <div class="form-group padding-t15">
                    <div class="col-md-12">
                        <input type="hidden" name="education" value="higher" />

                        <div class="field_all form-group">
                            <label for="numeducation" class="control-label">{{__('Number')}}</label>
                            <div class="col-md-12">
                                <input id="numeducation" type="text" class="form-control" name="numeducation" value="{{ old('numeducation') }}" required autofocus>
                            </div>
                        </div>

                        <div class="field_all form-group">
                            <label for="sereducation" class="control-label">{{__('Series')}}</label>
                            <div class="col-md-12">
                                <input id="sereducation" type="text" class="form-control" name="sereducation" value="{{ old('sereducation') }}" required autofocus>
                            </div>
                        </div>

                        <div class="field_all form-group">
                            <label for="nameeducation" class="control-label">{{__('Institution name')}}</label>
                            <div class="col-md-12">
                                <input id="nameeducation" type="text" class="form-control" name="nameeducation" value="{{ old('nameeducation') }}" required autofocus>
                            </div>
                        </div>

                        <div class="field_all form-group">
                            <label for="dateeducation" class="control-label">{{__('Issue date')}}</label>
                            <div class="col-md-12">
                                <input id="dateeducation" type="date" class="form-control" name="dateeducation" value="{{ old('dateeducation') }}" required autofocus maxlength="9">
                            </div>
                        </div>

                        <div class="field_all form-group">
                            <label for="cityeducation" class="control-label">{{__('City of issue')}}</label>
                            <div class="col-md-12">
                                <input id="cityeducation" type="text" class="form-control" name="cityeducation" value="{{ old('cityeducation') }}" required autofocus >
                            </div>
                        </div>

                        <div class="field_all form-group">
                            <label for="atteducation" class="control-label">{{__('Diploma')}}</label>
                            <div class="col-md-12">
                                <student-document-option name="diploma" v-bind:active="true">
                                    <div class="subform col-md-12">
                                        <div class="col-md-6 col-xs-6">
                                            <img id="photo-diploma" style="width: 100%; margin-bottom: 10px; display: none;" />
                                        </div>
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <input id="diploma_photo" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('diploma_photo')" class="form-control" name="diploma_photo" value="{{ old('diploma_photo') }}" required autofocus>
                                        </div>
                                    </div>
                                </student-document-option>
                            </div>
                        </div>

                        <div class="field_all form-group">
                            <label for="atteducation" class="control-label">{{__('Diploma supplement')}}</label>
                            <div class="col-md-12">
                                <student-document-option name="diploma_supplement" active="true">
                                    <div class="subform col-md-12">
                                        <div class="col-md-12">
                                            <div class="col-md-12 no-padding-left">
                                                <label>{{ __('Front side') }}</label>
                                            </div>
                                            <div class="col-md-6 col-xs-6 no-padding-left">
                                                <img id="photo-atteducation" style="width: 100%; margin-bottom: 10px; display: none;" />
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <input id="atteducation" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('atteducation')" class="form-control" name="atteducation" value="{{ old('atteducation') }}" required autofocus>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="col-md-12 no-padding-left">
                                                <label>{{ __('Back side') }}</label>
                                            </div>
                                            <div class="col-md-6 col-xs-6 no-padding-left">
                                                <img id="photo-atteducation-back" style="width: 100%; margin-bottom: 10px; display: none;" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <input id="atteducation_back" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('atteducation_back')" class="form-control" name="atteducation_back" value="{{ old('atteducation_back') }}" required autofocus>
                                        </div>
                                    </div>
                                </student-document-option>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edudegree" class="control-label">{{__('Degree')}}</label>
                            <div class="col-md-12">
                                <select id="edudegree" type="text" class="form-control" name="edudegree" autofocus>
                                    <option value="bachelor">{{ __('bachelor_origin') }}</option>
                                    <option value="specialist">{{ __('specialist_origin') }}</option>
                                    <option value="master">{{ __('master_origin') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="field_all form-group">
                            <label for="kzornot" class="control-label">{{__('Issued in Kazakhstan')}}</label>
                            <div class="col-md-12">
                                <student-document-option-nostrification>
                                    <div class="subform col-md-12">
                                        <label>{{ __('Nostrification') }}</label>
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="col-md-12">
                                                <label>{{ __('Front side') }}</label>
                                            </div>
                                            <div class="col-md-6 col-xs-6 no-padding-left">
                                                <img id="photo-nostrificationattach" style="width: 100%; margin-bottom: 10px; display: none;" />
                                            </div>
                                            <input id="nostrificationattach" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('nostrificationattach')" class="form-control" name="nostrificationattach" value="{{ old('nostrificationattach') }}" required autofocus>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <label>{{ __('Back side') }}</label>
                                            </div>
                                            <div class="col-md-6 col-xs-6 no-padding-left">
                                                <img id="photo-nostrificationattach-back" style="width: 100%; margin-bottom: 10px; display: none;" />
                                            </div>
                                            <input id="nostrificationattach_back" type="file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('nostrificationattach_back')" class="form-control" name="nostrificationattach_back" value="{{ old('nostrificationattach_back') }}" required autofocus>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <label>{{ __('CON page') }}</label>
                                            </div>
                                            <div class="col-md-6 col-xs-6 no-padding-left">
                                                <img id="photo-con-confirm" style="width: 100%; margin-bottom: 10px; display: none;" />
                                            </div>
                                            <input id="con_confirm" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" @change="checkImageValid('con_confirm')" name="con_confirm" value="{{ old('con_confirm') }}" required autofocus>
                                        </div>
                                    </div>

                                </student-document-option-nostrification>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <br>
                        <br>

                        <div class="form-check margin-b10">
                            <input id="with_honors" type="checkbox" name="with_honors" value="1">
                            <label class="form-check-label" for="with_honors">
                                {{__('With honors')}}
                            </label>
                        </div>
                        <div class="form-check margin-b20">
                            <input v-model="isTransfer" id="is_transfer" type="checkbox" name="is_transfer" value="1">
                            <label class="form-check-label" for="is_transfer">
                                {{__('Is transfer')}}
                            </label>
                        </div>

                        <template v-if="isTransfer">
                            <div class="form-group">
                                <label for="eduspecialization" class="control-label">{{__('Course')}}</label>
                                <div class="col-md-12">
                                    <select class="form-control" name="transfer_course">
                                        <option value="{{ \App\Profiles::EDUCATION_COURSE_1 }}">
                                            {{ \App\Profiles::EDUCATION_COURSE_1 }}
                                        </option>
                                        <option value="{{ \App\Profiles::EDUCATION_COURSE_2 }}">
                                            {{ \App\Profiles::EDUCATION_COURSE_2 }}
                                        </option>
                                        <option value="{{ \App\Profiles::EDUCATION_COURSE_3 }}">
                                            {{ \App\Profiles::EDUCATION_COURSE_3 }}
                                        </option>
                                        <option value="{{ \App\Profiles::EDUCATION_COURSE_4 }}">
                                            {{ \App\Profiles::EDUCATION_COURSE_4 }}
                                        </option>
                                        <option value="{{ \App\Profiles::EDUCATION_COURSE_5 }}">
                                            {{ \App\Profiles::EDUCATION_COURSE_5 }}
                                        </option>
                                        <option value="{{ \App\Profiles::EDUCATION_COURSE_6 }}">
                                            {{ \App\Profiles::EDUCATION_COURSE_6 }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{__('Education study form')}}</label>
                                <div class="col-md-12">
                                    <select class="form-control" name="transfer_study_form">
                                        <option value="{{ \App\Profiles::EDUCATION_STUDY_FORM_FULLTIME }}">
                                            {{ __(\App\Profiles::EDUCATION_STUDY_FORM_FULLTIME) }}
                                        </option>
                                        <option value="{{ \App\Profiles::EDUCATION_STUDY_FORM_ONLINE }}">
                                            {{ __(\App\Profiles::EDUCATION_STUDY_FORM_ONLINE) }}
                                        </option>
                                        <option value="{{ \App\Profiles::EDUCATION_STUDY_FORM_EVENING }}">
                                            {{ __(\App\Profiles::EDUCATION_STUDY_FORM_EVENING) }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="eduspecialization" class="control-label">{{__('Specialty')}}</label>
                                <div class="col-md-12">
                                    <input id="eduspecialization" type="text" class="form-control" name="transfer_specialty" value="" autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="eduspecialization" class="control-label">{{__('Higher educational')}}</label>
                                <div class="col-md-12">
                                    <input id="eduspecialization" type="text" class="form-control" name="transfer_university" value="" autofocus>
                                </div>
                            </div>

                        </template>

                    </div>
                </div>

            </div>
        </student-education-block>
    </div>

@endsection