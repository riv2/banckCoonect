@extends('layouts.app')

@section('title', __('Study'))

@section('content')

    <style>
        .accordion > .card {
            overflow: visible;
        }
    </style>

    <section class="content">
        <div class="container-fluid" id="study-app">
            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin">@lang('Study')</h2></div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            @if (false /*Auth::user()->studentProfile->category == \App\Profiles::CATEGORY_TRANSIT && !$transitClassAttendanceBought*/)
                                <h4 v-if="!transitClassAttendanceBought">
                                    @lang('The cost of full-time attendance of classes per semester') 10000 тенге <button @click="serviceBuyService({{\App\FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID}})" :disabled="serviceSendRequest" class="btn btn-info btn-sm" type="button">@lang('Buy')</button>
                                </h4>
                            @endif

                            <h4>@lang('If you applied for a discount, please wait for confirmation from the University and recalculation of the cost of 1 credit. Otherwise, the cost of 1 credit will be displayed without a discount. Refunds on purchased credits are not provided')</h4>

                            @if(Auth::user()->studentProfile->status == \App\Profiles::STATUS_ACTIVE)
                                @if( count(Auth::user()->disciplines) > 0 )
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#list" data-toggle="tab">@lang("Discipline list")</a>
                                        </li>                                      
                                    </ul>
                                @endif

                                <div class="tab-content padding-b20">
                                    <div class="tab-pane active margin-t10" id="list">
                                        @include('student.study.disciplineTab')
                                    </div>

                                    <div class="tab-pane" id="addition">
                                        <br>
                                        <div v-if="serviceMessage" :class="{ 'alert-danger': serviceError, 'alert-success': !serviceError }" class="alert">
                                            <div v-html="serviceMessage"> </div>
                                        </div>

                                        <br>
                                        @if(!empty($nomenclature))
                                            <div class="table-responsive">
                                                <div id="loader-layout" style="position:absolute;width: 100%;height: 100%;background: rgba(255,255,255,0.5); text-align: center; display: none;">
                                                    <img style="opacity: 0.5; max-width: 100px;" src="{{ URL::to('assets/img/load.gif') }}" />
                                                </div>
                                                <table class="table table-sm table-font-size09">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ __('Nomination') }}</th>
                                                        <th>{{ __('Cost') }}</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($nomenclature as $nomenclatureItem)
                                                        @if(
                                                            $nomenclatureItem->type == \App\FinanceNomenclature::TYPE_FEE &&
                                                            !empty($nomenclatureItem->cost)
                                                        )
                                                            <tr>
                                                                <td> {{ $nomenclatureItem->$locale ?? $nomenclatureItem->name }} </td>
                                                                <td> {{ $nomenclatureItem->cost }} </td>
                                                                <td>
                                                                    <button @click="serviceBuyService({{ $nomenclatureItem->id }})" :disabled="serviceSendRequest" class="btn btn-info" type="button" @if(in_array($nomenclatureItem->id, $boughtServiceIds)) disabled="disabled" @endif>{{ __('Buy') }}</button> </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            @endif
                            @if(Auth::user()->studentProfile->status == \App\Profiles::STATUS_MODERATION)
                                <div class="alert alert-warning">
                                    {{ __('Data successfully saved and sent for processing.') }}<br>
                                    {{ __('You have chosen training') }}&nbsp;{{ \App\Services\Auth::user()->studentProfile->speciality->$locale }}.
                                </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.min.js"></script>
    <script src="https://unpkg.com/vue-simple-context-menu"></script>

    <script type="text/javascript">
        const TRANSIT_CLASS_ATTENDANCE_ID = {{\App\FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID}};

        var app = new Vue({
            el: "#study-app",
            data: {
                serviceUserid: false,
                serviceMessage: false,
                serviceError: false,
                serviceSendRequest: false,
                transitClassAttendanceBought: false,
                confirming: false
            },
            methods: {
                serviceBuyService: function (id) {
                    if (!confirm('@lang('Do you want to purchase a service?')')) {
                        return;
                    }

                    this.serviceMessage = false;
                    this.serviceError = false;
                    this.serviceSendRequest = true;
                    var loader = $('#loader-layout');
                    $('html, body').animate({scrollTop: $("#addition").offset().top}, 500);
                    loader.show();

                    var self = this;
                    axios.post('{{ route('profileAjaxUserBuyService') }}', {
                        "_token": "{{ csrf_token() }}",
                        "service": id
                    })
                        .then(function (response) {
                            if (response.data.status) {
                                if (id == TRANSIT_CLASS_ATTENDANCE_ID) {
                                    self.transitClassAttendanceBought = true;
                                    $('.nav-tabs a[href="#addition"]').tab('show');
                                }
                            } else {
                                self.serviceError = true;
                                console.log(response.data);
                            }

                            self.serviceMessage = response.data.message;
                            self.serviceSendRequest = false;

                            $('html, body').animate({scrollTop: $("#addition").offset().top}, 500);
                            loader.hide();
                        });
                },
                cancelPayment: function(disciplineId) {
                    if(!confirm('@lang('Cancel payment')?'))
                    {
                        return false;
                    }

                    axios.post('{{ route('studentDisciplinePayCancel') }}', {
                        "_token": "{{ csrf_token() }}",
                        "discipline_id": disciplineId
                    })
                        .then(function (response) {
                            if (response.data.status) {
                                $('#discipline_cancel_sent_' + disciplineId).css('display','block');
                                $('#discipline_cancel_button_' + disciplineId).css('display','none');
                            } else {
                                alert(response.data.message);
                                console.log(response.data);
                            }
                        });
                },
                confirmStudyPlan: function() {
                    var self = this;

                    self.confirming = true;

                    bootbox.confirm('@lang('Do you confirm the study plan?')', function(result) {
                        if (result === false) {
                            self.confirming = false;
                            return;
                        }

                        window.location.href = '{{route('studentConfirmStudyPlan')}}';
                    });
                },
                notConfirmStudyPlan: function() {
                    var self = this;

                    self.confirming = true;

                    bootbox.prompt({
                        title: "@lang('Please write in the reason')",
                        required: true,
                        placeholder: '@lang('Reason')...',
                        callback: function(reason) {
                            if (reason === null) {
                                self.confirming = false;
                                return;
                            }

                            axios.post('{{route('studentNotConfirmStudyPlan')}}', {
                                "reason": reason
                            })
                                .then(function (response) {
                                    if (response.data.success) {
                                        bootbox.alert('@lang('Message sent')')
                                    } else {
                                        alert(response.data.error);
                                        self.confirming = false;
                                    }
                                });
                        }
                    });
                }
            },
            created: function () {
                this.serviceUserid = '{{Auth::user()->id}}';
            }
        });

        window.onload = function () {
            //open specific tab on page
            var url = document.location.toString();
            if (url.match('#')) {
                $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
            }

            //Change hash for page-reload
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').on('shown', function (e) {
                window.location.hash = e.target.hash;
            });
        }
    </script>
@endsection
