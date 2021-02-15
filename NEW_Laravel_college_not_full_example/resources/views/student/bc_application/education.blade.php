@extends('student.bc_application.main')

@section('part')

    <div class="form-group">
        <label class="col-md-4 control-label">{{__('Last education')}}</label>
        <student-education-block name="education-block" v-bind:active="true">
            <div class="col-md-12 subform">

                <div class="form-group padding-t15">
                    <div class="col-md-12">
                        <a class="btn margin-b15" v-bind:class="educationLevel == 'high_school' ? 'btn-info text-white' : 'btn-default'" v-on:click="educationLevel = 'high_school'">{{__('High school')}}</a>
                        <a class="btn margin-b15" v-bind:class="educationLevel == 'vocational_education' ? 'btn-info text-white' : 'btn-default'" v-on:click="educationLevel = 'vocational_education'">{{__('Vocational education')}}</a>
                        <a class="btn margin-b15" v-bind:class="educationLevel == 'bachelor' ? 'btn-info text-white' : 'btn-default'" v-on:click="educationLevel = 'bachelor'">{{__('Bachelor')}}</a>
                        <input type="hidden" v-model="educationLevel" name="education">
                        <div v-if="educationLevel" class="col-md-12 subform">
                            <div class="field_all form-group">
                                <label for="numeducation" class="control-label">{{__('Number')}}</label>
                                <div class="col-md-12">
                                    <input id="numeducation" type="text" class="form-control" name="numeducation" value="{{ $bcApplication->numeducation ?? '' }}" required autofocus>
                                </div>
                            </div>

                            <div class="field_all form-group">
                                <label for="sereducation" class="control-label">{{__('Series')}}</label>
                                <div class="col-md-12">
                                    <input id="sereducation" type="text" class="form-control" name="sereducation" value="{{ $bcApplication->sereducation ?? '' }}" required autofocus>
                                </div>
                            </div>

                            <div class="field_all form-group">
                                <label for="nameeducation" class="control-label">{{__('Institution name')}}</label>
                                <div class="col-md-12">
                                    <input id="nameeducation" type="text" class="form-control" name="nameeducation" value="{{ $bcApplication->nameeducation ?? '' }}" required autofocus>
                                </div>
                            </div>

                            <div class="field_all form-group">
                                <label for="dateeducation" class="control-label">{{__('Issue date')}}</label>
                                <div class="col-md-12">
                                    <input id="dateeducation" type="date" class="form-control" name="dateeducation" value="{{ $bcApplication->dateeducation ?? '' }}" required autofocus maxlength="9">
                                </div>
                            </div>

                            <div class="field_all form-group">
                                <label for="cityeducation" class="control-label">{{__('City of issue')}}</label>
                                <div class="col-md-12">
                                    <input id="cityeducation" type="text" class="form-control" name="cityeducation" value="{{ $bcApplication->cityeducation ?? '' }}" required autofocus >
                                </div>
                            </div>

                            <div class="field_all form-group">
                                <label v-if="educationLevel == 'high_school'" for="atteducation" class="control-label">{{__('Certificate Education')}}</label>
                                <label v-else for="atteducation" class="control-label">{{__('Diploma')}}</label>
                                <div class="col-md-12">
                                    <student-document-option name="diploma" v-bind:active="true">
                                        <div class="subform col-md-12">
                                            <div class="col-md-6 col-xs-6">
                                                <img id="photo-diploma" style="width: 100%; margin-bottom: 10px; display: none;" />
                                            </div>
                                            <div class="col-md-12" style="margin-bottom: 10px;">
                                                <input id="diploma_photo" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="diploma_photo" value="{{ old('diploma_photo') }}" @change="checkImageValid('diploma_photo')" required autofocus>
                                            </div>
                                        </div>
                                    </student-document-option>
                                </div>
                            </div>

                            <div class="field_all form-group">
                                <label v-if="educationLevel == 'high_school'" for="atteducation" class="control-label">{{__('Certificate Application')}}</label>
                                <label v-else for="atteducation" class="control-label">{{__('Diploma supplement')}}</label>
                                <div class="col-md-12">
                                    <student-document-option name="diploma_supplement" v-bind:active="true">
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
                                                <input id="atteducation" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="atteducation" value="{{ old('atteducation') }}" @change="checkImageValid('atteducation')" required autofocus>
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
                                                <input id="atteducation_back" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="atteducation_back" @change="checkImageValid('atteducation_back')" value="{{ old('atteducation_back') }}" required autofocus>
                                            </div>
                                        </div>
                                    </student-document-option>
                                </div>
                            </div>

                            <div v-if="educationLevel == 'vocational_education' || educationLevel == 'secondary_special'" class="form-group">
                                <label for="typevocational" class="control-label">{{__('Type of educational institution')}}</label>
                                <div class="col-md-12">
                                    <select class="form-control" name="typevocational" title="{{ __('Please select') }}">
                                        <option value="colledge">{{__('Colledge')}}</option>
                                        <option value="technical_school">{{__('Technical school')}}</option>
                                        <option value="specialized_school">{{__('Specialized school')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="educationLevel == 'vocational_education'" class="form-group">
                                <label for="eduspecialty" class="control-label">{{__('Specialty')}}</label>
                                <div class="col-md-12">
                                    <input id="eduspecialty" type="text" class="form-control" name="eduspecialty" value="{{ $bcApplication->eduspecialty ?? '' }}" autofocus>
                                </div>
                            </div>

                            <div v-if="educationLevel == 'bachelor'" class="form-group">
                                <label for="edudegree" class="control-label">{{__('Degree')}}</label>
                                <div class="col-md-12">
                                    <select id="edudegree" type="text" class="form-control" name="edudegree" autofocus>
                                        <option value="bachelor">{{ __('bachelor_origin') }}</option>
                                        <option value="specialist">{{ __('specialist_origin') }}</option>
                                        <option value="master">{{ __('master_origin') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="educationLevel == 'vocational_education'" class="form-group">
                                <label for="eduspecialization" class="control-label">{{__('Specialization')}}</label>
                                <div class="col-md-12">
                                    <input id="eduspecialization" type="text" class="form-control" name="eduspecialization" value="{{ $bcApplication->eduspecialization ?? '' }}" autofocus>
                                </div>
                            </div>

                            <div class="field_all form-group">
                                <label for="kzornot" class="control-label">{{__('Issued in Kazakhstan')}}</label>
                                <div class="col-md-12">
                                    <student-document-option-nostrification v-bind:active="{{$bcApplication->kzornot ?? 'true'}}">
                                        <div class="subform col-md-12">
                                            <label>{{ __('Nostrification') }}</label>
                                            <div class="col-md-12" style="margin-bottom: 10px;">
                                                <div class="col-md-12">
                                                    <label>{{ __('Front side') }}</label>
                                                </div>
                                                <div class="col-md-6 col-xs-6 no-padding-left">
                                                    <img id="photo-nostrificationattach" style="width: 100%; margin-bottom: 10px; display: none;" />
                                                </div>

                                                <input id="nostrificationattach" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" @change="checkImageValid('nostrificationattach')" name="nostrificationattach" value="{{ old('nostrificationattach') }}" required autofocus>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="col-md-12">
                                                    <label>{{ __('Back side') }}</label>
                                                </div>
                                                <div class="col-md-6 col-xs-6 no-padding-left">
                                                    <img id="photo-nostrificationattach-back" style="width: 100%; margin-bottom: 10px; display: none;" />
                                                </div>
                                                <input id="nostrificationattach_back" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" @change="checkImageValid('nostrificationattach_back')" name="nostrificationattach_back" value="{{ old('nostrificationattach_back') }}" required autofocus>
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

            </div>
        </student-education-block>
    </div>

@endsection
