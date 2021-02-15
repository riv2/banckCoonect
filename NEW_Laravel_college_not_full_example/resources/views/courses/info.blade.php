@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="courses-app">

            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">
                    {{__('Course settings')}}  {{ $course->title ?? '' }}
                </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <div id="error-block" class="alert alert-danger fade" role="alert"></div>

                            <form id="courses_info" name="courses_info" method="POST" action="{{ route('courseInfoPost') }}">
                            {{ csrf_field() }}

                                <input type="hidden" name="courses_id" value="{{ $course->id }}" />

                                <!-- LANGUAGE -->
                                <div class="form-group{{ $errors->has('language') ? ' has-error' : '' }}">
                                    <label for="language" class="col-md-3 control-label">{{__('Language')}}</label>
                                    <div class="col-md-10">

                                        <select id="language" class="form-control" name="language">
                                        @foreach( $language as $itemL )
                                                <option value="{{ $itemL }}"> {{ __($itemL) }} </option>
                                        @endforeach
                                        </select>

                                        @if ($errors->has('language'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('language') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- SCHEDULE -->
                                @if( $course->schedule )
                                <div class="form-group">
                                    <label for="schedule" class="col-md-3 control-label">{{__('Schedule')}}</label>
                                    <div class="col-md-10">
                                        <p>  </p>
                                        <textarea id="schedule" class="form-control" row="4" readonly> {{ isset($course->schedule) ? $course->schedule : '' }} </textarea>
                                    </div>
                                </div>
                                @endif

                                <!-- COST -->
                                <div class="form-group">
                                    <label for="cost" class="col-md-3 control-label">{{__('Cost')}}</label>
                                    <div class="col-md-10">
                                        <input id="cost" class="form-control" type="text" name="cost" value="{{ $course->cost ?? 0 }}" readonly />
                                    </div>
                                </div>

                                <!-- PAY METHOD -->
                                <div class="form-group">
                                    <label for="paymethod" class="col-md-3 control-label">{{__('Payment method')}}</label>
                                    <div class="col-md-10">
                                        <template v-if="coursesChoosePaymethod">
                                            <input v-model="coursesPaymentMethod( coursesChoosePaymethod )" class="form-control margin-b10" type="text" readonly />
                                        </template>
                                    </div>
                                </div>

                                <!-- Button PAY -->
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <button class="btn btn-info" type="button" data-toggle="modal" data-target="#coursesModal"> {{ __('Choose payment method') }} </button>
                                    </div>
                                </div>

                                <!-- modal -->
                                <div id="coursesModal" class="modal" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"> {{ __('Choose payment method') }} </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">


                                                <div class="form-group row">
                                                    <label for="paymethod" class="col-12 control-label">{{__('Payment method')}}</label>
                                                    <div class="col-12">

                                                        <select id="pay_method" v-model="coursesChoosePaymethod" class="form-control" name="pay_method">
                                                            <option>...</option>
                                                            <option value="{{ \App\CourseStudent::PAYMENT_METHOD_BALANCE }}" > {{ __('From balance') }} </option>
                                                        </select>

                                                    </div>
                                                </div>


                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-info" data-dismiss="modal"> {{ __('Close') }} </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="margin-b15"> &nbsp; </div>

                                <button id="submit" class="btn btn-info" type="submit"> {{ __('To pay') }} </button>


                            </form>




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

        var app = new Vue({
            el: "#courses-app",
            data: {
                coursesPaymethod: false,
                coursesChoosePaymethod: false,

                coursesPaymethodList: {
                    "{{ \App\CourseStudent::PAYMENT_METHOD_BALANCE }}": "{{ __('From balance') }}"
                }

            },
            methods: {

                coursesPaymentMethod: function(value){
                    return this.coursesPaymethodList[ value ];
                }
            },
            created: function(){

                this.coursesPaymethod = false;
                this.coursesChoosePaymethod = false;

            }
        });

        $('#submit').click(function() {

            var pay_method = $("#pay_method");
            var error = false;
            var errorList = [];
            $('#error-block').html('').addClass('fade');

            if ( !pay_method || (pay_method.val() == '' || pay_method.val() == null) ) {

                pay_method.addClass('error');
                error = true;
                errorList.push( "{{ __('Payment method is required') }}" );
            } else {

                pay_method.removeClass('error');
            }

            if (error) {
                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#app").offset().top
                }, 2000);

                $('#error-block').html( errorList.join('<br> * ') ).removeClass('fade');
                return false;
            }


            var isConfirm = confirm('{{ __('Do you want to purchase a service?') }}');
            if( !isConfirm ){
                return false;
            }

            return true;


        });


    </script>
@endsection