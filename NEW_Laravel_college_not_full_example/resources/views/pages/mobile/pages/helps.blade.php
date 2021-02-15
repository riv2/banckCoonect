@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="study-app">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{ __('Helps') }} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    @if (false /*Auth::user()->studentProfile->category == \App\Profiles::CATEGORY_TRANSIT && !$transitClassAttendanceBought*/)
                        <blockquote style="border-left: 5px solid #1b809e;" v-if="!transitClassAttendanceBought">
                            {{ __('The cost of full-time attendance of classes per semester')}} 10000 тенге <button @click="serviceBuyService({{\App\FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID}})" :disabled="serviceSendRequest" class="btn btn-info btn-sm" type="button">{{ __('Buy') }}</button>
                        </blockquote>
                    @endif

                    <br>
                    <div v-if="serviceMessage" :class="{ 'alert-danger': serviceError, 'alert-success': !serviceError }" class="alert">
                        <div v-html="serviceMessage"> </div>
                    </div>

                    <div class="row">
                    @if(!empty($nomenclature))

                        <div id="loader-layout" class="fade" style="position:absolute;width: 100%;height: 100%;background: rgba(255,255,255,0.5); text-align: center; display: none;">
                            <img style="opacity: 0.5; max-width: 100px;" src="{{ URL::to('assets/img/load.gif') }}" />
                        </div>

                        <div class="table-responsive col-12">
                            <table class="table font-size09">
                                <thead>
                                <tr>
                                    <th>{{ __('Nomination') }}</th>
                                    <th>{{ __('Cost') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($nomenclature as $nomenclatureItem)
                                    @if( !empty($nomenclatureItem->cost) )
                                        <tr>
                                            <td> {{ $nomenclatureItem->$locale ?? $nomenclatureItem->name }} </td>
                                            <td> {{ $nomenclatureItem->cost }} </td>
                                            <td>
                                                {{--@if( !empty(Auth::user()->studentProfile->category) && ( Auth::user()->studentProfile->category != \App\Profiles::CATEGORY_TRANSIT ) )--}}
                                                @if(Auth::user()->studentProfile->education_status == \App\Profiles::EDUCATION_STATUS_STUDENT)
                                                    <button @click="serviceBuyService({{ $nomenclatureItem->id }})" :disabled="serviceSendRequest" class="btn btn-info" type="button" @if(in_array($nomenclatureItem->id, $boughtServiceIds)) disabled="disabled" @endif>{{ __('Buy') }}</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script type="text/javascript">
        const TRANSIT_CLASS_ATTENDANCE_ID = {{\App\FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID}};

        var app = new Vue({
            el: "#study-app",
            data: {
                serviceUserid: false,
                serviceMessage: false,
                serviceError: false,
                serviceSendRequest: false,
                transitClassAttendanceBought: false
            },
            methods: {
                serviceBuyService: function(id){

                    var isConfirm = confirm('{{ __('Do you want to purchase a service?') }}');
                    if( !isConfirm ){
                        return;
                    }

                    this.serviceMessage = false;
                    this.serviceError = false;
                    this.serviceSendRequest = true;

                    var loader = $('#loader-layout');
                    $('html, body').animate({ scrollTop: $("#study-app").offset().top}, 500);
                    loader.removeClass('fade');

                    var self = this;
                    axios.post('{{ route('profileAjaxUserBuyService') }}',{
                        "_token": "{{ csrf_token() }}",
                        "service": id
                    })
                        .then(function( response ){
                            if( response.data.status ){
                                if (id == TRANSIT_CLASS_ATTENDANCE_ID) {
                                    self.transitClassAttendanceBought = true;
                                }
                            } else {
                                self.serviceError = true;
                                console.log( response.data );
                            }

                            self.serviceMessage = response.data.message;
                            self.serviceSendRequest = false;

                        });

                    loader.addClass('fade');
                    $('html, body').animate({
                        scrollTop: $("#study-app").offset().top
                    }, 500);

                }
            },
            created: function(){
                this.serviceUserid = '{{ $user }}';
            }
        });

    </script>
@endsection
