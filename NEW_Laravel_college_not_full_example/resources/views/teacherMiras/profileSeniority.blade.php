@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__("Entering work experience")}}</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route( \App\Teacher\ProfileTeacher::REGISTRATION_STEP_SENIORITY_POST) }}">
                            {{ csrf_field() }}


                            <div class="form-group">
                                <label for="date_from" class="col-md-4 control-label"></label>
                                <div class="col-md-8">
                                    <button type="button" class="btn btn-success added">{{__('Add seniority')}}</button>
                                </div>
                            </div>

                            <!-- Experience block -->
                            <div id="here" class="panel panel-default experience-block">
                                <div class="panel-body">

                                    <div class="form-group">
                                        <label for="date_from" class="col-md-4 control-label">{{__('Start Date')}}</label>
                                        <div class="col-md-8">
                                            <input id="date_from" type="date" class="form-control" name="experience[date_from][]" value="" required autofocus maxlength="9">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_to" class="col-md-4 control-label">{{__('Expiration date')}}</label>
                                        <div class="col-md-8">
                                            <input id="date_to" type="date" class="form-control" name="experience[date_to][]" value="" required autofocus maxlength="9">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="workplace" class="col-sm-4 control-label">{{ __('Workplace') }}</label>
                                        <div class="col-sm-8">
                                            <textarea id="workplace" name="experience[workplace][]" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="type_experience" class="col-md-4 control-label">{{__('Type of experience')}}</label>
                                        <div class="col-md-6">
                                            <select name="experience[type_experience][]" class="form-control">
                                                <option value="{{ \App\TeachersExperience::TYPE_EXPERIENCE_PRACTICAL }}">{{ __('Practical') }}</option>
                                                <option value="{{ \App\TeachersExperience::TYPE_EXPERIENCE_TEACHING }}">{{ __('Teaching') }}</option>
                                                <option value="{{ \App\TeachersExperience::TYPE_EXPERIENCE_OTHER }}">{{ __('Other') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="current_experience" class="col-md-4 control-label">{{__('Current')}}</label>
                                        <div class="col-md-6">
                                            <select name="experience[current_experience][]" class="form-control">
                                                <option value="{{ \App\TeachersExperience::CURRENT_EXPERIENCE_YES }}">{{ __('Yes') }}</option>
                                                <option value="{{ \App\TeachersExperience::CURRENT_EXPERIENCE_NO }}">{{ __('No') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field_all form-group">
                                        <label for="workstatus" class="col-md-4 control-label">{{__('Position held')}}</label>
                                        <div class="col-md-8">
                                            <input id="workstatus" type="text" class="form-control" name="experience[workstatus][]" value="" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="charges" class="col-sm-4 control-label">{{ __('Functional responsibilities') }}</label>
                                        <div class="col-sm-8">
                                            <textarea id="charges" name="experience[charges][]" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="contacts" class="col-sm-4 control-label">{{ __('Contacts of the head (organization)') }}</label>
                                        <div class="col-sm-8">
                                            <textarea id="contacts" name="experience[contacts][]" class="form-control" required></textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

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


    <div class="copy hide">
        <div id="here" class="panel panel-default experience-block">
            <div class="panel-body">

                <div class="form-group">
                    <label for="date_from" class="col-md-4 control-label">{{__('Start Date')}}</label>
                    <div class="col-md-8">
                        <input id="date_from" type="date" class="form-control" name="experience[date_from][]" value="" required autofocus maxlength="9">
                    </div>
                </div>
                <div class="form-group">
                    <label for="date_to" class="col-md-4 control-label">{{__('Expiration date')}}</label>
                    <div class="col-md-8">
                        <input id="date_to" type="date" class="form-control" name="experience[date_to][]" value="" required autofocus maxlength="9">
                    </div>
                </div>
                <div class="form-group">
                    <label for="workplace" class="col-sm-4 control-label">{{ __('Workplace') }}</label>
                    <div class="col-sm-8">
                        <textarea id="workplace" name="experience[workplace][]" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type_experience" class="col-md-4 control-label">{{__('Type of experience')}}</label>
                    <div class="col-md-6">
                        <select name="experience[type_experience][]" class="form-control">
                            <option value="{{ \App\TeachersExperience::TYPE_EXPERIENCE_PRACTICAL }}">{{ __('Practical') }}</option>
                            <option value="{{ \App\TeachersExperience::TYPE_EXPERIENCE_TEACHING }}">{{ __('Teaching') }}</option>
                            <option value="{{ \App\TeachersExperience::TYPE_EXPERIENCE_OTHER }}">{{ __('Other') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="current_experience" class="col-md-4 control-label">{{__('Current')}}</label>
                    <div class="col-md-6">
                        <select name="experience[current_experience][]" class="form-control">
                            <option value="{{ \App\TeachersExperience::CURRENT_EXPERIENCE_YES }}">{{ __('Yes') }}</option>
                            <option value="{{ \App\TeachersExperience::CURRENT_EXPERIENCE_NO }}">{{ __('No') }}</option>
                        </select>
                    </div>
                </div>
                <div class="field_all form-group">
                    <label for="workstatus" class="col-md-4 control-label">{{__('Position held')}}</label>
                    <div class="col-md-8">
                        <input id="workstatus" type="text" class="form-control" name="experience[workstatus][]" value="" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label for="charges" class="col-sm-4 control-label">{{ __('Functional responsibilities') }}</label>
                    <div class="col-sm-8">
                        <textarea id="charges" name="experience[charges][]" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="contacts" class="col-sm-4 control-label">{{ __('Contacts of the head (organization)') }}</label>
                    <div class="col-sm-8">
                        <textarea id="contacts" name="experience[contacts][]" class="form-control" required></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="contacts" class="col-sm-4 control-label"></label>
                    <div class="col-sm-8">
                        <button type="button" class="btn btn-danger remove">{{ __('Remove') }}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script type="text/javascript">

        $("body").on("click",".remove",function(){
            $(this).parents(".experience-block").remove();
        });

        $(".added").click(function(){
            var html = $(".copy").html();
            $("#here").after(html);
        });

    </script>
@endsection
