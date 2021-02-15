@extends('layouts.app')

@section('content')
    <div id="educationInputs" class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__("Entering education")}}</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route( \App\Teacher\ProfileTeacher::REGISTRATION_STEP_ENTER_EDUCATION_POST) }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label class="col-md-4 control-label"></label>
                                <div class="col-md-8">
                                    <button @click="addBlock()" type="button" class="btn btn-success">{{ __('Added education') }}</button>
                                </div>
                            </div>

                            <!-- Education block -->
                            <template v-for="(item, index) in educationBlockList">
                                <div id="education_block" class="panel panel-default education_block">
                                <div class="panel-body">

                                    <div class="form-group">
                                        <label for="front" class="col-md-4 control-label">{{__('Indicate the type of education')}}</label>
                                        <div class="col-md-6">
                                            <select v-model="item.select" class="form-control">
                                                <option value="basic_education">{{__('Basic education')}}</option>
                                                <option value="master">{{__('Master')}}</option>
                                                <option value="academic_degree">{{__('Scholastic degree')}}</option>
                                                <option value="academic_rank">{{__('Academic rank')}}</option>
                                                <option value="knowledge_languages">{{__('Knowledge of languages')}}</option>
                                                <option value="extra_skills">{{__('Extra skills')}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Basic education -->
                                    <div class="basic_education" v-if="item.select == 'basic_education'">

                                        <div class="form-group">
                                            <label for="front" class="col-md-4 control-label">{{__('Academic degree')}}</label>
                                            <div class="col-md-6">
                                                <select name="education[type][]" class="form-control">
                                                    <option value="{{ \App\TeachersEducation::TYPE_BACHELOR }}">{{__('Bachelor type')}}</option>
                                                    <option value="{{ \App\TeachersEducation::TYPE_SPECIALIST }}">{{__('Specialist')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="date_from" class="col-md-4 control-label">{{__('Training Start Date')}}</label>
                                            <div class="col-md-8">
                                                <input id="date_from" type="date" class="form-control" name="education[date_from][]" value="" required autofocus maxlength="9">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="date_to" class="col-md-4 control-label">{{__('Expiration date')}}</label>
                                            <div class="col-md-8">
                                                <input id="date_to" type="date" class="form-control" name="education[date_to][]" value="" required autofocus maxlength="9">
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="education_place" class="col-md-4 control-label">{{__('Education place')}}</label>
                                            <div class="col-md-8">
                                                <input id="education_place" type="text" class="form-control" name="education[education_place][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="qualification_awarded" class="col-md-4 control-label">{{__('Qualification Assigned')}}</label>
                                            <div class="col-md-8">
                                                <input id="qualification_awarded" type="text" class="form-control" name="education[qualification_awarded][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="speciality" class="col-md-4 control-label">{{__('Name of specialty')}}</label>
                                            <div class="col-md-8">
                                                <input id="speciality" type="text" class="form-control" name="education[speciality][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label v-bind:for="'issue_in_kz' + item" for="issue_in_kz" class="col-md-4 control-label">
                                                {{__('Issued in Kazakhstan')}}
                                                <input v-model="item.issueKZ" v-bind:id="'issue_in_kz' + item" type="checkbox">
                                            </label>
                                        </div>
                                        <div v-if="!item.issueKZ" class="field_all form-group">
                                            <label for="nostrification" class="col-md-4 control-label">{{__('Nostrification')}}</label>
                                            <div class="col-md-8">
                                                <input id="nostrification" type="text" class="form-control" name="education[nostrification][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="diploma_photo" class="col-md-4 control-label">{{__('Diploma')}}</label>
                                            <div class="col-md-8">
                                                    <div class="subform col-md-12">
                                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                                            <input id="diploma_photo" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[diploma_photo][]" value="" required autofocus>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="atteducation" class="col-md-4 control-label">{{__('Diploma supplement')}}</label>
                                            <div class="col-md-8">
                                                    <div class="subform col-md-12">
                                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                                            <label>{{ __('Front side') }}</label>
                                                            <input id="atteducation" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[atteducation][]" value="" required autofocus>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label>{{ __('Back side') }}</label>
                                                            <input id="atteducation_back" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[atteducation_back][]" value="" required autofocus>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Master -->
                                    <div class="basic_education" v-if="item.select == 'master'">
                                        <div class="form-group">
                                            <label for="front" class="col-md-4 control-label">{{__('Academic degree')}}</label>
                                            <div class="col-md-6">
                                                <select name="education[type][]" class="form-control">
                                                    <option value="{{ \App\TeachersEducation::TYPE_MAGISTRACY }}">{{__('Magistracy')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="date_from" class="col-md-4 control-label">{{__('Training Start Date')}}</label>
                                            <div class="col-md-8">
                                                <input id="date_from" type="date" class="form-control" name="education[date_from][]" value="" required autofocus maxlength="9">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="date_to" class="col-md-4 control-label">{{__('Expiration date')}}</label>
                                            <div class="col-md-8">
                                                <input id="date_to" type="date" class="form-control" name="education[date_to][]" value="" required autofocus maxlength="9">
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="education_place" class="col-md-4 control-label">{{__('Education place')}}</label>
                                            <div class="col-md-8">
                                                <input id="education_place" type="text" class="form-control" name="education[education_place][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="qualification_awarded" class="col-md-4 control-label">{{__('Qualification Assigned')}}</label>
                                            <div class="col-md-8">
                                                <input id="qualification_awarded" type="text" class="form-control" name="education[qualification_awarded][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="speciality" class="col-md-4 control-label">{{__('Name of specialty')}}</label>
                                            <div class="col-md-8">
                                                <input id="speciality" type="text" class="form-control" name="education[speciality][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label v-bind:for="'issue_in_kz' + item" for="issue_in_kz" class="col-md-4 control-label">
                                                {{__('Issued in Kazakhstan')}}
                                                <input v-model="item.issueKZ" v-bind:id="'issue_in_kz' + item" type="checkbox">
                                            </label>
                                        </div>
                                        <div v-if="!item.issueKZ" class="field_all form-group">
                                            <label for="nostrification" class="col-md-4 control-label">{{__('Nostrification')}}</label>
                                            <div class="col-md-8">
                                                <input id="nostrification" type="text" class="form-control" name="education[nostrification][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="diploma_photo" class="col-md-4 control-label">{{__('Diploma')}}</label>
                                            <div class="col-md-8">
                                                    <div class="subform col-md-12">
                                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                                            <input id="diploma_photo" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[diploma_photo][]" value="" required autofocus>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="atteducation" class="col-md-4 control-label">{{__('Diploma supplement')}}</label>
                                            <div class="col-md-8">
                                                    <div class="subform col-md-12">
                                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                                            <label>{{ __('Front side') }}</label>
                                                            <input id="atteducation" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[atteducation][]" value="" required autofocus>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label>{{ __('Back side') }}</label>
                                                            <input id="atteducation_back" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[atteducation_back][]" value="" required autofocus>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Academic degree -->
                                    <div class="basic_education" v-if="item.select == 'academic_degree'">

                                        <input type="hidden" name="education[type][]" value="{{ \App\TeachersEducation::TYPE_CANDIDATE_SCIENCES }}" />
                                        <div class="form-group">
                                            <label for="academic_degree_id" class="col-md-4 control-label">{{__('Scholastic degree')}}</label>
                                            <div class="col-md-6">
                                                <select name="education[academic_degree_id][]" class="form-control">
                                                    @foreach($academicDegree as $itemAD)
                                                        <option value="{{$itemAD->id}}">{{$itemAD->$locale}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="speciality" class="col-md-4 control-label">{{__('Name of specialty')}}</label>
                                            <div class="col-md-8">
                                                <input id="speciality" type="text" class="form-control" name="education[speciality][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="date_from" class="col-md-4 control-label">{{__('Training Start Date')}}</label>
                                            <div class="col-md-8">
                                                <input id="date_from" type="date" class="form-control" name="education[date_from][]" value="" required autofocus maxlength="9">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="date_to" class="col-md-4 control-label">{{__('Expiration date')}}</label>
                                            <div class="col-md-8">
                                                <input id="date_to" type="date" class="form-control" name="education[date_to][]" value="" required autofocus maxlength="9">
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="education_place" class="col-md-4 control-label">{{__('Education place')}}</label>
                                            <div class="col-md-8">
                                                <input id="education_place" type="text" class="form-control" name="education[education_place][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="scientific_field_id" class="col-md-4 control-label">{{__('Science field')}}</label>
                                            <div class="col-md-6">
                                                <select id="scientific_field_id" class="form-control" name="education[scientific_field_id][]" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                                    @foreach($scientificField as $itemSF)
                                                        <option value="{{$itemSF->id}}">{{$itemSF->$locale}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="dissertation_topic_1" class="col-md-4 control-label">{{__('Dissertation topic')}}</label>
                                            <div class="col-md-8">
                                                <input id="dissertation_topic_1" type="text" class="form-control" name="education[dissertation_topic_1][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="dissertation_topic_2" class="col-md-4 control-label">{{__('Dissertation topic')}}</label>
                                            <div class="col-md-8">
                                                <input id="dissertation_topic_2" type="text" class="form-control" name="education[dissertation_topic_2][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="protocol_number" class="col-md-4 control-label">{{__('Protocol number')}}</label>
                                            <div class="col-md-8">
                                                <input id="protocol_number" type="text" class="form-control" name="education[protocol_number][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label v-bind:for="'issue_in_kz' + item" for="issue_in_kz" class="col-md-4 control-label">
                                                {{__('Issued in Kazakhstan')}}
                                                <input v-model="item.issueKZ" v-bind:id="'issue_in_kz' + item" type="checkbox">
                                            </label>
                                        </div>
                                        <div v-if="!item.issueKZ" class="field_all form-group">
                                            <label for="nostrification" class="col-md-4 control-label">{{__('Nostrification')}}</label>
                                            <div class="col-md-8">
                                                <input id="nostrification" type="text" class="form-control" name="education[nostrification][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="certificate_file" class="col-md-4 control-label">{{__('Certificate')}}</label>
                                            <div class="col-md-8">
                                                    <div class="subform col-md-12">
                                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                                            <label>{{ __('Upload file') }}</label>
                                                            <input id="certificate_file" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[certificate_file][]" value="" required autofocus>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Academic rank -->
                                    <div class="basic_education" v-if="item.select == 'academic_rank'">

                                        <input type="hidden" name="education[type][]" value="{{ \App\TeachersEducation::TYPE_ACADEMIC_RANK }}" />
                                        <div class="form-group">
                                            <label for="academic_title" class="col-md-4 control-label">{{__('Scholastic degree')}}</label>
                                            <div class="col-md-6">
                                                <select name="education[academic_title][]" class="form-control">
                                                    <option value="{{ \App\TeachersEducation::ACADEMIC_TITLE_NO_TITLE }}">{{ __('no title') }}</option>
                                                    <option value="{{ \App\TeachersEducation::ACADEMIC_TITLE_ASSOCIATE_PROFESSOR }}">{{ __('Associate professor') }}</option>
                                                    <option value="{{ \App\TeachersEducation::ACADEMIC_TITLE_PROFESSOR }}">{{ __('Professor') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="speciality" class="col-md-4 control-label">{{__('Name of specialty')}}</label>
                                            <div class="col-md-8">
                                                <input id="speciality" type="text" class="form-control" name="education[speciality][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="protocol_date" class="col-md-4 control-label">{{__('Protocol date')}}</label>
                                            <div class="col-md-8">
                                                <input id="protocol_date" type="date" class="form-control" name="education[protocol_date][]" value="" required autofocus maxlength="9">
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="protocol_number" class="col-md-4 control-label">{{__('Protocol number')}}</label>
                                            <div class="col-md-8">
                                                <input id="protocol_number" type="text" class="form-control" name="education[protocol_number][]" value="" required autofocus>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="certificate_file" class="col-md-4 control-label">{{__('Certificate')}}</label>
                                            <div class="col-md-8">
                                                    <div class="subform col-md-12">
                                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                                            <label>{{ __('Upload file') }}</label>
                                                            <input id="certificate_file" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[certificate_file][]" value="" required autofocus>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="embership_academies" class="col-md-4 control-label">{{__('Embership academies')}}</label>
                                            <div class="col-md-8">
                                                <input id="embership_academies" type="text" class="form-control" name="education[embership_academies][]" value="" required autofocus>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Knowledge of languages -->
                                    <div class="basic_education" v-if="item.select == 'knowledge_languages'">

                                        <input type="hidden" name="education[type][]" value="{{ \App\TeachersEducation::TYPE_LANGUAGE_ABILITY }}" />
                                        <div class="form-group">
                                            <label for="lang_id" class="col-md-4 control-label">{{__('Language selection')}}</label>
                                            <div class="col-md-6">
                                                <select id="lang_id" class="form-control" name="education[lang_id][]" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                                    @foreach($language as $itemL)
                                                        <option value="{{$itemL->id}}">{{$itemL->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lang_level_id" class="col-md-4 control-label">{{__('Level')}}</label>
                                            <div class="col-md-6">
                                                <select id="lang_level_id" class="form-control" name="education[lang_level_id][]" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                                                    @foreach($languagesLevel as $itemLL)
                                                        <option value="{{$itemLL->id}}">{{$itemLL->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="field_all form-group">
                                            <label for="certificate_lang_file" class="col-md-4 control-label">{{__('Certificate')}}</label>
                                            <div class="col-md-8">
                                                    <div class="subform col-md-12">
                                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                                            <label>{{ __('Upload file') }}</label>
                                                            <input id="certificate_lang_file" type="file" accept=".jpg, .png, .gif, .webp" class="form-control" name="education[certificate_lang_file][]" value="" autofocus>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Extra skills -->
                                    <div class="basic_education" v-if="item.select == 'extra_skills'">

                                        <input type="hidden" name="education[type][]" value="{{ \App\TeachersEducation::TYPE_ADDITIONAL_SKILL }}" />
                                        <div class="form-group">
                                            <label for="data_input" class="col-sm-4 control-label">{{ __('Data input') }}</label>
                                            <div class="col-sm-8">
                                                <textarea id="data_input" name="education[data_input][]" class="form-control"></textarea>
                                            </div>
                                        </div>

                                    </div>

                                    <div v-if="index > 0" class="field_all form-group">
                                        <label for="embership_academies" class="col-md-4 control-label"></label>
                                        <div class="col-md-8">
                                            <button v-on:click="educationBlockList.splice(index, 1)" class="btn btn-danger education-block-remove" type="button"><i class="glyphicon glyphicon-remove"></i> Удалить</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            </template>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" id="sendButton">
                                        {{__("Send")}}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">

        var app = new Vue({
            el: '#educationInputs',
            data: {

                educationBlockList: []
            },
            methods: {

                addBlock: function(){

                    this.educationBlockList.push({ id: this.educationBlockList.length, select: '', issueKZ: true });
                },
                removeBlock: function(index){

                    this.$delete(this.educationBlockList[index], index)
                }
            },
            created: function() {

                this.educationBlockList.push({ id: this.educationBlockList.length, select: '', issueKZ: true });
            }
        });

    </script>
@endsection
