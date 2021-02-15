@extends('layouts.app')

@section('title', __('Payment for working off'))

@section('content')

    <section class="content" id="coursework-pay">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Payment for working off')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">


                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            {{-- course не оплачено --}}
                            @if( !$bIsPayedCourse && $bUserHasNotTaskPoints )

                                <div class="alert alert-warning" role="alert">
                                    @lang('Payment for working 4000')
                                </div>

                                <form id="sendPayCourse" class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('sroCourseworkPayPost') }}">
                                    {{ csrf_field() }}

                                    <input type="hidden" name="discipline_id" value="{{ $discipline_id }}" />

                                    <div class="form-group">
                                        <label for="front" class="col-md-4 control-label">{{__('Charge amount')}}</label>
                                        <div class="col-md-6">
                                            <input class="form-control" type="text" readonly value="{{ $cost }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <a class="btn btn-info" href="{{ route('sroGetList',['discipline_id'=>$discipline_id,'course'=>1]) }}"> {{__("Back")}} </a>
                                            &nbsp;
                                            <button @click="taskPay" type="button" class="btn btn-primary">
                                                {{__("To pay")}}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                            @else

                                <p> @lang('Your application has been accepted for consideration. Wait for a response within 24 hours.') </p>

                                <a class="btn btn-info" href="{{ route('study') }}"> {{__("Back")}} </a>

                            @endif

                        </div>
                        <div class="col-md-2"></div>
                    </div>


                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script type="text/javascript">

        var taskApp = new Vue({
            el: '#coursework-pay',
            data: {},
            methods: {

                taskPay: function(){

                    if (!confirm('@lang('Confirm the withdrawal')')) {
                        return;
                    }

                    $("#sendPayCourse").submit();

                }

            }
        });

    </script>
@endsection