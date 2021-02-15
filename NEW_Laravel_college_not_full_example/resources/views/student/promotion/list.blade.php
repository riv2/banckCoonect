@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" id="subjects">
                                <div>

                                    <h3>{{__('Promotions list')}}</h3>

                                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                        @foreach($promotionList as $key => $promotion)
                                            <div class="panel
                                            @if(!\App\Services\Auth::user()->getPromotionStatus($promotion->name))
                                                panel-default
                                            @endif
                                            @if(\App\Services\Auth::user()->getPromotionStatus($promotion->name) == \App\PromotionUser::STATUS_MODERATION)
                                                panel-warning
                                            @endif
                                            @if(\App\Services\Auth::user()->getPromotionStatus($promotion->name) == \App\PromotionUser::STATUS_ACTIVE)
                                                panel-success
                                            @endif
                                            @if(\App\Services\Auth::user()->getPromotionStatus($promotion->name) == \App\PromotionUser::STATUS_REJECT)
                                                panel-danger
                                            @endif
                                            ">
                                                <div class="panel-heading" role="tab" id="heading{{$key}}">
                                                    <h4 class="panel-title">
                                                        <a class="{{ $promotion->id != $showId ? 'collapsed' : '' }}" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}" aria-expanded="{{ $promotion->id == $showId ? 'true' : 'false' }}" aria-controls="collapse{{$key}}">
                                                            {{__($promotion->name)}}
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapse{{$key}}" class="panel-collapse collapse {{ $promotion->id == $showId ? 'in' : '' }}" role="tabpanel" aria-labelledby="heading{{$key}}">
                                                    <div class="panel-body">
                                                        <div class="col-md-12">
                                                            {{__($promotion->description)}}
                                                        </div>
                                                        <div class="col-md-12">
                                                            @if(View::exists('student.promotion.form.' . $promotion->name))
                                                                @include('student.promotion.form.' . $promotion->name)
                                                            @endif
                                                        </div>

                                                        @if(!\App\Services\Auth::user()->getPromotionStatus($promotion->name))
                                                        <div class="col-md-12">
                                                            <a class="btn btn-primary pull-right" onclick="promotionSubmit('{{$promotion->name}}')">{{ __('Participate in the promotion') }}</a>
                                                        </div>
                                                        @endif

                                                        @if(\App\Services\Auth::user()->getPromotionStatus($promotion->name) == \App\PromotionUser::STATUS_MODERATION)
                                                            <div class="col-md-12">
                                                                <span class="label label-warning">{{ __('The application is checked by the moderator') }}.</span>
                                                            </div>
                                                        @endif
                                                        @if(\App\Services\Auth::user()->getPromotionStatus($promotion->name) == \App\PromotionUser::STATUS_ACTIVE)
                                                            <div class="col-md-12">
                                                                <span class="label label-success">{{ __('You are participating in the promotion') }}.</span>
                                                            </div>
                                                        @endif
                                                        @if(\App\Services\Auth::user()->getPromotionStatus($promotion->name) == \App\PromotionUser::STATUS_REJECT)
                                                            <div class="col-md-12">
                                                                <span class="label label-danger">{{ __('Application rejected by moderator') }}.</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        function promotionSubmit(formId)
        {
            if(formId == 'working_student' &&
                ($('[name=work_certificate_file]').val() == '' ||
                    $('[name=pension_report_file]').val() == '')
            )
            {
                alert('{{ __('Need to add documents') }}.');
                return false;
            }
            $('#' + formId + '_form').submit();
        }
    </script>
@endsection