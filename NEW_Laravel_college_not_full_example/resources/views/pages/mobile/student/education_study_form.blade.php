@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-form">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Student panel page')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <form method="POST" action="">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="education_lang" class="control-label">{{__('Education study form')}}</label>
                            <select class="form-control" name="education_study_form" required>
                                <option value="{{\App\Profiles::EDUCATION_STUDY_FORM_FULLTIME}}">{{ __(\App\Profiles::EDUCATION_STUDY_FORM_FULLTIME) }}</option>
                                <option value="{{\App\Profiles::EDUCATION_STUDY_FORM_EVENING}}">{{ __(\App\Profiles::EDUCATION_STUDY_FORM_EVENING) }}</option>
                                <option value="{{\App\Profiles::EDUCATION_STUDY_FORM_ONLINE}}">{{ __(\App\Profiles::EDUCATION_STUDY_FORM_ONLINE) }}</option>
                            </select>
                        </div>

                        <div class="form-group text-right">
                            <button class="btn btn-info btn-lg" type="submit">{{ __('Continue') }}</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </section>
@endsection
