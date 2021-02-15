@extends('layouts.app')

@section('content')

    <section class="content" id="notifications_news">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{ __("Dean's Office") }} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <ul class="nav nav-tabs">
                        <li class="nav-item @if(!Session::has('flash_message_callback')) active @endif">
                                    <span class="nav-link active" data-toggle="tab" onclick="activeTab = 'notifications'" href="#notifications"> <span @click="setNotificationsCount"> {{ __("Notifications") }} (@{{ notificationsCount }}) </span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" onclick="activeTab = 'news'" href="#news"> <span @click="setNewsCount"> {{ __("Ads") }} (@{{ newsCount }}) </span></a>
                        </li>
                        <li class="nav-item @if(Session::has('flash_message_callback')) active @endif">
                            <a class="nav-link" data-toggle="tab" href="#callback"> {{ __("Feedback") }} </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#academic_calendar"> {{ __("Academic calendar") }} </a>
                        </li>
                    </ul>

                    <div class="tab-content margin-t20 row">

                        <div class="tab-pane col-12 padding-t10 @if(!Session::has('flash_message_callback')) active @endif" id="notifications"></div>

                        <div class="tab-pane col-12 padding-t10" id="news"></div>

                        <div class="tab-pane col-12 padding-t10 @if(Session::has('flash_message_callback')) active @endif" id="callback">
                            <div class="col-12">
                                @if(Session::has('flash_message_callback'))
                                    <div class="alert alert-success">
                                        {{ Session::get('flash_message_callback') }}
                                    </div>
                                @else
                                    @if( empty($profile->mobile) )
                                        <h4>{{__('If you need a consultation, leave your phone number here and we will contact you')}}</h4>
                                    @else
                                        <h4> {{ __("If you need advice, click on the 'call back' button and we will call you back") }} {{$profile->mobile}} </h4>
                                    @endif

                                    {!! Form::open(array('url' => array( route('callBack') ),'class'=>'form-horizontal padding-15','method'=>'POST')) !!}

                                    <div class="form-group row">

                                        @if( empty($profile->mobile) )
                                            <label for="number" class="col-sm-2 control-label"> {{__('Phone number')}} </label>
                                            <div class="col-sm-5">
                                                {{Form::text('number', '', ['class' => 'grey form-control', 'id' => 'number'])}}
                                            </div>
                                        @else
                                            {{Form::hidden('number', $profile->mobile)}}
                                        @endif

                                        <div class="col-sm-1">
                                            <button id="usedSubmit" type="submit" class="btn btn-info btn-lg">{{__('callback')}}</button>
                                        </div>
                                    </div>

                                    {!! Form::close() !!}
                                @endif

                            </div>
                        </div>

                        <div class="tab-pane col-12 padding-t10" id="academic_calendar">
                            @foreach($calendars as $name => $calendar)
                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-4 col-form-label">{{ $name }}</label>
                                    <div class="col-sm-8">
                                        <a 
                                            href="{{ route('deansoffice.download.calendar', ['type' => $calendar]) }}" 
                                            class="btn btn-primary"
                                        >
                                            Скачать
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection
@section('scripts')
    <script type="text/javascript">

        var app = new Vue({
            el: '#notifications_news',
            data: {
                notificationsCount: 0,
                newsCount: 0
            },
            methods: {

                getNewsNotificationsCount: function(){

                    var self = this;
                    axios.post('{{ route('deansGetCount') }}',{
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.notificationsCount = response.data.countNotification;
                                self.newsCount = response.data.countNews;
                            }

                        })
                        .catch( error => {

                        console.log(error);
                });
                },
                setNotificationsCount: function(){

                    var self = this;
                    axios.post('{{ route('deansSetNotificationCount') }}',{
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.getNewsNotificationsCount();
                            }

                        })
                        .catch( error => {

                        console.log(error);
                });
                },
                setNewsCount: function(){

                    var self = this;
                    axios.post('{{ route('deansSetNewsCount') }}',{
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.getNewsNotificationsCount();
                            }

                        })
                        .catch( error => {

                        console.log(error);
                });
                }

            },
            created: function(){

                this.getNewsNotificationsCount();
            }
        });

        var notificationPage = 1;
        var newsPage = 1;
        var activeTab = 'notifications';

        function loadNotifications() {
            $.ajax({
                url: '{{ route('deansofficeNotificationsList') }}',
                type: 'post',
                data: {
                    page: notificationPage,
                    count: 10,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data){
                    for(var i=0; i<data.length; i++)
                    {
                        $('#notifications').append('<div class="alert alert-warning">' + data[i].text + '</div>');
                        notificationPage++
                    }
                }
            });
        }

        function loadNews() {
            $.ajax({
                url: '{{ route('deansofficeNewsList') }}',
                type: 'post',
                data: {
                    page: newsPage,
                    count: 10,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data){
                    for(var i=0; i<data.length; i++)
                    {
                        $('#news').append('                     <div class="panel panel-warning" id="accordion' + data[i].id + '">\n' +
                            '                                        <div class="panel-heading" role="tab" id="heading' + data[i].id + '">\n' +
                            '                                            <h4 class="panel-title">\n' +
                            '                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion' + data[i].id + '" href="#collapse' + data[i].id + '" aria-expanded="false" aria-controls="collapse' + data[i].id + '">\n' +
                            '                                                    ' + data[i].title + '\n' +
                            '                                                </a>\n' +
                            '                                            </h4>\n' +
                            '                                        </div>\n' +
                            '                                        <div id="collapse' + data[i].id + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading' + data[i].id + '">\n' +
                            '                                            <div class="panel-body">\n' +
                            '                                                ' + data[i].text + '\n' +
                            '                                            </div>\n' +
                            '                                        </div>\n' +
                            '                                    </div>');
                        newsPage++
                    }
                }
            });
        }

        window.onscroll = function(){
            var doc = document.documentElement;

            if(doc.scrollTop == doc.scrollHeight - doc.clientHeight) {
                if(activeTab == 'notifications') {
                    loadNotifications();
                }

                if(activeTab == 'news') {
                    loadNews();
                }
            }
        }

        loadNotifications();
        loadNews();

    </script>
@endsection