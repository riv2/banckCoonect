<?php
$hasOrderEdit = \App\Services\Auth::user()->hasRight('orders','edit');
?>

@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> {{ isset($order->id) ? 'Редактировать приказ' : 'Добавить приказ' }}</h2>

            <a href="{{ URL::to('/orders') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default" id="main-panel">
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Наименование</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="order_name_id" v-model="orderNameId" required @if(!$hasOrderEdit) disabled @endif>
                                <option value="">- Выбрать из списка -</option>
                                @foreach($orderNames as $orderName)
                                    <option value="{{ $orderName->id }}">{{ $orderName->name }} ({{ $orderName->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Форма обучения</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="study_form_id" v-model="studyFormNumber" required @if(!$hasOrderEdit) disabled @endif>
                                <option value="">- Выбрать из списка -</option>
                                <option value="1">Заочная и вечерняя</option>
                                <option value="2">Дистанционная</option>
                                <option value="3">ФЭПИТ очная бакалавриат</option>
                                <option value="4">Магистратура</option>
                                <option value="5">ФПИЯ очная бакалавриат</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Номер приказа</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" name="number" value="{{ $order->number ?? '' }}" v-model="orderNumber" required @if(!$hasOrderEdit) disabled @endif />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Дата приказа</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="date" name="date" value="{{ $order->date ?? '' }}" required @if(!$hasOrderEdit) disabled @endif />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">НПА приказа</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="npa" required v-model="npa" @if(!$hasOrderEdit) disabled @endif>
                                <option value="">- Выбрать из списка -</option>
                                <option>Правилами перевода и восстановления обучающихся по типам организации образования (Приказ Министра образования и науки Республики Казахстан от 20 января 2015 года № 19)</option>
                                <option>Типовыми правилами деятельности организаций образования соответствующих типов (Приказ Министра образования и науки Республики Казахстан от 30 октября 2018 года № 595)</option>
                                <option>В соответствии с Типовыми правилами приема в высшие учебные заведения Республики Казахстан, в части формирования студенческого контингента на 2019-2020 учебный год.</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="form-group">
                        <label for="" class="col-md-3 control-label">Студенты</label>
                        <div class="col-md-9">
                            @if(count($order->users))
                                <table id="main-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>ФИО</th>
                                        <th>ИИН</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($order->users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->studentProfile->fio ?? '' }}</td>
                                            <td>{{ $user->studentProfile->iin ?? '' }}</td>
                                            <td>
                                                <input type="checkbox" value="{{ $user->id }}" name='selectUserList' @if(!$hasOrderEdit) disabled @endif />
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @if($hasOrderEdit)
                                    <a class="btn btn-primary" v-on:click="detachUsers()">Исключить из приказа выбранных</a>
                                @endif
                            @else
                                Список пуст
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-md-3 control-label">Подписанты</label>
                        <div class="col-md-9">

                            <table id="main-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ФИО</th>
                                    <th>Статус</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($order->signatures as $signature)
                                    @if($signature->signed)
                                        <tr>
                                            <td>{{$signature->user->id}}</td>
                                            <td>{{$signature->user->name}}</td>
                                            <td>Подписано</td>
                                        </tr>

                                    @endif
                                @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Действие</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="order_action_id" v-model="order_action_id" required @if(!$hasOrderEdit) disabled @endif>
                                <option value="">- Выбрать из списка -</option>
                                @foreach($orderActions as $orderAction)
                                    <option value="{{ $orderAction->id }}">{{ $orderAction->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label"></label>
                        <div class="col-sm-4">
                            @if($hasOrderEdit)
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            @endif

                            @if($order->id)
                                <a class="btn btn-info" href="{{ route('adminPrintOrder', ['id' => $order->id]) }}">Скачать приказ</a>
                            @endif

                            @if(!$order->checkSignature(\App\Services\Auth::user()->id))
                                <a class="btn btn-info" v-on:click="addSignature()">Подписать ({{ \App\Services\Auth::user()->name }})</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

        let app = new Vue({
            el: '#main',
            data: {
                orderNameList: [],
                orderNameId: {!! $order->order_name_id ?? "''" !!},
                orderNumber: '{{ $order->number ?? '' }}',
                studyFormNumber: {!! $order->study_form_id ?? "''" !!},
                orderId: {{ \App\Order::idForNew() }},
                npa: '{{ $order->npa ?? '' }}',
                order_action_id: '{{ $order->order_action_id ?? '' }}'
            },
            created: function() {
                @foreach($orderNames as $orderName)
                    this.orderNameList[{{ $orderName->id }}] = {name: '{{ $orderName->name }}', code: '{{ $orderName->code }}' };
                @endforeach
            },
            methods: {
                generateNumber: function(){
                    if(!this.orderNameId || !this.studyFormNumber)
                    {
                        return '';
                    }

                    let today = new Date();
                    let year = today.getFullYear();

                    year = year.toString().substr(-2);

                    let orderId = '';

                    if(this.orderId >= 10) {
                        if(this.orderId < 100) {
                            orderId = '0' + this.orderId.toString();
                        } else {
                            orderId = this.orderId.toString();
                        }
                    } else {
                        orderId = '00' + this.orderId.toString();
                    }

                    this.orderNumber = this.orderNameList[this.orderNameId].code + '-' +
                        year + this.studyFormNumber.toString() + '/' + orderId;
                },

                getSelectedUser: function() {
                    let favorite = [];

                    $.each($("input[name='selectUserList']:checked"), function(){
                        favorite.push($(this).val());
                    });

                    return favorite;
                },

                addSignature: function() {
                    if(!confirm('Подписать приказ?')) {
                        return false;
                    }

                    axios.post('{{route('adminAddOrderSignature', ['id' => $order->id])}}', {})
                        .then(function(response){
                            location.reload();
                        });
                },

                @if($order->id)
                    detachUsers: function() {
                        if(!confirm('Исключить студента из приказа?')) {
                            return false;
                        }

                        var userList = this.getSelectedUser();
                        var self = this;

                        axios.post('{{route('adminOrderDetachUsers')}}', {
                            users: userList,
                            order_id: {{ $order->id }}
                        })
                            .then(function(response){
                                location.reload();
                            });
                    }
                @endif
            },
            watch: {
                orderNameId: function(newval){
                    this.generateNumber();
                },
                studyFormNumber: function(newval){
                    this.generateNumber();
                }
            }
        });
    </script>
@endsection