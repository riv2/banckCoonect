@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-form">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Language settings')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <form method="POST" action="">

                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label for="education_lang" class="col-md-4 control-label">{{__('Select education language')}}</label>

                                    <div class="col-md-6">
                                        <select name="education_lang" class="form-control">
                                            <option value="{{ \App\Profiles::EDUCATION_LANG_RU }}"> {{ __('russian') }} </option>
                                            <option value="{{ \App\Profiles::EDUCATION_LANG_KZ }}"> {{ __('kazakh') }} </option>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group col-md-12">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <button class="btn btn-info" type="submit">{{ __('Continue') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

