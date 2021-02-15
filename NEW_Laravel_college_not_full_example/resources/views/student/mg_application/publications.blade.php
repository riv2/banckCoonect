@extends('student.mg_application.main')

@section('part')
<div class="form-group">
    <label for="" class="control-label">{{__('Publications')}}</label>
    <div class="col-md-12">
        <student-document-option name="publication">
            <div class="col-md-12 subform" v-for="(model, index) in publicationList" :key="model.id">
                <div class="col-md-12 no-padding">{{__('Publication')}} &nbsp; @{{index + 1}} &nbsp;
                    <a class="btn btn-xs pull-right" v-on:click="deletePublication(index)" v-if="publicationList.length > 1">
                        <i class="glyphicon glyphicon-remove"></i>
                    </a>
                </div>
                <hr>
                <div class="form-group">
                    <label for="publication_type" class="control-label">{{__('Type')}}</label>
                    <div class="col-md-12">
                        <select v-bind:name="'publication[' + model.id + '][type]'" class="form-control" title="{{ __('Please select') }}" required>
                            <option value="research_article">{{__('Research article')}}</option>
                            <option value="publication">{{__('Publication')}}</option>
                            <option value="monograph">{{__('Monograph')}}</option>
                            <option value="tutorial">{{__('Tutorial')}}</option>
                            <option value="glossary">{{__('Glossary')}}</option>
                            <option value="directory">{{__('Directory')}}</option>
                            <option value="other">{{__('Other')}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="publication_name" class="control-label">{{__('Name')}}</label>
                    <div class="col-md-12">
                        <input type="text" class="form-control" v-bind:name="'publication[' + model.id + '][name]'" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="publication_place" class="control-label">{{__('Placement')}}</label>
                    <div class="col-md-12">
                        <input type="text" class="form-control" v-bind:name="'publication[' + model.id + '][place]'" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="publication_year" class="col-md-4 control-label">{{__('Year of publication')}}</label>
                    <div class="col-md-12">
                        <input type="text" class="form-control" v-bind:name="'publication[' + model.id + '][year]'" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="publication_issue_number" class="col-md-4 control-label">{{__('Issue number')}}</label>
                    <div class="col-md-12">
                        <input type="text" class="form-control" v-bind:name="'publication[' + model.id + '][issue_number]'"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="publication_file" class="col-md-4 control-label">{{__('Attach file')}}</label>
                    <div class="col-md-12">
                        <keep-alive>
                        <input type="file" id="publication_file" accept=".jpg, .png, .gif, .webp" @change="checkImageValid('publication_file')" class="form-control" v-bind:name="'publication[' + model.id + '][file]'"/>
                        </keep-alive>
                    </div>
                </div>

                <div class="form-group">
                    <label for="publication_collegues" class="col-md-4 control-label">{{__('Collaborators')}}</label>
                    <div class="col-md-12">
                        <input type="text" class="form-control" v-bind:name="'publication[' + model.id + '][colleagues]'"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="publication_lang" class="col-md-4 control-label">{{__('Publication language')}}</label>
                    <div class="col-md-12">
                        <select v-bind:name="'publication[' + model.id + '][lang]'" class="form-control" title="{{ __('Please select') }}" required>
                            <option value="scienpublkaz">{{__('Kaz')}}</option>
                            <option value="scienpublrus">{{__('Rus')}}</option>
                            <option value="scienpubleng">{{__('Eng')}}</option>
                            <option value="scienpublother">{{__('Other')}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="publication_collegues" class="col-md-4 control-label">{{__('Availability of ISBN')}}</label>
                    <div class="col-md-12">
                        <student-document-option v-bind:name="'publication[' + model.id + '][isbn]'">
                            <div class="col-md-7 no-padding-left">
                                <input type="text" class="form-control" v-bind:name="'publication[' + model.id + '][isbn]'" required/>
                            </div>
                        </student-document-option>
                    </div>
                </div>
            </div>
            <a class="btn btn-sm btn-info text-white pull-right" v-on:click="addPublication()">{{ __('Add publication') }}</a>
        </student-document-option>
    </div>
</div>
@endsection