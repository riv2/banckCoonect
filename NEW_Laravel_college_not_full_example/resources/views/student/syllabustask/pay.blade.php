@extends('layouts.app')

@section('title', __('Pay SRO'))

@section('content')

    <section class="content" id="task-pay">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Pay SRO')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">


                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            {{-- SRO не оплачено --}}
                            @if( empty($taskPay) || empty($taskPay->payed) )

                                <form id="sendPaySRO" class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('sroTaskPayPost') }}">
                                    {{ csrf_field() }}

                                    <input type="hidden" name="task_id" value="{{ $task->id }}" />

                                    <div class="form-group">
                                        <label for="front" class="col-md-4 control-label">{{__('Charge amount')}}</label>
                                        <div class="col-md-6">
                                            <input class="form-control" type="text" readonly value="{{ $cost }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <a class="btn btn-info" href="{{ route('sroGetList',['discipline_id'=>$task->discipline_id]) }}"> {{__("Back")}} </a>
                                            &nbsp;
                                            <button @click="taskPay" type="button" class="btn btn-primary">
                                                {{__("To pay")}}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                            @else

                                <p> @lang('SRO already paid') </p>

                                <a class="btn btn-info" href="{{ route('sroGetList',['discipline_id'=>$task->discipline_id]) }}"> {{__("Back")}} </a>

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
            el: '#task-pay',
            data: {},
            methods: {

                taskPay: function(){

                    if (!confirm('@lang('Do you want to pay SRO?')')) {
                        return;
                    }

                    $("#sendPaySRO").submit();

                }

            }
        });

    </script>
@endsection