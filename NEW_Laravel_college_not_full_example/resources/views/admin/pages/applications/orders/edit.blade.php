@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> Заявление на отчисление</h2>

            <a
                    href="{{ route('adminApplicationList', ['type' => $type]) }}"
                    class="btn btn-default-light btn-xs">
                <i class="md md-backspace"></i> Назад
            </a>

        </div>

        <div class="alert"
             v-bind:class="{
             'alert-success': appStatus=='{{ \App\UserApplication::STATUS_CONFIRM }}',
             'alert-warning': appStatus=='{{ \App\UserApplication::STATUS_DECLINE }}'}"
             v-show="appStatus != '{{ \App\UserApplication::STATUS_MODERATION }}'">
            <span v-if="appStatus == '{{ \App\UserApplication::STATUS_CONFIRM }}'">Заявление добавлено в приказ</span>
            <span v-if="appStatus == '{{ \App\UserApplication::STATUS_DECLINE }}'">Заявление отправлено на доработку</span>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                {!! Form::open([
                    'url' => [route('adminApplicationEdit', ['type' => $type , 'id' => $application->id]) ],
                    'class'=>'form-horizontal padding-15',
                    'name'=>'application_form',
                    'id'=>'application_form',
                    'role'=>'form',
                    'enctype' => 'multipart/form-data']) !!}

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"> ФИО </label>
                    <div class="col-sm-8"> {{ $application->studentProfile->fio }} </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"> ИИН </label>
                    <div class="col-sm-8"> {{ $application->studentProfile->iin }} </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"> Специальность </label>
                    <div class="col-sm-8"> {{ $application->studentProfile->speciality->name }} </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"> Курс </label>
                    <div class="col-sm-8"> {{ $application->studentProfile->course }} </div>
                </div>
                
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"> Заявление </label>
                    <div class="col-sm-8" style="max-height: 150px; overflow-y: auto;">
                        <a href="{{ $application->file_src }}" target="_blank">
                            <img src="{{ $application->file_src }}" style="max-width: 100%" />
                        </a>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"> Комментарий </label>
                    {{--
                    <div class="col-sm-3" id="comment-block">
                        <textarea disabled rows="10" style="width: 100%;">{{ $application->comment }}</textarea>
                    </div>
                    --}}
                    <div id="comment-block" class="col-sm-8" style="max-height: 500px; overflow-y: auto;"></div>
                    <div class="col-md-offset-3 col-sm-8">
                        <textarea  style="margin-bottom: 15px;" name="comment" v-model='comment' class="form-control" spellcheck="false"></textarea>
                        <input type="checkbox" name="for-student" id="for-student" v-model="forStudent">
                        <label for="for-student"> показать студенту</label>
                        <div class="btn btn-primary" v-on:click='commentAdd' v-bind:class="{disabled:comment==''}">Добавить коментарий</div>
                        <div class="btn btn-success" v-if="!currentUserSign" v-on:click='confirmApp'>Подтвердить</div>
                        <div class="btn btn-danger" v-if="!currentUserSign" v-on:click='declineApp' v-bind:class="{disabled:comment==''}">Отправить на доработку</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"> Подписи </label>
                    <div class="col-md-offset-3 col-sm-8" id="signs">
                        
                        <div class="alert-info"></div>
                        
                    </div>

                </div>

                {{--<div class="form-group" v-if="appStatus == '{{ \App\UserApplication::STATUS_MODERATION }}'">--}}
                <div class="form-group">
                    <hr>
                    <div class="col-md-offset-3 col-sm-9">
                        {{--
                        <div v-show="allSigned">
                            <a class="btn btn-primary" v-on:click="showConfirmForm = true">Добавить в приказ</a>
                            
                            <a class="btn btn-primary" v-on:click="showDeclineForm = true">Отправить на доработку</a>
                        </div>
                        --}}
                        <div v-show="allSigned" class="col-md-8 no-padding">
                            <div class="col-md-12 form-group">
                                <select class="form-control selectpicker" data-live-search="true" id="orderId" v-model="orderId">
                                    <option value="0">Выберите приказ</option>
                                    @foreach($orderList as $order)
                                    <option value="{{ $order->id }}">
                                        {{ $order->orderName->name . ' (' . $order->number . ')' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <a class="btn btn-primary"
                                   v-bind:class="{disabled:orderId==0}"
                                   v-on:click="setOrder()">
                                    Добавить в приказ
                                </a>
                                {{--<a class="btn btn-default" v-on:click="showConfirmForm=false" >Отмена</a>--}}
                            </div>
                        </div>

                        <div v-show="showDeclineForm" class="col-md-6 no-padding hidden">
                            <div class="col-md-12 form-group">
                                <textarea v-model="comment" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12 form-group">
                                <a class="btn btn-primary"
                                   v-bind:class="{disabled:!comment}"
                                   v-on:click="declineApp()">
                                    Отправить на доработку
                                </a>
                                <a class="btn btn-default" v-on:click="showDeclineForm=false" >Отмена</a>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script type="text/javascript">

    var app = new Vue({
        el: '#main',
        data: {
            showConfirmForm: false,
            showDeclineForm: false,
            orderId: 0,
            comment: '',
            appId: {{ $id }},
            appStatus: '{{ $application->status }}',
            forStudent: null,
            allSigned: false,
            currentUserSign: false,
        },
        methods: {
            setOrder: function(){
                if(!this.orderId){
                    return;
                }

                var self = this;

                axios.post('{{route('adminApplicationAjaxSetOrder', ['type' => $type])}}', {
                    request_id: this.appId,
                    order_id: this.orderId
                })
                    .then(function(response){
                        if(response.data.status == true){
                            self.getSignList();
                        }
                    });
            },
            confirmApp: function(){
                var self = this;

                axios.post('{{route('adminApplicationAjaxConfirm', ['type' => $type])}}', {
                    request_id: this.appId
                })
                    .then(function(response){
                        if(response.data.status == true){
                            self.getSignList();
                        }
                    });
                self.currentUserSign = true;
            },
            declineApp: function(){
                if(!this.comment){
                    return;
                }
                this.commentAdd();

                var self = this;

                axios.post('{{route('adminApplicationAjaxDecline', ['type' => $type])}}', {
                    request_id: this.appId
                })
                    .then(function(response){
                        if(response.data.status == true){
                            self.getSignList();
                            
                        }
                    });
                self.currentUserSign = true;
            },
            commentList: function(){
                
                var self = this;

                axios.post('{{route('adminApplicationAjaxCommentList', ['type' => $type])}}', {
                    request_id: this.appId
                })
                    .then(function(response){
                        if(response.data.status == true){
                            $("#comment-block").html('');
                            response.data.list.forEach(function(item){
                                var for_student_text = '';
                                var alert_status = 'warning';
                                if(item.for_student != null) {
                                   for_student_text = '<sub> - Показано студенту </sub>'; 
                                   alert_status = 'success';
                                }
                                var commment = '\
                                    <div class="alert alert-' + alert_status + '">\
                                        ' + item.text + '\
                                        <p>\
                                            <sub> - ' + item.name + '</sub>\
                                            <sub> - ' + item.created_at + '</sub>\
                                            '+ for_student_text +'\
                                        </p>\
                                    </div>';
                            $("#comment-block").append(commment);
                            });
                            
                        }
                    });
            },
            commentAdd: function(){
                
                var self = this;
                axios.post('{{route('adminApplicationAjaxCommentAdd', ['type' => $type])}}', {
                    request_id: this.appId,
                    text: self.comment,
                    forStudent: self.forStudent
                })
                    .then(function(response){
                        if(response.data.status == true){
                            self.commentList();
                            self.comment = null;
                        }
                    });
            },
            getSignList: function(){
                var self = this;
                axios.post('{{route('adminApplicationAjaxGetSignList', ['type' => $type])}}', {
                    request_id: this.appId
                })
                    .then(function(response){
                        if(response.data.status == true){
                            $('#signs').html('');
                            response.data.list.forEach(function(item){
                                var signed = '';
                                if(item.signed !== undefined && item.signed) {
                                    signed = item.name +' '+ item.position +' ✅';
                                }
                                var sign = '<div class="alert-info">'+ item.position +' '+ signed +'</div>';
                                $('#signs').append(sign);
                            });

                            self.allSigned = response.data.allSigned;
                            self.currentUserSign = response.data.currentUserSign;

                            // check if all signs we have
                            //self.showConfirmForm = true;
                            //self.showDeclineForm = true;

                        }
                    });
            }
            
        },
        mounted: function(){
            this.commentList();
            this.getSignList();
        }
    });


    $('select.selectpicker#orderId').on('change', function(){
        app.orderId = this.value;
    });

</script>
@endsection