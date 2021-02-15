@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-form">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Language settings')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body row">

                    <form class="col-12" method="POST" action="">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="control-label">{{__('Select education language')}}</label>
                            <select class="form-control" name="education_lang">
                                <option value="{{ \App\Profiles::EDUCATION_LANG_RU }}"> {{ __('russian') }} </option>
                                <option value="{{ \App\Profiles::EDUCATION_LANG_KZ }}"> {{ __('kazakh') }} </option>
                            </select>
                        </div>
                        <div class="form-group text-right">
                            <button class="btn btn-info btn-lg margin-tb15" type="submit">{{ __('Continue') }}</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </section>
@endsection
