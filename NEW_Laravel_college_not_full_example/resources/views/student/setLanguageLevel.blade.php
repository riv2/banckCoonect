@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__('Language settings')}}</div>
                    <div class="panel-body">

                        <form class="form-horizontal" method="POST" action="">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <div class="col-md-12">
                                    <label for="language_english_level" class="col-md-4 control-label">{{__('Select english level')}}</label>

                                    <div class="col-md-3">
                                        <select name="language_english_level" class="form-control">
                                            @foreach($englishLevelList as $item)
                                                <option value="{{ $item->id }}"> {{ $item->level }} </option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    {{__('If you find it difficult to answer, you can determine your level')}} <a href="https://www.cambridgeenglish.org/test-your-english/general-english/" target="_blank">{{__('following the link')}}</a>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group col-md-12">
                                <button class="btn btn-primary pull-right" type="submit">{{ __('Save') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
